<?php

namespace Lexcode\XmlTemplating\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xml-templating:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installiert das XML-Templating-Paket';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Installiere XML-Templating-Paket...');

        $this->info('Veröffentliche Konfiguration...');
        $this->call('vendor:publish', [
            '--provider' => 'Lexcode\XmlTemplating\Providers\XmlTemplatingServiceProvider',
            '--tag' => 'config'
        ]);

        $this->info('Führe Migrationen aus...');
        $this->call('migrate');

        $this->info('Erstelle Verzeichnisse...');
        $directory = config('xml-templating.template_directory', storage_path('framework/views/xml-templates'));
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $this->info('Installation abgeschlossen!');
    }
}