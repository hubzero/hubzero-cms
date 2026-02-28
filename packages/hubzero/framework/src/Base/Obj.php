<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Framework\Base;

/**
 * Base object with get/set methods.
 *
 * Minimal port of core/libraries/Hubzero/Base/Obj.php.
 */
#[\AllowDynamicProperties]
class Obj
{
    public function __construct(mixed $properties = null)
    {
        if ($properties !== null) {
            $this->setProperties($properties);
        }
    }

    public function get(string $property, mixed $default = null): mixed
    {
        return $this->$property ?? $default;
    }

    public function set(string $property, mixed $value = null): static
    {
        $this->$property = $value;
        return $this;
    }

    public function def(string $property, mixed $default = null): mixed
    {
        $value = $this->get($property, $default);
        return $this->set($property, $value);
    }

    public function setProperties(mixed $properties): bool
    {
        if (is_array($properties) || is_object($properties)) {
            foreach ((array) $properties as $k => $v) {
                $this->set($k, $v);
            }
            return true;
        }
        return false;
    }

    public function getProperties(bool $public = true): array
    {
        $vars = get_object_vars($this);

        if ($public) {
            foreach (array_keys($vars) as $key) {
                if (str_starts_with($key, '_')) {
                    unset($vars[$key]);
                }
            }
        }

        return $vars;
    }
}
