<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Framework\Pathway;

/**
 * Breadcrumb trail service.
 */
class Pathway
{
    private array $items = [];

    public function append(string $name, string $link = ''): static
    {
        $this->items[] = ['name' => $name, 'link' => $link];
        return $this;
    }

    public function prepend(string $name, string $link = ''): static
    {
        array_unshift($this->items, ['name' => $name, 'link' => $link]);
        return $this;
    }

    public function names(): array
    {
        return array_column($this->items, 'name');
    }

    /**
     * Return trail items as objects with ->name and ->link properties,
     * matching the legacy Hubzero\Pathway\Item interface.
     *
     * @return \stdClass[]
     */
    public function items(): array
    {
        return array_map(function (array $item) {
            $obj = new \stdClass();
            $obj->name = $item['name'];
            $obj->link = $item['link'];
            return $obj;
        }, $this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function clear(): static
    {
        $this->items = [];
        return $this;
    }
}
