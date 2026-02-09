# Bug: Zip Download Fails on Names with Slashes

**Date:** 2026-02-02  
**Status:** Open

---

## Problem

Applicant names containing slashes (e.g., "Marcel / Stadelmann") break the zip file creation. The name is used as the zip filename/title, and the `/` causes filesystem issues.

## Fix Needed

Sanitize the filename before creating the zip — strip or replace problematic characters (`/`, `\`, `:`, `*`, `?`, `"`, `<`, `>`, `|`).

Example:
```php
$safeName = preg_replace('/[\/\\\\:*?"<>|]/', '-', $name);
```

---

## Location

Check the download/export controller — likely in `app/Http/Controllers/` or wherever the applicant data zip is generated.
