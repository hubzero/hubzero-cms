<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class BlogEntry extends Model
{
    protected $table = 'blog_entries';

    public $timestamps = false;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'created' => 'datetime',
            'publish_up' => 'datetime',
            'publish_down' => 'datetime',
        ];
    }

    // -- Relationships --

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class, 'entry_id');
    }

    // -- Scopes --

    public function scopePublished(Builder $query): void
    {
        $now = Carbon::now();

        $query->where('state', 1)
            ->where(function ($q) use ($now) {
                $q->whereNull('publish_up')
                    ->orWhere('publish_up', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('publish_down')
                    ->orWhere('publish_down', '>', $now);
            });
    }

    public function scopeSite(Builder $query): void
    {
        $query->where('scope', 'site');
    }

    // -- Accessors --

    protected function url(): Attribute
    {
        return Attribute::get(function () {
            $date = $this->publish_up ?? $this->created;

            return '/blog/' . $date->format('Y') . '/' . $date->format('m') . '/' . $this->alias;
        });
    }

    protected function excerpt(): Attribute
    {
        return Attribute::get(function () {
            return \Illuminate\Support\Str::limit(strip_tags($this->content), 200);
        });
    }

    // -- Query helpers --

    public function isPublished(): bool
    {
        if ($this->state !== 1) {
            return false;
        }

        $now = Carbon::now();

        if ($this->publish_up && $this->publish_up->gt($now)) {
            return false;
        }

        if ($this->publish_down && $this->publish_down->lte($now)) {
            return false;
        }

        return true;
    }
}
