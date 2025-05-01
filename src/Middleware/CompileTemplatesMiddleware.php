<?php

namespace Lexcode\XmlTemplating\Middleware;

use Closure;
use Illuminate\Http\Request;
use Lexcode\XmlTemplating\Services\TemplateManager;
use Illuminate\Support\Facades\File;

class CompileTemplatesMiddleware
{
    /**
     * @var TemplateManager
     */
    protected $templateManager;
    
    /**
     * Konstruktor.
     *
     * @param TemplateManager $templateManager
     */
    public function __construct(TemplateManager $templateManager)
    {
        $this->templateManager = $templateManager;
    }
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Prüfen, ob Templates neu kompiliert werden müssen
        if (config('app.debug') || !File::exists(storage_path('framework/views/xml-templates/compiled'))) {
            $this->templateManager->compileAllTemplates();
            
            // Markieren, dass Templates kompiliert wurden
            $directory = storage_path('framework/views/xml-templates');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
            
            File::put("{$directory}/compiled", time());
        }
        
        return $next($request);
    }
}