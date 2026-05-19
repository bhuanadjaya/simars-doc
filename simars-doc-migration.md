# SIMARS-DOC — Database Migration Specification
> **For Claude Code:** Read this file and generate all Laravel migration files.
> Stack: **Laravel 12**, **MySQL 8**, **UUID primary keys**.

---

## Business Rules Summary

| # | Rule |
|---|------|
| 1 | No approval workflow — documents are uploaded directly as final by Document Admin |
| 2 | Status flow: `draft` → `active` → `obsolete` only |
| 3 | Obsolete is manual only — set by super_admin OR admin_unit (own unit docs only) |
| 4 | Obsolete is never automatic based on time or review date |
| 5 | Document number is entered manually — required, no format validation, duplicates allowed |
| 6 | A new document can explicitly reference the old document it replaces (`replaced_by_id`) |

---

## Instructions for Claude Code

```
Generate all 12 migration files for SIMARS-DOC using the specs below.

General rules:
1. Use Laravel 12 migration syntax
2. All primary keys: $table->uuid('id')->primary()
3. All foreign keys: $table->foreignUuid()
4. Add ->constrained()->cascadeOnDelete() or ->nullOnDelete() as specified per table
5. Add indexes on columns used for filtering/searching (specified per table)
6. Follow the migration ORDER below — it reflects foreign key dependencies
7. All table and column names in snake_case English
8. One migration per file with sequential timestamps
9. Add $table->timestamps() on all tables unless stated otherwise
10. Use softDeletes() only on tables marked [SOFT DELETE]

DO NOT create migrations for workflow_configs or approval_histories.
These tables are removed — the system does not use an approval workflow.
```

---

## Migration Order (Dependency Order)

```
001 - create_roles_table
002 - create_units_table
003 - create_users_table
004 - create_document_types_table
005 - create_documents_table
006 - create_document_files_table
007 - create_external_regulations_table
008 - create_activity_logs_table
009 - create_notifications_table

REMOVED (do not create):
- document_distributions  ← no hardcopy distribution needed, fully digital
- document_access         ← all active documents visible to all 
authenticated users
- obsolete_retentions.    ← obselete retentions doesn't required
```

---

## Table Specifications

### 001 — `roles`

```php
Schema::create('roles', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('name', 50)->unique();        // super_admin, admin_unit, user, auditor, guest
    $table->string('description')->nullable();
    $table->json('permissions');                 // array of permission strings
    $table->timestamps();
});
```

**Required seeders:**
```
super_admin  → full access: upload, publish, obsolete, delete, manage users
admin_unit   → upload & publish documents for own unit, view all active docs
user         → read-only: view & download active documents
auditor      → view all including obsolete, export reports
guest        → restricted: public documents only
```

---

### 002 — `units`

```php
Schema::create('units', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('code', 20)->unique();        // e.g. IGD, RANAP, LAB
    $table->string('name', 100);
    $table->foreignUuid('parent_id')             // self-referencing for sub-units
          ->nullable()
          ->constrained('units')
          ->nullOnDelete();
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->index('code');
    $table->index('is_active');
});
```

---

### 003 — `users` [SOFT DELETE]

```php
Schema::create('users', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('employee_id', 30)->unique()->nullable(); // NIP
    $table->string('name', 150);
    $table->string('nip', 18)->unique();
    $table->string('email', 150)->unique();
    $table->string('password');
    $table->foreignUuid('unit_id')
          ->constrained('units')
          ->restrictOnDelete();
    $table->foreignUuid('role_id')
          ->constrained('roles')
          ->restrictOnDelete();
    $table->boolean('is_active')->default(true);
    $table->timestamp('last_login_at')->nullable();
    $table->rememberToken();
    $table->softDeletes();
    $table->timestamps();

    $table->index('unit_id');
    $table->index('role_id');
    $table->index('is_active');
});
```

---

### 004 — `document_types`

