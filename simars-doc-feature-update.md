# SIMARS-DOC — Feature Update Specification

> Dokumen ini mencatat perubahan dan tambahan fitur dari spesifikasi awal (`simars-doc-feature.md`).
> Baca bersama `simars-doc-feature.md` dan `SIMARS_DOC_Tech_Stack.md` sebelum implementasi.

---

## Ringkasan Perubahan

| # | Area | Jenis | Deskripsi Singkat |
|---|------|-------|-------------------|
| U1 | Upload Document | Update | Pilihan upload langsung sebagai obsolete |
| U2 | Upload Document | Update | Tambah field `expired_at` (masa berlaku) |
| U3 | Upload Document | Update | Tambah field `reminder` (bulan sebelum expired) |
| U4 | Upload Document | Update | Tambah field `visibility` (public / restricted) |
| U5 | Index Document | Update | Filter visibility: restricted hanya tampil ke `uploaded_by` |
| N1 | Review | Tambahan | Flag `is_reviewed` dengan dua mekanisme review |
| N2 | Dashboard | Tambahan | Statistik dokumen reviewed & akan expired |
| N3 | Portal User | Tambahan | Tampilkan dokumen obsolete + export Excel |

---

## U1 — Upload Langsung sebagai Obsolete

**Konteks:**
Saat ini, setiap dokumen baru selalu dimulai dari status `draft`, kemudian dipublikasikan menjadi `active`. Fitur ini menambahkan opsi agar admin dapat mengupload dokumen yang memang sudah tidak berlaku (misalnya dokumen lama yang baru didigitalisasi).

**Aktor:** `super_admin`, `admin_unit`

**Perubahan pada form upload (F02):**

Tambah field baru `target_status` (radio button atau select):
- `active` *(default)* — alur seperti sebelumnya: dokumen masuk sebagai `draft`, perlu dipublikasikan secara terpisah
- `obsolete` — dokumen langsung disimpan dengan status `obsolete`, melewati tahap `draft` dan `publish`

**Field tambahan yang muncul secara kondisional:**

Ketika `target_status = obsolete`, tampilkan:
- `obsolete_reason` — textarea, **required**
- `obsolete_date` — date picker, default today, **required**

Field `replaced_by_id` tidak diperlukan saat upload obsolete (dokumen baru, bukan pengganti).

**Proses saat `target_status = obsolete`:**
1. Validasi semua field termasuk `obsolete_reason` dan `obsolete_date`
2. Simpan dokumen langsung dengan:
   - `status = obsolete`
   - `obsolete_date` → dari input
   - `obsolete_reason` → dari input
   - `obsoleted_by` → `auth()->id()`
   - `published_at` → null (tidak pernah aktif)
3. Simpan file PDF/DOCX seperti biasa
4. Log `activity_logs` (action: `create_document`)
5. **Tidak ada notifikasi** — dokumen obsolete tidak dikirimkan ke unit

**Edge cases:**
- `target_status = obsolete` + `obsolete_reason` kosong → validasi error
- `target_status = active` → abaikan field obsolete, alur normal (draft)
- Dokumen obsolete yang diupload langsung **tidak bisa diedit** (sama seperti aturan existing: hanya draft yang bisa diedit)

---

## U2 — Field Masa Berlaku (`expired_at`)

**Konteks:**
Setiap dokumen dapat memiliki tanggal kadaluarsa. Field ini bersifat opsional dan berlaku untuk semua status dokumen.

**Perubahan pada form:**
- Tambah field `expired_at` (date picker) pada form upload (F02) dan edit (F03)
- Label: **"Masa Berlaku s/d"**
- Opsional
- Tidak ada batasan — admin bebas mengisi tanggal apapun

**Perubahan pada tampilan:**
- Tampilkan `expired_at` di halaman detail dokumen (admin show + portal show)
- Jika tanggal sudah lewat (`expired_at < today`): tampilkan badge/label **"Kadaluarsa"** berwarna merah di samping tanggal
- Jika belum lewat: tampilkan tanggal saja

**Database:**
- Kolom `expired_at` (nullable date) pada tabel `documents`
- Migration baru diperlukan

---

## U3 — Field Reminder Masa Berlaku (`reminder_months`)

**Konteks:**
Admin dapat menentukan berapa bulan sebelum `expired_at` dokumen perlu diingatkan untuk ditinjau ulang. Digunakan sebagai dasar perhitungan di dashboard.

