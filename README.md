# Laravel XML Templating

Ein XML-Templating-System für Laravel, das modulare Templates mit XPath-Modifikationen ermöglicht.

## Funktionen

- XML-basierte Templates für Laravel
- Vererbung zwischen Templates
- XPath-basierte Modifikationen (replace, inside, before, after)
- Modulare Struktur für erweiterbare Anwendungen
- Caching-Mechanismus für optimale Performance

## Installation

Fügen Sie das Repository und die Abhängigkeit in Ihrer `composer.json` hinzu:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/lexcode24/laravel-xml-templating.git"
    }
],
"require": {
    "lexcode/laravel-xml-templating": "dev-main"
}
```

Führen Sie dann `composer update` aus:

```bash
composer update
```

Veröffentlichen Sie die Konfiguration:

```bash
php artisan vendor:publish --provider="Lexcode\XmlTemplating\Providers\XmlTemplatingServiceProvider" --tag="config"

```

Veröffentliche und führe die Migrationen aus:

```bash
php artisan vendor:publish --provider="Lexcode\XmlTemplating\Providers\XmlTemplatingServiceProvider" --tag="migrations"

```

Benenne die veröffentlichten Migrationsdateien mit Zeitstempeln um, damit sie in der richtigen Reihenfolge ausgeführt werden:

```bash
# Beispiel (passe die Pfade an dein Projekt an)
mv database/migrations/create_templates_table.php database/migrations/2025_05_01_000001_create_templates_table.php

mv database/migrations/create_template_modifications_table.php database/migrations/2025_05_01_000002_create_template_modifications_table.php
```

Führen Sie die Migrationen aus:

```bash
php artisan migrate
```

### Voraussetzungen

- PHP 8.0 oder höher
- Laravel 8.0 oder höher
- PostgreSQL, MySQL oder SQLite

### Via Composer

```bash
composer require lexcode/laravel-xml-templating
```