```php
Schema::create('document_types', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('code', 20)->unique();         // SK, SPO, PERDIRUT, PANDUAN, etc.
    $table->string('name', 100);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

---

### 005 — `documents` [SOFT DELETE]

```php
Schema::create('documents', function (Blueprint $table) {
    $table->uuid('id')->primary();

    // Identity
    $table->string('number', 100);                // Manual input, required, no unique constraint
    $table->string('title', 255);
    $table->foreignUuid('document_type_id')
          ->constrained('document_types')
          ->restrictOnDelete();
    $table->foreignUuid('owner_unit_id')
          ->constrained('units')
          ->restrictOnDelete();
    $table->foreignUuid('uploaded_by')
          ->constrained('users')
          ->restrictOnDelete();

    // Metadata
    $table->enum('source', ['internal', 'external'])->default('internal');
    $table->unsignedSmallInteger('revision_number')->default(0); // 0=original, 1=Rev.01, etc.
    $table->text('description')->nullable();
    $table->string('tags')->nullable();            // Comma-separated keywords

    // Status & dates
    $table->enum('status', ['draft', 'active', 'obsolete'])->default('draft');
    $table->date('effective_date')->nullable();
    $table->date('published_at')->nullable();      // Set when admin publishes (draft → active)

    // Obsolete fields — filled by super_admin or admin_unit (own docs only)
    $table->date('obsolete_date')->nullable();
    $table->text('obsolete_reason')->nullable();   // Required when setting obsolete
    $table->foreignUuid('obsoleted_by')            // User who set obsolete
          ->nullable()
          ->constrained('users')
          ->nullOnDelete();
    $table->foreignUuid('replaced_by_id')          // The NEW document that replaces this one
          ->nullable()
          ->constrained('documents')
          ->nullOnDelete();

    // Revision chain
    $table->foreignUuid('parent_document_id')      // Previous version of this document
          ->nullable()
          ->constrained('documents')
          ->nullOnDelete();

    $table->softDeletes();
    $table->timestamps();

    $table->index('status');
    $table->index('document_type_id');
    $table->index('owner_unit_id');
    $table->index('number');
    $table->index('effective_date');
    $table->fullText(['title', 'description', 'tags']);
});
```

**Application logic notes (not migration):**
```
NUMBER FIELD:
- Required, entered manually by admin
- No format validation, no unique constraint
- Duplicates are allowed

STATUS FLOW:
  draft  → active  : Admin clicks "Publish" → sets published_at = today()
  active → obsolete: super_admin (all docs) OR admin_unit (own unit docs only)
                     obsolete_reason is required
                     replaced_by_id is optional (link to the new document)
  draft  → deleted : Soft delete, only allowed on draft documents

REPLACED_BY_ID relationship:
  Old SOP (obsolete).replaced_by_id = UUID of New SOP (active)
  UI shows: "This document has been replaced by: [New SOP title]"
  Reverse: "This document replaces: [Old SOP title]"
