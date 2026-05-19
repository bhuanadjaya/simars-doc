# SIMARS-DOC — Feature Specification
> **For Claude Code:** Read this file together with `SIMARS_DOC_Tech_Stack.md`
> before building any feature. This file describes **behavior** — what the system
> must do, who can do it, and what the edge cases are.

---

## Feature List

| # | Feature | Area | Roles |
|---|---------|------|-------|
| F01 | Login & session management | Both | All |
| F02 | Upload new document | Admin | super_admin, admin_unit |
| F03 | Edit draft document metadata | Admin | super_admin, admin_unit* |
| F04 | Publish document (draft → active) | Admin | super_admin, admin_unit* |
| F05 | Set obsolete (active → obsolete) | Admin | super_admin, admin_unit* |
| F06 | Delete draft document | Admin | super_admin, admin_unit* |
| F07 | Document search & browse portal | Portal | All |
| F08 | View detail & download document | Portal | All (per access rules) |
| F09 | Automatic watermark on download | Both | — (automatic) |
| F10 | External regulation management | Admin | super_admin |
| F11 | User & unit management | Admin | super_admin |
| F12 | Dashboard & reports | Admin | super_admin, auditor |
| F13 | In-app notifications | Both | All |
| F14 | Obsolete archive & destruction | Admin | super_admin |

*admin_unit: own unit documents only

---

## F01 — Login & Session Management

**Actor:** All roles

**Flow:**
1. User visits `/login` (Laravel Breeze default)
2. Enter email + password
3. On success → update `last_login_at` in `users` table
4. Log to `activity_logs` (action: `login`)
5. Redirect based on role:
   - `super_admin`, `admin_unit`, `auditor` → `/admin`
   - `user`, `guest` → `/portal/documents`

**Logout:**
- Log to `activity_logs` (action: `logout`)
- Clear session → redirect to `/login`

**Edge cases:**
- `is_active = false` → reject login, show "Your account has been deactivated"
- Soft-deleted user → reject login

---

## F02 — Upload New Document

**Actor:** `super_admin`, `admin_unit`
**Route:** `GET /admin/documents/create` → `POST /admin/documents`
**Controller:** `Admin\DocumentController@create` / `@store`
**Form Request:** `StoreDocumentRequest`

**Form fields:**

| Field | Type | Rules |
|-------|------|-------|
| `number` | text input | Required. Free format. Duplicates allowed. |
| `title` | text input | Required, max 255 |
| `document_type_id` | select | Required, from active `document_types` |
| `owner_unit_id` | select | Required. admin_unit: auto-filled with own unit (disabled). super_admin: free choice. |
| `source` | select | Required: `internal` / `external` |
| `effective_date` | date picker | Optional |
| `description` | textarea | Optional |
| `tags` | text input | Optional, comma-separated |
| PDF file | file upload | Required. `.pdf` only. Max 20MB. |
| DOCX file | file upload | Optional. `.docx` only. Max 20MB. |

**Process after submit:**
1. Validate via `StoreDocumentRequest`
2. Save metadata to `documents` with `status = draft`
3. Save file(s) to `storage/app/documents/{unit_code}/{year}/{uuid}/`
4. Save record(s) to `document_files`
5. Log to `activity_logs` (action: `create_document`)
6. Redirect to document show page with success flash message

**admin_unit rule:**
- `owner_unit_id` is auto-filled with the logged-in user's `unit_id`
- The field is rendered as disabled/readonly in the form

**Edge cases:**
- File upload fails → rollback `documents` record, show error
- File is not PDF/DOCX → reject with validation error
- File exceeds 20MB → reject with size error message

---

## F03 — Edit Draft Document

**Actor:** `super_admin`, `admin_unit` (own unit only)
**Route:** `GET /admin/documents/{id}/edit` → `PUT /admin/documents/{id}`
**Controller:** `Admin\DocumentController@edit` / `@update`
**Form Request:** `UpdateDocumentRequest`
**Policy:** `DocumentPolicy@update`

