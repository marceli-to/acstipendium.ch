# AC Stipendium — Post-Deadline Correction Form

**Date:** 2026-02-02  
**Request from:** Florian Beyeler (Bleifrei Type) via email  
**Status:** Planned

---

## Problem

The application deadline has passed, but some artists need to correct/update their uploaded documents (errors or incompleteness). Florian (AC) needs a way to let them resubmit.

---

## Proposed Solution

Create a **second collection** for corrections, with a hidden form page and an **artisan command** to merge corrections into the main applications collection.

### Why This Approach?

- ✅ Clean separation — no duplicate confusion in main collection
- ✅ Review before merge — Florian can check corrections first
- ✅ Batch process all at once
- ✅ Audit trail
- ✅ Reversible if something goes wrong

---

## Implementation Overview

### 1. New Collection: `corrections`

**File:** `content/collections/corrections.yaml`

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

**Blueprint:** Copy from `resources/blueprints/collections/applications/application.yaml` to `resources/blueprints/collections/corrections/correction.yaml`

---

### 2. New API Endpoint

**File:** `app/Http/Controllers/Api/CorrectionController.php`

- Copy `ApplicationController.php` as base
- Change collection from `applications` to `corrections`
- Adjust notifications (maybe simpler confirmation, or notify Florian that a correction came in)
- Store files in `storage/app/corrections/` instead of `applications/`

**Route:** `routes/api.php`

```php
Route::post('/correction', [CorrectionController::class, 'store']);
```

---

### 3. Hidden Pages (DE + FR)

**Create pages:**
- `content/collections/pages/de/korrektur.md`
- `content/collections/pages/fr/correction.md`

**URL:** `/korrektur` (DE) / `/fr/correction` (FR)

**Important:** 
- Add `noindex: true` in frontmatter
- Don't link in navigation
- Use obscure URL or share only via direct link

**Form partial:** Create `resources/views/partials/application_form_correction.antlers.html`

```antlers
{{# Same as application_form.antlers.html but WITHOUT the is_active check #}}
<div
  id="application-form"
  class="
    card
    card-rounded
    border
    md:border-2
    border-white
    mt-8
    md:mt-0
    !px-8
    md:!px-12
    col-span-full">
  <application-form 
    :eligibility-year="{{ eligibility_year }}"
    endpoint="/api/correction"
  ></application-form>
</div>
```

**Note:** The Vue component needs a small modification to accept `endpoint` as a prop (currently hardcoded to `/api/application`).

**Modify `Application.vue`:**
```javascript
const props = defineProps({
  eligibilityYear: {
    type: Number,
    default: () => new Date().getFullYear() + 1
  },
  endpoint: {
    type: String,
    default: '/api/application'
  }
});

// Then in submitForm():
const response = await axios.post(props.endpoint, formData, { ... });
```

---

### 4. Artisan Merge Command

**File:** `app/Console/Commands/MergeCorrections.php`

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Statamic\Facades\Entry;

class MergeCorrections extends Command
{
    protected $signature = 'applications:merge-corrections 
                            {--dry-run : Show what would be merged without making changes}
                            {--email= : Only process correction for specific email}';
    
    protected $description = 'Merge correction entries into main applications collection';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $filterEmail = $this->option('email');
        
        $corrections = Entry::query()
            ->where('collection', 'corrections')
            ->get();
        
        if ($filterEmail) {
            $corrections = $corrections->filter(fn($c) => $c->get('email') === $filterEmail);
        }
        
        if ($corrections->isEmpty()) {
            $this->info('No corrections to process.');
            return 0;
        }
        
        $this->info(sprintf('Found %d correction(s) to process.', $corrections->count()));
        
        foreach ($corrections as $correction) {
            $email = $correction->get('email');
            $name = $correction->get('firstname') . ' ' . $correction->get('name');
            
            $this->line('');
            $this->info("Processing: {$name} <{$email}>");
            
            // Find matching application
            $application = Entry::query()
                ->where('collection', 'applications')
                ->where('email', $email)
                ->first();
            
            if (!$application) {
                $this->warn("  ⚠ No matching application found for {$email}. Skipping.");
                continue;
            }
            
            $this->line("  Found matching application: {$application->id()}");
            
            if ($dryRun) {
                $this->line("  [DRY RUN] Would merge correction into application.");
                continue;
            }
            
            // Merge data (correction overwrites application)
            $correctionData = $correction->data()->toArray();
            unset($correctionData['id']); // Keep original ID
            
            // Handle file migration
            $this->migrateFiles($correction, $application);
            
            // Update application with correction data
            foreach ($correctionData as $key => $value) {
                if ($value !== null) {
                    $application->set($key, $value);
                }
            }
            
            $application->save();
            
            // Delete correction entry
            $correction->delete();
            
            $this->info("  ✓ Merged and deleted correction.");
        }
        
        $this->line('');
        $this->info('Done.');
        
        return 0;
    }
    
    protected function migrateFiles($correction, $application)
    {
        // Move files from corrections folder to applications folder
        // Update file paths in application entry
        
        $fileFields = ['zip_file', 'resume_file'];
        
        foreach ($fileFields as $field) {
            $correctionPath = $correction->get($field);
            if (!$correctionPath) continue;
            
            // Replace 'corrections/' with 'applications/' in path
            $newPath = str_replace('corrections/', 'applications/', $correctionPath);
            
            // Ensure target directory exists
            $targetDir = dirname(Storage::path($newPath));
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            
            // Move file
            if (Storage::exists($correctionPath)) {
                Storage::move($correctionPath, $newPath);
                $application->set($field, $newPath);
                $this->line("    Moved: {$correctionPath} → {$newPath}");
            }
        }
    }
}
```

**Usage:**
```bash
# Preview what would happen
php artisan applications:merge-corrections --dry-run

# Merge all corrections
php artisan applications:merge-corrections

# Merge specific email only
php artisan applications:merge-corrections --email="artist@example.com"
```

---

## Workflow for Florian

1. **Share the hidden URL** with artists who need to correct: `https://acstipendium.ch/korrektur`
2. Artist re-submits their application
3. New entry appears in **Korrekturen** collection in CMS (can review if needed)
4. When ready, Florian (or Marcel) runs:
   ```bash
   php artisan applications:merge-corrections
   ```
5. Original applications are updated, corrections are deleted

---

## Files to Create/Modify

| File | Action |
|------|--------|
| `content/collections/corrections.yaml` | Create |
| `resources/blueprints/collections/corrections/correction.yaml` | Create (copy from applications) |
| `app/Http/Controllers/Api/CorrectionController.php` | Create (based on ApplicationController) |
| `routes/api.php` | Add route |
| `resources/views/partials/application_form_correction.antlers.html` | Create |
| `resources/js/forms/application/Application.vue` | Modify (add endpoint prop) |
| `content/collections/pages/de/korrektur.md` | Create |
| `content/collections/pages/fr/correction.md` | Create |
| `app/Console/Commands/MergeCorrections.php` | Create |

---

## Estimate

**~3-4 hours** total implementation and testing.

---

## Edge Cases to Handle

- **No matching application:** Skip with warning (artist used different email?)
- **Multiple corrections for same email:** Process newest, or process all in order?
- **Files don't exist:** Log warning, continue
- **Application already deleted:** Skip with warning

---

## Notes

- The hidden URL should be shared via email only, not posted publicly
- Consider adding a note on the form explaining this is for corrections only
- Notifications can be simplified (just confirm receipt, no need for full welcome email)