```

---

### 006 — `document_files`

> One document can have 2 files: PDF (required) and DOCX (optional).

```php
Schema::create('document_files', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('document_id')
          ->constrained('documents')
          ->cascadeOnDelete();
    $table->enum('file_type', ['pdf', 'docx']);
    $table->string('original_filename', 255);
    $table->string('file_path', 500);             // Path in storage/app/documents/
    $table->unsignedBigInteger('file_size');       // Bytes
    $table->string('mime_type', 100);
    $table->foreignUuid('uploaded_by')
          ->constrained('users')
          ->restrictOnDelete();
    $table->timestamp('uploaded_at')->useCurrent();

    // DO NOT add $table->timestamps() — use uploaded_at only

    $table->index('document_id');
    $table->unique(['document_id', 'file_type']); // Max 1 PDF + 1 DOCX per document
});
```

---

### 007 — `external_regulations`

> Repository for Permenkes, SNI, Accreditation Standards, etc.
> Status is immediately `active` on upload — no internal approval needed.

```php
Schema::create('external_regulations', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('regulation_number', 100);     // e.g. "Permenkes No. 1596 Tahun 2024"
    $table->string('title', 255);
    $table->string('issuing_agency', 150);         // Kemenkes, BNSP, etc.
    $table->enum('category', [
        'law',
        'government_regulation',
        'ministerial_regulation',
        'ministerial_decree',
        'national_standard',
        'accreditation_standard',
        'bpjs_regulation',
        'other'
    ]);
    $table->date('issued_date');
    $table->date('effective_date');
    $table->string('file_path', 500);
    $table->enum('status', ['active', 'inactive'])->default('active');
    $table->json('affected_unit_ids')->nullable(); // Array of unit UUIDs
    $table->foreignUuid('uploaded_by')
          ->constrained('users')
          ->restrictOnDelete();
    $table->timestamps();

    $table->index('category');
    $table->index('status');
    $table->fullText(['regulation_number', 'title']);
});
```

---

---

---

### 008 — `activity_logs`

> Append-only audit trail. No updates or deletes ever.

```php
Schema::create('activity_logs', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('user_id')
          ->constrained('users')
          ->cascadeOnDelete();
    $table->foreignUuid('document_id')
          ->nullable()
          ->constrained('documents')
          ->nullOnDelete();
    $table->enum('action', [
        'login',
        'logout',
        'view_document',
        'download_document',
        'create_document',
        'edit_document',
        'publish_document',
        'set_obsolete',
        'delete_document',
        'upload_regulation',
    ]);
    $table->text('detail')->nullable();            // JSON or extra context
    $table->string('ip_address', 45)->nullable();
    $table->string('user_agent', 255)->nullable();
    $table->timestamp('created_at')->useCurrent();

    // DO NOT add updated_at — this table is append-only

    $table->index('user_id');
    $table->index('document_id');
    $table->index('action');
    $table->index('created_at');
});
```

---

### 009 — `notifications`

```php
Schema::create('notifications', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('user_id')
          ->constrained('users')
          ->cascadeOnDelete();
    $table->foreignUuid('document_id')
          ->nullable()
          ->constrained('documents')
          ->nullOnDelete();
    $table->string('title', 150);
    $table->text('message');
    $table->enum('type', [
        'new_document',       // New document published in user's unit
        'document_obsolete',  // A document user accessed is now obsolete
        'new_regulation',     // New external regulation uploaded
    ]);
    $table->boolean('is_read')->default(false);
    $table->timestamp('read_at')->nullable();
    $table->timestamps();

    $table->index(['user_id', 'is_read']);
    $table->index('type');
});
```


**Application logic notes:**
```
- Record is created automatically when super_admin or admin_unit sets a document obsolete
- retention_end_date = obsolete_date + document_type.retention_months
- Admin dashboard shows WARNING if retention_end_date < today() and status = in_retention
- Physical file deletion only after super_admin confirms + enters destruction_certificate_number
- The document and this record are NEVER deleted — kept permanently as audit trail
```

---

## Database Changes Summary

| Table | Status | Notes |
|-------|--------|-------|
| `roles` | New | — |
| `units` | New | — |
| `users` | New | — |
| `document_types` | New | Replaces `jenis_dokumen` |
| `documents` | New | Replaces `dokumen` |
| `document_files` | New | Replaces `dokumen_files` |
| `external_regulations` | New | Replaces `regulasi_eksternal` |
| ~~`document_distributions`~~ | **Removed** | No hardcopy distribution needed |
| ~~`document_access`~~ | **Removed** | All active docs visible to all users |
| `activity_logs` | New | Replaces `log_aktivitas` |
| `notifications` | New | Replaces `notifikasi` |
| ~~`obselete_retentions`~~ | **Removed** | No retentions needed |
| ~~`workflow_configs`~~ | **Removed** | No approval workflow |
| ~~`approval_histories`~~ | **Removed** | No approval workflow |

**Total: 10 tables**

---

## Prompt for Claude Code

```
Read SIMARS_DOC_Migration_Spec.md in this directory.

Generate all 9 Laravel 12 migration files following the specifications.
Follow the numbered order (001–010). Use standard Laravel timestamp format:
  2025_01_01_000001_create_roles_table.php
  2025_01_01_000002_create_units_table.php
  ... and so on.

DO NOT create migrations for: workflow_configs, approval_histories,
document_distributions, or document_access.

Then generate:
1. DatabaseSeeder.php
2. RoleSeeder.php — 5 roles with appropriate JSON permissions
3. UnitSeeder.php — 5 sample hospital units (ER, Inpatient, Outpatient, Laboratory, Pharmacy)

Save to:
- database/migrations/
- database/seeders/
```

---

## Optional additions for Claude Code

**To generate Eloquent Models as well:**
```
Also generate an Eloquent Model for each table with:
- $fillable and $casts
- All relationships (belongsTo, hasMany, hasOne)
- Scopes on Document model: scopeActive(), scopeObsolete(), scopeDraft()
- replacedBy() and replaces() relationship methods on Document model
```