**Rules:**
- Only documents with `status = draft` can be edited
- `active` and `obsolete` documents cannot be edited
- PDF/DOCX files can be replaced while still draft
- `owner_unit_id` cannot be changed after creation

**Edge cases:**
- admin_unit tries to edit another unit's document → 403 via Policy
- Document is not draft → redirect back with error "Active documents cannot be edited"

---

## F04 — Publish Document (draft → active)

**Actor:** `super_admin`, `admin_unit` (own unit only)
**Route:** `PATCH /admin/documents/{id}/publish`
**Controller:** `Admin\DocumentController@publish`
**Policy:** `DocumentPolicy@publish`

**Process:**
1. Check `status = draft` — abort if not
2. Check PDF file exists in `document_files` — abort if missing
3. Update `documents`:
   - `status` → `active`
   - `published_at` → `today()`
4. Send notifications to all users in `owner_unit_id` (type: `new_document`)
   via Laravel Queue
5. Log to `activity_logs` (action: `publish_document`)
6. Redirect to document show page with success message

**Edge cases:**
- No PDF file uploaded → block publish, show "Please upload a PDF file first"
- Document already `active` → redirect with error

---

## F05 — Set Obsolete (active → obsolete)

**Actor:**
- `super_admin` → all documents
- `admin_unit` → only documents where `owner_unit_id = user.unit_id`

**Route:** `PATCH /admin/documents/{id}/obsolete`
**Controller:** `Admin\DocumentController@obsolete`
**Form Request:** `ObsoleteDocumentRequest`
**Policy:** `DocumentPolicy@obsolete`

**The request must include:**
- `obsolete_reason` → **Required**, text
- `replaced_by_id` → Optional. UUID of the new active document replacing this one.

**Process:**
1. Authorize via `DocumentPolicy@obsolete`
2. Validate `ObsoleteDocumentRequest`
3. Update `documents`:
   - `status` → `obsolete`
   - `obsolete_date` → `today()`
   - `obsolete_reason` → from request
   - `obsoleted_by` → `auth()->id()`
   - `replaced_by_id` → from request (nullable)
4. Send notifications to users who previously downloaded this document
   (type: `document_obsolete`) via Queue
5. Log to `activity_logs` (action: `set_obsolete`)
6. Redirect to document show with success message

**Implementation note:**
The obsolete confirmation should use a jQuery modal (not a new page).
The modal contains a form with `method="POST"` + `@method('PATCH')`,
`obsolete_reason` textarea (required), and a select for `replaced_by_id` (optional).

**Edge cases:**
- `obsolete_reason` empty → block, show validation error
- Document already `obsolete` → hide the button in UI
- admin_unit on another unit's document → 403 via Policy

---

## F06 — Delete Draft Document

**Actor:** `super_admin`, `admin_unit` (own unit only)
**Route:** `DELETE /admin/documents/{id}`
**Controller:** `Admin\DocumentController@destroy`
**Policy:** `DocumentPolicy@delete`

**Rules:**
- Only `draft` documents can be deleted
- `active` and `obsolete` documents cannot be deleted
- Deletion = soft delete (`deleted_at` is filled)
- Physical files in storage are NOT deleted on soft delete

**Process:**
1. Authorize via Policy
2. Check `status = draft` — abort if not
3. Soft delete the `documents` record
4. Log to `activity_logs` (action: `delete_document`)

---

## F07 — Document Search & Browse (Portal)

**Actor:** All roles
**Route:** `GET /portal/documents`
**Controller:** `Portal\DocumentController@index`

**IMPORTANT:** All queries **always** filter `status = active`.
Draft and obsolete documents never appear in the portal.
No per-unit or per-document access restriction — all active documents
are visible to every authenticated user.

**Search inputs (GET parameters):**
- `q` → full-text search on `title`, `description`, `tags`
- `type` → filter by `document_type_id`
- `unit` → filter by `owner_unit_id`
- `year` → filter by year of `effective_date`
- `sort` → `latest` (default), `title_asc`, `type`

