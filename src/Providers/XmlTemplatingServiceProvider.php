<?php

namespace Lexcode\XmlTemplating\Providers;

use Illuminate\Support\ServiceProvider;
use Lexcode\XmlTemplating\Services\TemplateCompiler;
use Lexcode\XmlTemplating\Services\TemplateManager;
use Lexcode\XmlTemplating\Console\Commands\CompileTemplatesCommand;
use Lexcode\XmlTemplating\Console\Commands\InstallCommand;

class XmlTemplatingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Konfiguration registrieren
        $this->mergeConfigFrom(
            __DIR__.'/../../config/xml-templating.php', 'xml-templating'
        );

        // Services registrieren
        $this->app->singleton('xml-template.compiler', function ($app) {
            return new TemplateCompiler();
        });

        $this->app->singleton('xml-template.manager', function ($app) {
            return new TemplateManager(
                $app->make('xml-template.compiler')
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Konfiguration veröffentlichen
        $this->publishes([
            __DIR__.'/../../config/xml-templating.php' => config_path('xml-templating.php'),
        ], 'config');

        // Migrationen veröffentlichen
        $this->publishes([
            __DIR__.'/../Database/migrations' => database_path('migrations'),
        ], 'migrations');

        // Migrationen laden - temporär deaktiviert, um die Reihenfolge zu kontrollieren
        // $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');

        // View-Namespace registrieren
        $this->app['view']->addNamespace('xml-templates', storage_path('framework/views/xml-templates'));

        // Middleware registrieren
        $this->app['router']->aliasMiddleware('compile-templates', 
            \Lexcode\XmlTemplating\Middleware\CompileTemplatesMiddleware::class);

        // Befehle registrieren
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                CompileTemplatesCommand::class,
            ]);
        }
    }
}