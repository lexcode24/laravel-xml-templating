<?php

namespace Lexcode\XmlTemplating\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateModification extends Model
{
    protected $fillable = [
        'template_id',
        'xpath',
        'operation',
        'content',
        'module_name',
        'priority',
    ];

    protected $casts = [
        'priority' => 'integer',
    ];

    /**
     * Das Template, auf das diese Modifikation angewendet wird.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }
}