**Perubahan pada form:**
- Tambah field `reminder_months` (input number) pada form upload (F02) dan edit (F03)
- Label: **"Ingatkan sebelum expired (bulan)"**
- Opsional, hanya relevan jika `expired_at` diisi
- Validasi: integer, min 1, max 60
- Placeholder: `e.g. 3`

**Logika reminder:**
Dokumen dianggap **"akan expired"** jika:
```
today >= expired_at - reminder_months bulan
```
Contoh: `expired_at = 2025-12-01`, `reminder_months = 3` → dokumen masuk hitungan reminder sejak `2025-09-01`.

**Database:**
- Kolom `reminder_months` (nullable tinyint unsigned) pada tabel `documents`
- Migration baru diperlukan (bisa digabung dengan U2)

---

## U4 — Visibilitas Dokumen (`visibility`)

**Konteks:**
Super admin dapat menandai dokumen sebagai `restricted` sehingga hanya bisa diakses oleh pengunggah dokumen tersebut. Ini untuk dokumen sensitif yang bahkan tidak boleh dilihat oleh sesama super_admin.

**Aktor:** Hanya `super_admin` yang dapat mengatur field ini

**Perubahan pada form upload (F02) dan edit (F03):**
- Tambah field `visibility` (radio button atau select)
- **Hanya ditampilkan jika role = `super_admin`**
- Pilihan:
  - `public` *(default)* — semua pengguna yang berhak dapat mengakses
  - `restricted` — hanya `uploaded_by` yang dapat melihat dan mengakses dokumen ini

**Aturan akses restricted:**

| Role | List Admin | List Portal | Detail / Preview / Download |
|------|-----------|-------------|----------------------------|
| `super_admin` (uploaded_by) | ✅ Muncul | ✅ Muncul | ✅ Bisa |
| `super_admin` lain | ❌ Tersembunyi | ⚠️ Muncul, tidak bisa akses detail | ❌ 403 |
| `admin_unit` | ❌ Tersembunyi | ⚠️ Muncul, tidak bisa akses detail | ❌ 403 |
| `auditor` | *(belum dikembangkan)* | *(belum dikembangkan)* | *(belum dikembangkan)* |
| `user` (portal) | — | ⚠️ Muncul, tidak bisa akses detail | ❌ 403 |

**Perilaku portal untuk dokumen restricted:**
- Dokumen restricted **tetap muncul** di list portal dengan badge **"Terbatas"**
- Klik detail → halaman 403 dengan pesan "Dokumen ini bersifat terbatas dan tidak dapat diakses"
- Tidak bisa preview PDF maupun download

**Implementasi:**
- **Admin list & report**: Global scope aktif → restricted tersembunyi kecuali milik sendiri
- **Portal list**: Gunakan `withoutGlobalScope('visibility')` agar restricted tetap muncul di list, tapi `DocumentPolicy` menolak akses ke `show`, `stream`, dan `download`
- **Admin show / stream / download**: Policy tetap dicek — jika restricted dan bukan `uploaded_by` → 403

**Edge cases:**
- `admin_unit` upload dokumen → field `visibility` tidak muncul → default `public`
- `super_admin` bukan `uploaded_by` akses URL langsung ke detail/stream/download → 403
- Dokumen restricted tidak muncul di laporan/report admin (global scope aktif di sana)

**Database:**
- Kolom `visibility` enum(`public`, `restricted`) default `public` pada tabel `documents`
- Migration baru diperlukan

---

## U5 — Index Document: Filter Restricted

**Konteks:**
Perubahan pada query di semua list dokumen untuk menyembunyikan dokumen `restricted` dari pengguna yang bukan `uploaded_by`.

**Berlaku pada:**
- `Admin\DocumentController@index`
- `Portal\DocumentController@index`
- Report: Master Document List (`ReportController@masterDocumentList`)
- Export Excel/PDF

**Implementasi:**
Scope global atau query scope lokal ditambahkan di semua query yang melibatkan tabel `documents`.

**Hasil Diskusi:**
- Gunakan global scope pada model `Document` untuk menyembunyikan restricted dari admin list & report
- Portal list menggunakan `withoutGlobalScope('visibility')` agar restricted tetap tampil di list
- Policy (`DocumentPolicy`) menangani akses ke `show`, `stream`, `download` untuk semua area
---

## N1 — Fitur Review Dokumen

**Konteks:**
Setiap dokumen aktif perlu ditandai apakah sudah pernah ditinjau. Review bisa terjadi secara otomatis (saat diobsolete) atau manual (admin klik tombol review).

