# AC Stipendium — Post-Deadline Correction Form (v2)

**Date:** 2026-02-10  
**Request from:** Florian Beyeler (Bleifrei Type) via email  
**Status:** Planned

---

## Flow

1. Artist visits `/korrektur` (DE) or `/fr/correction` (FR)
2. Sees a simple form: **email address only**
3. Submits email
4. Response is always: **"E-Mail versendet – prüf deine Inbox"** (regardless of whether the email exists — prevents enumeration)
5. **If the email matches an existing application:** system sends a time-limited link (signed URL with token)
6. **If no match:** nothing is sent, but the user sees the same message
7. Artist clicks the link from their inbox → correction form loads **pre-filled** with their existing application data
8. Artist edits what they need, submits
9. The **existing application entry gets updated directly** (no separate collection, no merge step)

---

## Implementation

### 1. Token System

Add a `correction_token` and `correction_token_expires_at` field to the application entry when a correction is requested.

- Token: random 64-char string (`Str::random(64)`)
- Expiry: 48 hours from generation
- Stored directly on the application entry

### 2. New Routes

**API routes (`routes/api.php`):**
```php
Route::post('/correction/request', [CorrectionController::class, 'requestCorrection']);
Route::get('/correction/{token}', [CorrectionController::class, 'loadCorrection']);
Route::post('/correction/{token}', [CorrectionController::class, 'storeCorrection']);
```

### 3. CorrectionController

**`POST /api/correction/request`** — Email lookup
- Accepts `{ email: "..." }`
- Looks up application by email
- If found: generates token + expiry, saves to entry, sends email with link
- If not found: does nothing
- Always returns same response: `{ message: "E-Mail versendet – prüf deine Inbox" }`

**`GET /api/correction/{token}`** — Load existing data
- Finds application by `correction_token` where `correction_token_expires_at` is in the future
- Returns application data as JSON (personal info, works, etc.)
- If token invalid/expired: returns 404

**`POST /api/correction/{token}`** — Save correction
- Validates with same rules as original application (or relaxed subset)
- Updates the existing application entry
- Replaces uploaded files if new ones are provided
- Clears the correction token (single use)
- Returns success

### 4. Email Notification

New notification: `App\Notifications\Application\CorrectionLink`

- Subject: "Korrektur Ihrer Bewerbung / Correction de votre candidature"
- Body: Contains the signed correction link
- Link format: `https://acstipendium.ch/korrektur?token={token}`

### 5. Frontend

**Page 1: Email form** (`/korrektur`)
- Simple Vue component: email input + submit button
- On submit: POST to `/api/correction/request`
- On success: show "E-Mail versendet – prüf deine Inbox"

**Page 2: Correction form** (`/korrektur?token=xxx`)
- Same page, but if `?token=` is in the URL:
  - Fetches existing data via `GET /api/correction/{token}`
  - Pre-fills the application form
  - Submit goes to `POST /api/correction/{token}`
- Reuses the existing `Application.vue` form with props for:
  - `endpoint` (already added)
  - `prefillData` (new prop — object with existing field values)
  - `mode` (new prop — `'application'` or `'correction'`)

### 6. Vue Changes to Application.vue

- Add `prefillData` prop (Object, default null)
- Add `mode` prop (String, default 'application')
- On mount: if `prefillData` is provided, populate `form` and `works` from it
- In correction mode: maybe adjust success message/redirect

### 7. Antlers Template

The correction template checks for a `token` query param:
- No token → render email request form
- With token → render the full application form in correction mode

---

## Files to Create/Modify

| File | Action |
|------|--------|
| `app/Http/Controllers/Api/CorrectionController.php` | **Rewrite** (email lookup + token + load + save) |
| `app/Http/Requests/RequestCorrectionRequest.php` | **Create** (just validates email) |
| `app/Notifications/Application/CorrectionLink.php` | **Create** |
| `resources/js/forms/application/Application.vue` | **Modify** (add prefillData + mode props) |
| `resources/js/forms/correction/CorrectionRequest.vue` | **Create** (email-only form) |
| `resources/views/correction.antlers.html` | **Modify** (conditional: email form vs correction form) |
| `routes/api.php` | **Modify** (replace single route with 3 routes) |

### Files to Remove (from v1)

| File | Action |
|------|--------|
| `app/Http/Requests/StoreCorrectionRequest.php` | **Delete** (replaced by RequestCorrectionRequest) |
| `app/Console/Commands/MergeCorrections.php` | **Delete** (no longer needed) |
| `content/collections/corrections.yaml` | **Delete** (no separate collection) |
| `content/collections/corrections/` | **Delete** |
| `resources/blueprints/collections/corrections/correction.yaml` | **Delete** |
| `resources/views/partials/application_form_correction.antlers.html` | **Delete** (form partial merged into template) |

### Files to Keep (from v1)

| File | Status |
|------|--------|
| `content/collections/pages/de/korrektur.md` | ✅ Keep (page still needed) |
| `content/collections/pages/fr/correction.md` | ✅ Keep |
| `resources/views/correction.antlers.html` | ✅ Keep but modify |

---

## Security

- Token is single-use (cleared after successful correction)
- Token expires after 48h
- Same response for valid/invalid emails (no enumeration)
- Correction form validates all fields same as original

---

## Estimate

**~4-5 hours** total implementation and testing.
