# Correction Form — Server Deployment

**Date:** 2026-02-10  
**Status:** Code pushed, content files need server deployment

---

## Content files to create on server

These are gitignored (`content/` is in `.gitignore`) and must be created manually on the server.

### 1. Collection config: `content/collections/corrections.yaml`

```yaml
title: Korrekturen
sites:
  - de
propagate: false
template: default
layout: layout
revisions: false
sort_dir: desc
date_behavior:
  past: public
  future: private
```

### 2. Empty entries directory

```bash
mkdir -p content/collections/corrections
```

### 3. DE page: `content/collections/pages/de/korrektur.md`

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

### 4. FR page: `content/collections/pages/fr/correction.md`

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

### 5. Storage directory for uploads

```bash
mkdir -p storage/app/corrections
```

---

## URLs

- **DE:** `https://acstipendium.ch/korrektur`
- **FR:** `https://acstipendium.ch/fr/correction`

These pages are hidden (noindex, not in navigation). Share via direct link only.

---

## Merge command

After corrections come in, merge them into main applications:

```bash
# Preview
php artisan applications:merge-corrections --dry-run

# Merge all
php artisan applications:merge-corrections

# Merge specific email
php artisan applications:merge-corrections --email="artist@example.com"
```
