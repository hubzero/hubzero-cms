<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogComment extends Model
{
    protected $table = 'blog_comments';

    public $timestamps = false;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'created' => 'datetime',
            'modified' => 'datetime',
        ];
    }

    // -- Relationships --

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(BlogEntry::class, 'entry_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent')->published()->oldest('created');
    }

    // -- Scopes --

    public function scopePublished(Builder $query): void
    {
        $query->where('state', 1);
    }

    public function scopeTopLevel(Builder $query): void
    {
        $query->where('parent', 0);
    }
}
