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
            unset($correctionData['id']);
            
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
        $fileFields = ['zip_file', 'resume_file'];
        
        foreach ($fileFields as $field) {
            $correctionPath = $correction->get($field);
            if (!$correctionPath) continue;
            
            $newPath = str_replace('corrections/', 'applications/', $correctionPath);
            
            $targetDir = dirname(Storage::path($newPath));
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            
            if (Storage::exists($correctionPath)) {
                Storage::move($correctionPath, $newPath);
                $application->set($field, $newPath);
                $this->line("    Moved: {$correctionPath} → {$newPath}");
            }
        }
    }
}
