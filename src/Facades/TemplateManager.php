<?php

namespace Lexcode\XmlTemplating\Facades;

use Illuminate\Support\Facades\Facade;

class TemplateManager extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'xml-template.manager';
    }
}