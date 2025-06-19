<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;

class ExportTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:export {--locale=en : The locale to export}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Laravel translations to JSON files for laravel-react-i18n';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $locale = $this->option('locale');
        $langPath = base_path("lang/{$locale}");
        $publicLangPath = public_path('lang');

        if (! File::exists($langPath)) {
            $this->error("Language directory for locale '{$locale}' does not exist at {$langPath}");

            return self::FAILURE;
        }

        // Create public/lang directory if it doesn't exist
        if (! File::exists($publicLangPath)) {
            File::makeDirectory($publicLangPath, 0755, true);
        }

        // Get all PHP translation files
        $files = File::files($langPath);
        $translations = [];

        foreach ($files as $file) {
            $filename = $file->getFilenameWithoutExtension();
            $translations[$filename] = Lang::get($filename, [], $locale);
        }

        // Export to JSON
        $jsonPath = "{$publicLangPath}/{$locale}.json";
        File::put($jsonPath, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("Translations exported to {$jsonPath}");
        $this->info('Exported files: '.implode(', ', array_keys($translations)));

        return self::SUCCESS;
    }
}