**Database — kolom baru pada tabel `documents`:**

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `is_reviewed` | boolean, default `false` | Apakah sudah direview |
| `reviewed_at` | timestamp, nullable | Waktu review dilakukan |
| `reviewed_by` | UUID FK → users, nullable | Siapa yang mereview |
| `review_notes` | text, nullable | Catatan review (untuk review manual) |

**Mekanisme 1 — Review Otomatis (via Obsolete):**
- Ketika dokumen di-set obsolete (F05), secara otomatis:
  - `is_reviewed = true`
  - `reviewed_at = now()`
  - `reviewed_by = auth()->id()` (user yang melakukan obsolete)
  - `review_notes = null` (tidak ada catatan khusus, alasan ada di `obsolete_reason`)
- Tidak ada UI tambahan, terjadi di `DocumentService@setObsolete`

**Mekanisme 2 — Review Manual:**
- Tombol **"Review"** muncul di baris pada Index document (admin)
- Hanya muncul untuk dokumen dengan `is_reviewed = false`
- Hanya bisa dilakukan oleh `super_admin` dan `admin_unit` (own unit)

**Alur review manual:**
1. Admin klik tombol "Review" di baris dokumen
2. Muncul modal dengan:
   - Informasi dokumen (nomor, judul) — read-only
   - Textarea `review_notes` — **required**, label "Catatan Review"
3. Admin klik "Simpan Review"
4. Sistem update:
   - `is_reviewed = true`
   - `reviewed_at = now()`
   - `reviewed_by = auth()->id()`
   - `review_notes` → dari input
5. Log `activity_logs` (action: `review_document`) — **perlu tambah ke ENUM**
6. Baris di tabel: badge **"Reviewed"** menggantikan tombol Review

**Un-review (Reset Review):**
- Tombol **"Batalkan Review"** muncul di baris untuk dokumen dengan `is_reviewed = true`
- Hanya bisa dilakukan oleh `super_admin` dan `admin_unit` (own unit)
- Tidak ada modal konfirmasi — cukup konfirmasi JS sederhana
- Sistem reset: `is_reviewed = false`, `reviewed_at = null`, `reviewed_by = null`, `review_notes = null`
- Log `activity_logs` (action: `unreview_document`) — **perlu tambah ke ENUM**

**Routes baru:**
```
POST /admin/documents/{document}/review
POST /admin/documents/{document}/unreview
```

**Edge cases:**
- Dokumen sudah `is_reviewed = true` → tombol "Review" diganti tombol "Batalkan Review"
- `review_notes` kosong → validasi error
- Dokumen `obsolete` yang diupload langsung (U1) → `is_reviewed = true` secara otomatis saat upload
- `admin_unit` mencoba review/unreview dokumen unit lain → 403
- Dokumen yang auto-reviewed via obsolete tetap bisa di-unreview jika perlu

---

## N2 — Dashboard: Statistik Review & Expired

**Konteks:**
Tambahkan dua metrik baru pada halaman dashboard admin.

**Kartu statistik baru:**

### Total Dokumen Sudah Direview
- Hitung: `documents WHERE is_reviewed = true AND visibility = 'public' (+ milik sendiri jika restricted)`
- Label: **"Dokumen Direview"**
- Ikon: `ti-clipboard-check`
- Warna: hijau

### Kartu 2 — Dokumen Dalam Periode Reminder
- Hitung: dokumen yang memenuhi semua kondisi berikut:
  ```
  expired_at IS NOT NULL
  AND reminder_months IS NOT NULL
  AND today >= DATE_SUB(expired_at, INTERVAL reminder_months MONTH)
  AND expired_at >= today   ← belum benar-benar expired
  ```
- Label: **"Akan Expired"**
- Ikon: `ti-clock-exclamation`
- Warna: oranye

### Kartu 3 — Dokumen Sudah Expired (Overdue)
- Hitung: dokumen yang memenuhi:
  ```
  expired_at IS NOT NULL
  AND expired_at < today
  ```
- Label: **"Sudah Expired"**
- Ikon: `ti-clock-x`
- Warna: merah

**Catatan:** Kedua kartu (Akan Expired & Sudah Expired) ditampilkan sebagai kartu terpisah di dashboard. Klik kartu idealnya membuka daftar dokumen yang dimaksud (bisa link ke index dengan filter expired).

---

## N3 — Portal User: Tampilkan Obsolete + Export Excel

### N3a — Tampilkan Dokumen Obsolete

