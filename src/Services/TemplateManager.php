<?php

namespace Lexcode\XmlTemplating\Services;

use Lexcode\XmlTemplating\Models\Template;
use Lexcode\XmlTemplating\Models\TemplateModification;
use Illuminate\Support\Facades\Cache;

class TemplateManager
{
    /**
     * @var TemplateCompiler
     */
    protected $compiler;
    
    /**
     * Konstruktor.
     *
     * @param TemplateCompiler $compiler
     */
    public function __construct(TemplateCompiler $compiler)
    {
        $this->compiler = $compiler;
    }
    
    /**
     * Lädt ein Template aus der Datenbank oder dem Cache.
     *
     * @param string $templateName
     * @param string|null $moduleName
     * @return string
     */
    public function getCompiledTemplate(string $templateName, ?string $moduleName = null): string
    {
        $cacheKey = "xml-template:{$moduleName}:{$templateName}";
        
        // Versuche, das Template aus dem Cache zu holen
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        // Template kompilieren
        $templatePath = $this->compiler->compile($templateName, $moduleName);
        
        // Im Cache speichern (z.B. für 60 Minuten)
        Cache::put($cacheKey, $templatePath, 3600);
        
        return $templatePath;
    }
    
    /**
     * Erstellt ein neues Template.
     *
     * @param array $data
     * @return Template
     */
    public function createTemplate(array $data): Template
    {
        $template = Template::create($data);
        
        // Cache leeren
        $this->clearTemplateCache($template->name, $template->module_name);
        
        return $template;
    }
    
    /**
     * Aktualisiert ein bestehendes Template.
     *
     * @param Template $template
     * @param array $data
     * @return Template
     */
    public function updateTemplate(Template $template, array $data): Template
    {
        $template->update($data);
        
        // Cache leeren
        $this->clearTemplateCache($template->name, $template->module_name);
        
        return $template;
    }
    
    /**
     * Erstellt eine neue Template-Modifikation.
     *
     * @param array $data
     * @return TemplateModification
     */
    public function createModification(array $data): TemplateModification
    {
        $modification = TemplateModification::create($data);
        
        // Template finden und Cache leeren
        $template = Template::find($modification->template_id);
        if ($template) {
            $this->clearTemplateCache($template->name, $template->module_name);
        }
        
        return $modification;
    }
    
    /**
     * Leert den Cache für ein bestimmtes Template.
     *
     * @param string $templateName
     * @param string|null $moduleName
     * @return void
     */
    protected function clearTemplateCache(string $templateName, ?string $moduleName = null): void
    {
        $cacheKey = "xml-template:{$moduleName}:{$templateName}";
        Cache::forget($cacheKey);
    }
    
    /**
     * Kompiliert alle Templates neu.
     *
     * @return void
     */
    public function compileAllTemplates(): void
    {
        $templates = Template::where('active', true)->get();
        
        foreach ($templates as $template) {
            $this->compiler->compile($template->name, $template->module_name);
            $this->clearTemplateCache($template->name, $template->module_name);
        }
    }
}