**Browse tabs:**
- Internal Regulations (SK, SPO, PERDIRUT, PANDUAN, etc.)
- External Regulations (separate from main documents)
- By Unit

**Search results display:**
- Document number
- Title
- Document type
- Owner unit
- Effective date
- Active badge

**Pagination:** 20 documents per page

---

## F08 — View Detail & Download Document

**Actor:** All authenticated users
**Routes:**
- `GET /portal/documents/{id}` → show detail (any authenticated user)
- `GET /portal/documents/{id}/download` → trigger download with watermark

**Detail page shows:**
- All document metadata
- Inline PDF viewer using `<iframe>` embed
- Download PDF button
- If `parent_document_id` is set → show link "Previous version: [title]"
- If this document has a `replaced_by_id` (meaning it is obsolete and replaced) →
  show banner: ⚠️ "This document has been replaced by: [new document title]"
- If this document's ID appears as `replaced_by_id` on another doc →
  show info: "This document replaces: [old document title]"

**On view:**
- Log to `activity_logs` (action: `view_document`)

**On download:**
- Apply watermark (see F09)
- Log to `activity_logs` (action: `download_document`)

---

## F09 — Automatic Watermark on Download

**Trigger:** User clicks download in portal or admin area
**Service:** `WatermarkService`

**Process (server-side, on-the-fly):**
1. Retrieve original PDF from storage
2. Inject watermark text on every page:
   ```
   CONTROLLED DOCUMENT
   [user full name] — [download date dd/mm/yyyy HH:mm]
   ```
   - Position: diagonal, centered on page
   - Opacity: 20–30%, gray color
3. Stream the watermarked file directly to browser
4. **Do NOT save** the watermarked file to disk
5. Download filename: `[number]_[title].pdf`

**For auditors downloading obsolete documents (admin area):**
```
OBSOLETE DOCUMENT — NO LONGER VALID
[user full name] — [download date]
```

**Library:** Use `setasign/fpdi` + `setasign/fpdf` for injecting watermark into existing PDFs.

**Edge cases:**
- File not found in storage → show error: "File unavailable, please contact Document Controller"
- Encrypted/protected PDF → log error, show same message

---

## F10 — External Regulation Management

**Actor:** `super_admin`
**Routes:** `GET|POST /admin/external-regulations` + CRUD
**Controller:** `Admin\ExternalRegulationController`

**Form fields:**

| Field | Rules |
|-------|-------|
| `regulation_number` | Required |
| `title` | Required |
| `issuing_agency` | Required |
| `category` | Required, from enum |
| `issued_date` | Required |
| `effective_date` | Required |
| `affected_unit_ids` | Optional, multi-select from units |
| PDF file | Required |

**Process:**
- Status is immediately `active` on save — no draft state
- Save file to `storage/app/regulations/{year}/`
- Send notifications to users in selected units (type: `new_regulation`) via Queue
- Log to `activity_logs` (action: `upload_regulation`)
- No distribution record needed — document is immediately visible to all users

---

## F11 — User & Unit Management

**Actor:** `super_admin`

**User management** (`/admin/users`):
- List with filters: unit, role, active/inactive
- Create: name, employee_id (NIP), email, unit, role, password
- Edit: all fields except password
- Reset password: generate new password, show once to admin
- Deactivate: set `is_active = false` (never hard delete)

**Unit management** (`/admin/units`):
- List all units with hierarchy
- Create: code, name, parent unit
- Deactivate: set `is_active = false`
- Cannot deactivate a unit that still has active documents

---

## F12 — Dashboard & Reports

**Actor:** `super_admin`, `auditor`

**Dashboard** (`/admin`):
- Count cards: total active documents, new this month, total obsolete, ready to destroy
- Recent activity log (last 10 entries)

**Reports page** (`/admin/reports`):