**Konteks:**
Saat ini portal hanya menampilkan dokumen `active`. Fitur ini menambahkan dokumen `obsolete` agar pengguna dapat melihat riwayat dokumen.

**Perubahan pada `Portal\DocumentController@index`:**
- Query berubah dari `status = active` menjadi `status IN ('active', 'obsolete')`
- Tambah filter tab/select: **Semua** | **Aktif** | **Obsolete**
- Default filter: **Aktif** (agar experience tidak berubah drastis)

**Tampilan dokumen obsolete di portal:**
- Badge merah **"Obsolete"** di baris
- Jika ada `replaced_by_id`: tampilkan link ke dokumen pengganti
- Pengguna tetap bisa melihat detail dan PDF dokumen obsolete

**Edge cases:**
- Dokumen `restricted` tetap tersembunyi walau obsolete (aturan U4 tetap berlaku)
- Dokumen `draft` **tidak pernah** muncul di portal

### N3b — Export Excel Daftar Dokumen

**Konteks:**
Pengguna portal (bukan hanya admin/auditor) dapat mengunduh daftar dokumen yang sedang ditampilkan dalam bentuk file Excel.

**Route baru:**
```
GET /portal/documents/export-excel
```

**Kolom Excel yang diekspor:**

| No | Nomor Dokumen | Judul | Jenis Dokumen | Unit | Sumber | Tanggal Berlaku | Masa Berlaku (expired_at) | Status | Alasan Obsolete |
|----|--------------|-------|---------------|------|--------|-----------------|--------------------------|--------|-----------------|

- Kolom **"Alasan Obsolete"** diisi jika `status = obsolete`, kosong jika tidak

**Perilaku:**
- Filter yang sedang aktif di portal (q, type, unit, status) ikut diterapkan ke export
- Hanya dokumen yang bisa dilihat pengguna (visibility rules tetap berlaku; portal list menggunakan `withoutGlobalScope` tapi restricted docs tidak bisa diakses detailnya)
- Tidak ada batasan jumlah baris (no pagination)
- Nama file: `daftar-dokumen-{tanggal}.xlsx`
- Memerlukan login (portal sudah `auth` middleware)

**Library:** `maatwebsite/excel` (Laravel Excel) — install via composer jika belum ada.

---

## Keputusan Diskusi

| # | Pertanyaan | Jawaban | Implementasi |
|---|-----------|---------|--------------|
| Q1 | Auditor bisa lihat restricted? | Role auditor belum dikembangkan, skip dulu | — |
| Q2 | Restricted di portal: muncul di list atau disembunyikan? | Muncul di list, tapi detail/preview/download diblokir | `withoutGlobalScope` di portal index, policy blokir di show/stream/download |
| Q3 | `is_reviewed` bisa di-reset? | Ya, bisa un-review | Tambah route `POST /documents/{doc}/unreview` |
| Q4 | Siapa yang bisa review manual? | `super_admin` + `admin_unit` (own unit) | Cek di policy atau controller |
| Q5 | Dashboard expired: terpisah atau digabung? | Terpisah | Dua kartu: "Akan Expired" (oranye) dan "Sudah Expired" (merah) |
| Q6 | Export Excel: perlu kolom `obsolete_reason`? | Ya | Kolom "Alasan Obsolete" ditambahkan ke ekspor |
| Q7 | Upload langsung obsolete → `is_reviewed` otomatis `true`? | Ya | Set di `DocumentService@store` saat `target_status = obsolete` |

---

## Perubahan Database Summary

| Tabel | Kolom Baru | Tipe | Keterangan |
|-------|-----------|------|------------|
| `documents` | `expired_at` | date, nullable | Masa berlaku dokumen |
| `documents` | `reminder_months` | tinyint unsigned, nullable | Bulan sebelum expired untuk reminder |
| `documents` | `visibility` | enum('public','restricted'), default 'public' | Visibilitas dokumen |
| `documents` | `is_reviewed` | boolean, default false | Status review |
| `documents` | `reviewed_at` | timestamp, nullable | Waktu review |
| `documents` | `reviewed_by` | uuid FK → users, nullable | User yang mereview |
| `documents` | `review_notes` | text, nullable | Catatan review manual |
| `activity_logs` | `action` ENUM | tambah `review_document`, `unreview_document` | Untuk log review & un-review manual |

**Migration:** Satu migration baru untuk semua kolom di atas pada tabel `documents`.

---

## Routes Baru

```
POST   /admin/documents/{document}/review          admin.documents.review
GET    /portal/documents/export-excel              portal.documents.export-excel
```
