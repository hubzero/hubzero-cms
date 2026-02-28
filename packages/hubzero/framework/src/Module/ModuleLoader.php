<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Framework\Module;

use Hubzero\Config\Registry;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * Module loader service.
 *
 * Based on the legacy Hubzero\Module\Loader but works directly with
 * Laravel's container and our framework services instead of requiring
 * the legacy Hubzero Container.
 */
class ModuleLoader
{
    private Application $app;

    /** @var stdClass[]|null Cached module list from database */
    private ?array $modules = null;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Count modules using a logical condition string.
     *
     * Supports conditions like "left or right", "breadcrumbs",
     * "left and right". Odd tokens are position names, even tokens
     * are logical operators.
     */
    public function count(string $condition): int
    {
        $words = explode(' ', $condition);
        for ($i = 0; $i < count($words); $i += 2) {
            $words[$i] = count($this->byPosition(strtolower($words[$i])));
        }

        return (int) eval('return ' . implode(' ', $words) . ';');
    }

    /**
     * Get modules assigned to a position.
     *
     * @return stdClass[]
     */
    public function byPosition(string $position): array
    {
        $position = strtolower($position);
        $result = [];

        foreach ($this->all() as $module) {
            if ($module->position === $position) {
                $result[] = $module;
            }
        }

        return $result;
    }

    /**
     * Render all modules in a position.
     */
    public function position(string $position, string $style = 'none'): string
    {
        $html = '';
        foreach ($this->byPosition($position) as $module) {
            try {
                $html .= $this->render($module);
            } catch (\Throwable $e) {
                // Log but don't crash the page for a single broken module
                \Log::warning("Module {$module->module} failed: " . $e->getMessage());
            }
        }
        return $html;
    }

    /**
     * Render a single module.
     */
    public function render(stdClass $module): string
    {
        $params = new Registry($module->params);

        $moduleName = $this->canonical($module->module);
        $path = $this->path($moduleName);

        if (!$path) {
            return $module->content ?? '';
        }

        // Load language file for the module
        if ($this->app->bound('hubzero.lang')) {
            $lang = $this->app->make('hubzero.lang');
            $lang->load($moduleName, dirname($path));
        }

        // Resolve class name and try class-based rendering
        $className = $this->resolveClassName($moduleName);

        if ($className) {
            require_once $path;
        }

        if (
            $className
            && class_exists($className, false)
            && is_subclass_of($className, \Hubzero\Module\Module::class)
        ) {
            $instance = new $className($params, $module);

            ob_start();
            $instance->run();
            $content = ob_get_clean();

            return $content;
        }

        // Legacy fallback: include entry file with $params/$module in scope
        if (file_exists($path)) {
            ob_start();
            include $path;
            $content = ob_get_clean();
            return $content;
        }

        return '';
    }

    /**
     * Get the layout path for a module.
     *
     * Checks template override first, then falls back to module's
     * own tmpl directory.
     */
    public function getLayoutPath(string $module, string $layout = 'default'): string
    {
        $module = $this->canonical($module);

        // Check for template override
        $templatePath = $this->getTemplatePath();
        if ($templatePath) {
            $override = $templatePath . '/html/' . $module . '/' . $layout . '.php';
            if (file_exists($override)) {
                return $override;
            }
        }

        // Fall back to module's own layout
        $base = dirname($this->path($module));
        $layoutPath = $base . '/tmpl/' . $layout . '.php';
        $defaultPath = $base . '/tmpl/default.php';

        if (file_exists($layoutPath)) {
            return $layoutPath;
        }

        return $defaultPath;
    }

