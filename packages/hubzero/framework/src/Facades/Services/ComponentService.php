<?php

namespace Hubzero\Framework\Facades\Services;

use Hubzero\Framework\Config\Registry;
use Illuminate\Support\Facades\DB;

/**
 * Component service for Component::params(), isEnabled(), etc.
 */
class ComponentService
{
    /** @var array<string, object> Cached component records */
    private static array $components = [];

    /**
     * Get component parameters from the extensions table.
     */
    public function params(string $option, bool $strict = false): Registry
    {
        return $this->load($option, $strict)->params;
    }

    public function isEnabled(string $option, bool $strict = false): bool
    {
        return (bool) $this->load($option, $strict)->enabled;
    }

    public function path(string $option): string
    {
        $option = $this->canonical($option);
        $name = substr($option, 4);

        $appPath = base_path("app/components/{$option}");
        if (is_dir($appPath)) {
            return $appPath;
        }

        return base_path("core/components/{$option}");
    }

    /**
     * Load a component record from the extensions table.
     *
     * Returns an object with id, option, params, enabled properties.
     */
    public function load(string $option, bool $strict = false): object
    {
        $option = $this->canonical($option);

        if (isset(self::$components[$option])) {
            return self::$components[$option];
        }

        try {
            $row = DB::table('extensions')
                ->where('type', 'component')
                ->where('element', $option)
                ->select('extension_id as id', 'element as option', 'params', 'enabled')
                ->first();
        } catch (\Exception $e) {
            $row = null;
        }

        if ($row) {
            $row->params = new Registry($row->params ?? '');
            self::$components[$option] = $row;
        } else {
            self::$components[$option] = (object) [
                'id' => 0,
                'option' => $option,
                'params' => new Registry(),
                'enabled' => $strict ? 0 : 1,
            ];
        }

        return self::$components[$option];
    }

    /**
     * Ensure component name has com_ prefix.
     */
    private function canonical(string $option): string
    {
        if (!str_starts_with($option, 'com_')) {
            $option = 'com_' . $option;
        }
        return strtolower($option);
    }
}
