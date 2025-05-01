<?php

namespace Lexcode\XmlTemplating\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    protected $fillable = [
        'name',
        'module_name',
        'xml_content',
        'parent_template_id',
        'priority',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Das übergeordnete Template, von dem dieses erbt.
     */
    public function parentTemplate(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'parent_template_id');
    }

    /**
     * Die untergeordneten Templates, die von diesem erben.
     */
    public function childTemplates(): HasMany
    {
        return $this->hasMany(Template::class, 'parent_template_id');
    }

    /**
     * Die Modifikationen, die auf dieses Template angewendet werden.
     */
    public function modifications(): HasMany
    {
        return $this->hasMany(TemplateModification::class);
    }
}