### Master Document List (Daftar Induk Dokumen)
- Table of all active documents
- Filters: type, unit, year
- Export to Excel: `GET /admin/reports/export-excel`
- Export to PDF: `GET /admin/reports/export-pdf`

### Activity Log
- Filter: user, action, date range, document
- Paginate: 50 per page
- Export to Excel

### Usage Statistics
- Top 10 most downloaded documents (last 30 days)
- Most active users

---

## F13 — In-App Notifications

**Types and recipients:**

| Type | Trigger | Recipients |
|------|---------|------------|
| `new_document` | Document published | All users in owner unit |
| `document_obsolete` | Document set obsolete | Users who previously downloaded it |
| `new_regulation` | External regulation uploaded | Users in affected units |

**Implementation:**
- Notifications saved to `notifications` table
- Bell icon in navbar (admin layout + portal layout)
- Badge count = unread count (`is_read = false`)
- Click notification → mark as read → redirect to document
- Bulk sending dispatched via **Laravel Queue** (database driver)

---

## F14 — Obsolete Archive & Destruction

**Actor:** `super_admin`
**Route:** `GET /admin/archive`
**Controller:** `Admin\ArchiveController`

**Page shows:**
- Table of all documents with `status = obsolete`
- Columns: title, unit, type, obsolete date, retention end date, destruction status
- Filters: unit, type, destruction status
- Highlight row in red if `retention_end_date < today()` and `destruction_status = in_retention`

**Available actions per row:**

### Mark as Ready to Destroy
- Available if `destruction_status = in_retention` AND `retention_end_date < today()`
- Route: `PATCH /admin/archive/{retention}/mark-ready`
- Updates `destruction_status` → `ready_to_destroy`

### Execute Destruction
- Available if `destruction_status = ready_to_destroy`
- Route: `PATCH /admin/archive/{retention}/destroy-record`
- jQuery modal asks for `destruction_certificate_number` (Required)
- Process:
  1. Delete physical file from storage (`Storage::delete($file->file_path)`)
  2. Set `document_files.file_path = null` for this document
  3. Update `obsolete_retentions`:
     - `destruction_status` → `destroyed`
     - `destruction_certificate_number` → from input
     - `processed_by` → `auth()->id()`
     - `destroyed_at` → `now()`
  4. `documents` record and `obsolete_retentions` record are **NOT deleted**

**Edge cases:**
- File already missing from storage → continue process, do not throw error
- Print destruction certificate → generate PDF with list of destroyed documents

---

## Prompt for Claude Code

### For a single feature:
```
Read simars-doc-tech-stack.md and simars-doc-feature.md first.

Implement feature [F0X — feature name] as specified in simars-doc-feature.md.
Follow all conventions in simars-doc-tech-stack.md (Blade + Controller + jQuery, English naming).

Create all required files:
- Controller in app/Http/Controllers/Admin/ or Portal/
- Form Request in app/Http/Requests/ (if form submission)
- Service class in app/Services/ (for business logic)
- Policy in app/Policies/ (if access rules are involved)
- Blade views in resources/views/admin/ or portal/
- Add routes to routes/web.php
```

### For all features at once:
```
Read mars-doc-tech-stack.md, simars-doc-feature.md,
and SIMARS_DOC_Migration_Spec.md first.

Implement all features F01 to F14 in order.
Start with F01 (auth/Breeze setup), then F11 (master data), then document features.
Stack: Blade + Controller + jQuery + Tailwind. English naming throughout.
```

### For a specific feature with extra context:
```
Read all three spec files: Migration_Spec.md, Tech_Stack.md, Feature_Spec.md.

Implement F05 — Set Obsolete.
Key points:
- admin_unit can only obsolete documents from their own unit (enforce in DocumentPolicy)
- Use jQuery modal for confirmation (not a separate page)
- Modal form must include: obsolete_reason (required textarea) and
  replaced_by_id (optional select from active documents)
- After setting obsolete, automatically create a record in obsolete_retentions
- Send notifications via Laravel Queue to users who downloaded this document
```
