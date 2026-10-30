<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HealthCheck extends Command
{
    protected $signature = 'health:check';

    protected $description = 'Check if the site is returning 404/5xx errors and clear/warm caches if needed.';

    public function handle(): int
    {
        $url = config('app.url');

        try {
            $response = Http::timeout(10)->get($url);
        } catch (\Exception $e) {
            Log::warning('HealthCheck: could not reach site', ['error' => $e->getMessage()]);
            $this->warn("Could not reach {$url}: {$e->getMessage()}");

            return self::FAILURE;
        }

        if ($response->status() === 404 || $response->serverError()) {
            Log::warning('HealthCheck: homepage returned '.$response->status().', clearing caches');
            $this->warn("Homepage returned {$response->status()} — clearing and warming caches...");

            $this->call('optimize:clear');
            $this->call('stache:clear');
            $this->call('stache:warm');

            Log::info('HealthCheck: caches cleared and warmed');
            $this->info('Caches cleared and warmed.');

            return self::SUCCESS;
        }

        if ($response->successful()) {
            $this->info("Homepage OK ({$response->status()}).");

            return self::SUCCESS;
        }

        Log::warning('HealthCheck: homepage returned unexpected status', ['status' => $response->status()]);
        $this->warn("Homepage returned status {$response->status()}.");

        return self::FAILURE;
    }
}
