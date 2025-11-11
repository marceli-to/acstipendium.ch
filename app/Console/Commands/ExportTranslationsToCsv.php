<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportTranslationsToCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:export-csv {locale=fr}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export translation file to CSV format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $locale = $this->argument('locale');
        $jsonPath = lang_path("{$locale}.json");
        $jsTranslationsPath = resource_path('js/translations.json');

        $allTranslations = [];

        // Read lang/fr.json
        if (File::exists($jsonPath)) {
            $langTranslations = json_decode(File::get($jsonPath), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('Invalid JSON in lang file: '.json_last_error_msg());

                return Command::FAILURE;
            }

            $allTranslations = array_merge($allTranslations, $langTranslations);
            $this->info('✓ Loaded '.count($langTranslations)." translations from lang/{$locale}.json");
        } else {
            $this->warn("Translation file not found: {$jsonPath}");
        }

        // Read resources/js/translations.json
        if (File::exists($jsTranslationsPath)) {
            $jsTranslations = json_decode(File::get($jsTranslationsPath), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('Invalid JSON in JS translations file: '.json_last_error_msg());

                return Command::FAILURE;
            }

            $allTranslations = array_merge($allTranslations, $jsTranslations);
            $this->info('✓ Loaded '.count($jsTranslations).' translations from resources/js/translations.json');
        } else {
            $this->warn("JS translation file not found: {$jsTranslationsPath}");
        }

        // Check if we have any translations
        if (empty($allTranslations)) {
            $this->error('No translations found to export!');

            return Command::FAILURE;
        }

        // Create CSV filename with timestamp
        $timestamp = date('Y-m-d_H-i-s');
        $csvFilename = "translations_{$locale}_{$timestamp}.csv";
        $csvPath = storage_path("app/{$csvFilename}");

        // Open CSV file for writing
        $handle = fopen($csvPath, 'w');

        if ($handle === false) {
            $this->error("Could not create CSV file at: {$csvPath}");

            return Command::FAILURE;
        }

        // Write CSV header
        fputcsv($handle, ['German/Key', 'French/Translation']);

        // Write each translation as a row
        foreach ($allTranslations as $key => $value) {
            fputcsv($handle, [$key, $value]);
        }

        fclose($handle);

        $this->info('✓ Translations exported successfully!');
        $this->line("File: {$csvPath}");
        $this->line('Total entries: '.count($allTranslations));

        return Command::SUCCESS;
    }
}
