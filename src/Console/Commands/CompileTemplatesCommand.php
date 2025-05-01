<?php

namespace Lexcode\XmlTemplating\Console\Commands;

use Illuminate\Console\Command;
use Lexcode\XmlTemplating\Services\TemplateManager;

class CompileTemplatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xml-templating:compile {template? : Der Name des zu kompilierenden Templates}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kompiliert XML-Templates zu Blade-Templates';

    /**
     * Execute the console command.
     */
    public function handle(TemplateManager $templateManager)
    {
        $templateName = $this->argument('template');

        if ($templateName) {
            $this->info("Kompiliere Template '{$templateName}'...");
            $templateManager->getCompiledTemplate($templateName);
            $this->info("Template '{$templateName}' wurde kompiliert.");
        } else {
            $this->info('Kompiliere alle Templates...');
            $templateManager->compileAllTemplates();
            $this->info('Alle Templates wurden kompiliert.');
        }
    }
}