    /**
     * Find the entry file for a module.
     */
    public function path(string $module): string
    {
        $module = $this->canonical($module);
        $unprefixed = substr($module, 4);

        $paths = [
            base_path('app/modules/' . $unprefixed . '/' . $unprefixed . '.php'),
            base_path('app/modules/' . $module . '/' . $module . '.php'),
            base_path('core/modules/' . $unprefixed . '/' . $unprefixed . '.php'),
            base_path('core/modules/' . $module . '/' . $module . '.php'),
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return '';
    }

    /**
     * Ensure module name has mod_ prefix.
     */
    public function canonical(string $module): string
    {
        $module = preg_replace('/[^A-Z0-9_.\-]/i', '', $module);
        if (!str_starts_with($module, 'mod_')) {
            $module = 'mod_' . $module;
        }
        return $module;
    }

    /**
     * Resolve module element name to fully qualified class name.
     *
     * mod_breadcrumbs   → Modules\Breadcrumbs\Breadcrumbs
     * mod_events_cal    → Modules\EventsCal\EventsCal
     */
    public function resolveClassName(string $module): string
    {
        $name = $this->canonical($module);
        $name = substr($name, 4); // strip mod_
        $parts = explode('_', $name);
        $pascal = implode('', array_map('ucfirst', $parts));

        return "Modules\\{$pascal}\\{$pascal}";
    }

    /**
     * Get the active menu item ID for the current page.
     */
    private function getActiveMenuId(): int
    {
        if ($this->app->bound('hubzero.menu')) {
            $active = $this->app->make('hubzero.menu')->getActive();
            if ($active && isset($active->id)) {
                return (int) $active->id;
            }
        }

        return 0;
    }

    /**
     * Load all published site modules from the database.
     *
     * Filters by active menu item: modules with menuid=0 show on all
     * pages, menuid > 0 shows only on that menu item's page, and
     * menuid < 0 (negative of a menu item ID) excludes from that page.
     *
     * Results are cached for the lifetime of the request.
     *
     * @return stdClass[]
     */
    public function all(): array
    {
        if ($this->modules !== null) {
            return $this->modules;
        }

        $this->modules = [];
        $itemId = $this->getActiveMenuId();

        try {
            $query = DB::table('modules as m')
                ->leftJoin('modules_menu as mm', 'mm.moduleid', '=', 'm.id')
                ->leftJoin('extensions as e', function ($join) {
                    $join->on('e.element', '=', 'm.module')
                         ->on('e.client_id', '=', 'm.client_id');
                })
                ->where('m.published', 1)
                ->where('m.client_id', 0)
                ->where(function ($q) {
                    $q->where('e.enabled', 1)->orWhereNull('e.enabled');
                })
                ->where(function ($q) {
                    $q->whereNull('m.publish_up')
                      ->orWhere('m.publish_up', '<=', now());
                })
                ->where(function ($q) {
                    $q->whereNull('m.publish_down')
                      ->orWhere('m.publish_down', '>=', now());
                });

            // Filter by menu assignment: show on all pages (0),
            // or assigned to this specific menu item
            if ($itemId) {
                $query->where(function ($q) use ($itemId) {
                    $q->where('mm.menuid', $itemId)
                      ->orWhere('mm.menuid', '<=', 0);
                });
            } else {
                $query->where('mm.menuid', '<=', 0);
            }

            $rows = $query
                ->orderBy('m.position')
                ->orderBy('m.ordering')
                ->select(
                    'm.id', 'm.title', 'm.module', 'm.position',
                    'm.content', 'm.showtitle', 'm.params', 'mm.menuid'
                )
                ->get();
        } catch (\Exception $e) {
            return $this->modules;
        }

        // Apply negative exclusions and de-duplicate.
        // A module with menuid = -$itemId is explicitly excluded
        // from this page even if another row shows menuid = 0.
        $negId = $itemId ? -$itemId : false;
        $dupes = [];
        $clean = [];

        foreach ($rows as $row) {
            $negHit = ($negId !== false && (int) $row->menuid === $negId);

            if (isset($dupes[$row->id])) {
                if ($negHit) {
                    unset($clean[$row->id]);
                }
                continue;
            }

            $dupes[$row->id] = true;

            if (!$negHit) {
                $row->name = substr($row->module, 4);
                $row->style = null;
                $row->position = strtolower($row->position);
                $clean[$row->id] = $row;
            }
        }

        $this->modules = array_values($clean);

        return $this->modules;
    }

    /**
     * Get the active template's filesystem path.
     */
    private function getTemplatePath(): string
    {
        // Check if we have a template path configured
        $templateDir = base_path('app/templates');
        $template = 'hubzero'; // default

        $path = $templateDir . '/' . $template;
        if (is_dir($path)) {
            return $path;
        }

        return '';
    }
}
