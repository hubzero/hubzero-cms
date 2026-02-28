<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Framework\Facades\Services;

use Hubzero\Config\Registry;
use Illuminate\Support\Facades\DB;

/**
 * Menu service that loads menu items from the jos_menu table.
 *
 * Provides the same API as the legacy Hubzero\Menu\Type\Site class
 * so that mod_menu and other consumers work unchanged.
 */
class MenuService
{
    /** @var \stdClass[]|null Menu items indexed by ID */
    private ?array $items = null;

    /** @var \stdClass|null Default (home) menu item */
    private ?object $default = null;

    /** @var int|null Active menu item ID */
    private ?int $activeId = null;

    /**
     * Load all published frontend menu items from the database.
     */
    private function load(): void
    {
        if ($this->items !== null) {
            return;
        }

        $this->items = [];

        try {
            $rows = DB::table('menu')
                ->where('published', 1)
                ->where('parent_id', '>', 0)
                ->where('client_id', 0)
                ->orderBy('lft')
                ->get();
        } catch (\Exception $e) {
            return;
        }

        foreach ($rows as $item) {
            // Decode params to Registry
            $item->params = new Registry($item->params);

            // Parse the link query string
            $item->query = [];
            if ($item->link && str_contains($item->link, '?')) {
                $queryString = substr($item->link, strpos($item->link, '?') + 1);
                parse_str($queryString, $item->query);
            }

            // Derive component name from link query
            $item->component = $item->query['option'] ?? '';

            $this->items[$item->id] = $item;

            // Track the default/home item
            if ($item->home) {
                $this->default = $item;
            }
        }

        // Build tree property for each item (array of ancestor IDs)
        foreach ($this->items as &$item) {
            $tree = [];
            if (isset($this->items[$item->parent_id])) {
                $tree = $this->items[$item->parent_id]->tree ?? [];
            }
            $tree[] = $item->id;
            $item->tree = $tree;
        }
        unset($item);
    }

    /**
     * Get the currently active menu item.
     */
    public function getActive(): ?object
    {
        $this->load();

        if ($this->activeId && isset($this->items[$this->activeId])) {
            return $this->items[$this->activeId];
        }

        // Try to match by current request path
        $path = trim(request()->getPathInfo(), '/');
        if ($path) {
            // Match by menu path/alias
            foreach ($this->items as $item) {
                if ($item->path === $path || $item->alias === $path) {
                    return $item;
                }
            }

            // Match by component option in link
            $option = request()->query('option', '');
            if (!$option) {
                // Derive option from path (e.g., /poll → com_poll)
                $segments = explode('/', $path);
                $option = 'com_' . $segments[0];
            }

            foreach ($this->items as $item) {
                if (
                    isset($item->query['option'])
                    && $item->query['option'] === $option
                ) {
                    return $item;
                }
            }
        }

        return null;
    }

    /**
     * Set the active menu item by ID.
     */
    public function setActive(int $id): ?object
    {
        $this->load();
        $this->activeId = $id;
        return $this->items[$id] ?? null;
    }

    /**
     * Get the default (home) menu item.
     */
    public function getDefault(?string $language = '*'): ?object
    {
        $this->load();
        return $this->default ?? (object) [
            'id' => 0,
            'link' => '/',
            'tree' => [],
            'params' => new Registry(),
        ];
    }

    /**
     * Get a specific menu item by ID.
     */
    public function getItem(int $id): ?object
    {
        $this->load();
        return $this->items[$id] ?? null;
    }

    /**
     * Get menu items filtered by attribute/value.
     *
     * Supports single attribute or array of attributes.
     * If $firstonly is true, returns the first match instead of array.
     *
     * @return array|object|null
     */
    public function getItems(
        string|array|null $attribute = null,
        mixed $value = null,
        bool $firstonly = false
    ): mixed {
        $this->load();

        if ($attribute === null) {
            return $firstonly ? reset($this->items) : array_values($this->items);
        }

        // Normalize to arrays
        $attributes = (array) $attribute;
        $values = (array) $value;

        $result = [];
        foreach ($this->items as $item) {
            $match = true;
            foreach ($attributes as $i => $attr) {
                $val = $values[$i] ?? null;
                $itemVal = $item->$attr ?? null;

                if ($val === null) {
                    continue; // null means don't filter on this attribute
                }

                if ($itemVal !== $val) {
                    $match = false;
                    break;
                }
            }

            if ($match) {
                if ($firstonly) {
                    return $item;
                }
                $result[] = $item;
            }
        }

        return $firstonly ? null : $result;
    }

    /**
     * Get all loaded menu items.
     */
    public function getMenu(): array
    {
        $this->load();
        return $this->items;
    }
}
