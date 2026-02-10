# Correction Form v2 — Server Deployment

**Date:** 2026-02-10  
**Status:** Code pushed, content files need server deployment

---

## Content files to create on server

These are gitignored (`content/` is in `.gitignore`) and must be created manually.

### 1. DE page: `content/collections/pages/de/korrektur.md`

```yaml
---
id: 7e569074-42d0-48fb-ba67-e09bd520e8fa
blueprint: page
title: Korrektur
template: correction
noindex: true
updated_by: 60ed990e-b671-4114-a29f-2fd27d1ef4b6
updated_at: 1770714452
has_footer: true
---
```

### 2. FR page: `content/collections/pages/fr/correction.md`

```yaml
---
id: 1499bf2d-2726-4841-9af6-7d97fdc411cc
blueprint: page
title: Correction
template: correction
noindex: true
updated_by: 60ed990e-b671-4114-a29f-2fd27d1ef4b6
updated_at: 1770714452
has_footer: true
---
```

### 3. Clean up v1 leftovers (if deployed previously)

```bash
# Remove v1 corrections collection (no longer needed)
rm -f content/collections/corrections.yaml
rm -rf content/collections/corrections/
rm -rf resources/blueprints/collections/corrections/
```

---

## URLs

- **DE:** `https://acstipendium.ch/korrektur`
- **FR:** `https://acstipendium.ch/fr/correction`

Hidden (noindex, not in nav). Share via direct link only.

---

## How it works

1. Artist visits `/korrektur` → enters email
2. Always shows "E-Mail versendet – prüf deine Inbox" (no email enumeration)
3. If email matches an application → sends email with token link (48h expiry)
4. Artist clicks link → form pre-filled with existing data
5. Artist submits → original application updated directly, token cleared

No separate collection, no merge command needed.

---

## Verify on production

- [ ] Pages created and accessible
- [ ] Email sending works (`MAIL_*` env vars configured)
- [ ] `APP_URL` is set correctly in `.env` (used for correction link in email)
