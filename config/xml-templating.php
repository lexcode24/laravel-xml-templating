<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cache-Dauer
    |--------------------------------------------------------------------------
    |
    | Wie lange sollen kompilierte Templates im Cache gespeichert werden (in Minuten).
    |
    */
    'cache_duration' => 60,

    /*
    |--------------------------------------------------------------------------
    | Automatische Kompilierung
    |--------------------------------------------------------------------------
    |
    | Ob Templates automatisch neu kompiliert werden sollen, wenn sie sich ändern.
    |
    */
    'auto_compile' => true,

    /*
    |--------------------------------------------------------------------------
    | Template-Verzeichnis
    |--------------------------------------------------------------------------
    |
    | Das Verzeichnis, in dem die kompilierten Templates gespeichert werden.
    |
    */
    'template_directory' => storage_path('framework/views/xml-templates'),
];