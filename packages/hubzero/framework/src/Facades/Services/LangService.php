<?php

namespace Hubzero\Framework\Facades\Services;

/**
 * INI-based language/translation service.
 *
 * Loads HubZero INI language files and provides translation
 * via txt() and translate(). Auto-loads base framework strings
 * on construction; component strings are loaded on demand via load().
 */
class LangService
{
    private array $strings = [];
    private array $paths = [];
    private string $lang = 'en-GB';

    public function __construct()
    {
        // Load base framework language strings
        $baseLangDir = base_path('core/bootstrap/Site/language/' . $this->lang);

        $this->loadFile($baseLangDir . '/' . $this->lang . '.ini', 'hubzero');
        $this->loadFile($baseLangDir . '/' . $this->lang . '.lib_hubzero.ini', 'lib_hubzero');

        // Also load admin framework strings when in admin context
        if ($this->isAdminClient()) {
            $adminLangDir = base_path('core/bootstrap/Administrator/language/' . $this->lang);
            $this->loadFile($adminLangDir . '/' . $this->lang . '.ini', 'hubzero.admin');
        }
    }

    /**
     * Translate a string key, with optional sprintf arguments.
     */
    public function txt(?string $key, ...$args): string
    {
        if ($key === null || $key === '') {
            return '';
        }

        $translation = $this->lookup($key);

        if (empty($args)) {
            return $translation;
        }

        // Filter out boolean args (legacy jsSafe/interpretBackSlashes flags)
        $sprintfArgs = array_values(array_filter($args, fn ($a) => !is_bool($a) && !is_array($a)));

        if (empty($sprintfArgs)) {
            return $translation;
        }

        return sprintf($translation, ...$sprintfArgs);
    }

    /**
     * Translate with an alternate suffix. Tries KEY_ALT first, falls back to KEY.
     */
    public function alt(?string $key, string $alt = '', ...$args): string
    {
        if ($key === null || $key === '') {
            return '';
        }

        $altKey = $key . '_' . $alt;
        if ($this->hasKey($altKey)) {
            return $this->txt($altKey, ...$args);
        }

        return $this->txt($key, ...$args);
    }

    /**
     * Core translation lookup.
     */
    public function translate(string $key, bool $jsSafe = false, bool $interpretBackSlashes = true): string
    {
        $result = $this->lookup($key);

        if ($jsSafe) {
            $result = str_replace("'", "\\'", str_replace('"', '\\"', $result));
        }

        return $result;
    }

    /**
     * Load a language file for an extension.
     */
    public function load(
        string $extension = 'hubzero',
        string $basePath = '',
        ?string $lang = null,
        bool $reload = false,
        bool $default = true
    ): bool {
        $lang = $lang ?: $this->lang;

        // Don't reload if already loaded (unless forced)
        if (!$reload && isset($this->paths[$extension])) {
            return true;
        }

        $filename = $lang . '.' . $extension . '.ini';

        // Search paths in priority order
        $searchPaths = [];
        $clientDir = $this->isAdminClient() ? 'admin' : 'site';

        if ($basePath) {
            $searchPaths[] = $basePath . '/' . $clientDir . '/language/' . $lang . '/' . $filename;
            $searchPaths[] = $basePath . '/language/' . $lang . '/' . $filename;
        }

        // Component path from core
        $searchPaths[] = base_path('core/components/' . $extension . '/' . $clientDir . '/language/' . $lang . '/' . $filename);

        // Also try packages path
        $searchPaths[] = base_path('packages/hubzero/components/' . $extension . '/' . $clientDir . '/language/' . $lang . '/' . $filename);

        foreach ($searchPaths as $path) {
            if ($this->loadFile($path, $extension)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a key exists in loaded strings.
     */
    public function hasKey(string $key): bool
    {
        return isset($this->strings[strtoupper($key)]);
    }

    /**
     * Get the current language tag.
     */
    public function getTag(): string
    {
        return $this->lang;
    }

    public function isRTL(): bool
    {
        return false;
    }

    /**
     * Get the language code. Alias for getTag().
     */
    public function getLanguage(): string
    {
        return $this->lang;
    }

    /**
     * Get the language path for a given base and language.
     */
    public function getLanguagePath(string $basePath = '', ?string $language = null): string
    {
        if ($basePath === '') {
            $basePath = base_path('app');
        }

        $dir = $basePath . '/language';

        if (!empty($language)) {
            $dir .= '/' . $language;
        }

        return $dir;
    }

    /**
     * Get the default language tag.
     */
    public function getDefault(): string
    {
        return $this->lang;
    }

    /**
     * Get the locale string for sorting/collation.
     */
    public function getLocale(): string
    {
        return str_replace('-', '_', $this->lang);
    }

    /**
     * Return self — legacy code calls Lang::getRoot().
     */
    public function getRoot(): static
    {
        return $this;
    }

    /**
     * Get loaded file paths for an extension.
     */
    public function getPaths(?string $extension = null): ?array
    {
        if ($extension === null) {
            return $this->paths;
        }

        return $this->paths[$extension] ?? null;
    }

    public function isMultilang(): bool
    {
        return false;
    }

    /**
     * Check if the current client context is admin.
     */
    private function isAdminClient(): bool
    {
        return app()->bound('hubzero.client')
            && app('hubzero.client')->id === 1;
    }

    /**
     * Look up a translation key.
     */
    private function lookup(string $key): string
    {
        $upper = strtoupper($key);
        return $this->strings[$upper] ?? $key;
    }

    /**
     * Parse an INI file and merge into the strings array.
     */
    private function loadFile(string $path, string $extension): bool
    {
        if (!is_file($path)) {
            return false;
        }

        $parsed = @parse_ini_file($path, false, INI_SCANNER_RAW);

        if (!is_array($parsed) || empty($parsed)) {
            return false;
        }

        // Merge with uppercase keys
        foreach ($parsed as $key => $value) {
            $this->strings[strtoupper($key)] = str_replace('\\"', '"', $value);
        }

        // Track the loaded path
        $this->paths[$extension][] = $path;

        return true;
    }
}
