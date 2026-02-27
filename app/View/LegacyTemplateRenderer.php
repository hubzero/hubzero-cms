<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace App\View;

/**
 * Renders legacy HubZero PHP templates within Laravel.
 *
 * Processes a legacy template file (with jdoc:include tags and embedded
 * PHP using HubZero facades) and returns the complete HTML page.
 *
 * Usage:
 *   $renderer = new LegacyTemplateRenderer('hubzero', base_path('app/templates/hubzero'));
 *   $html = $renderer->render($componentOutput, 'System Status');
 */
class LegacyTemplateRenderer
{
    public string $template;
    public string $baseurl;
    public string $direction = 'ltr';
    public string $language = 'en';
    public TemplateParams $params;

    private string $templatePath;
    private string $title;
    private string $componentOutput;
    private array $headData = [];

    public function __construct(string $templateName, string $templatePath)
    {
        $this->template = $templateName;
        $this->templatePath = $templatePath;
        $this->baseurl = '';
        $this->params = new TemplateParams();
        $this->title = config('app.name', 'Hubzero');
    }

    /**
     * Render a complete page using the legacy template.
     */
    public function render(string $componentOutput, string $title = ''): string
    {
        $this->componentOutput = $componentOutput;

        if ($title) {
            $this->title = $title;
        }

        // Register stub facades before including the template
        $this->registerStubs();

        // Execute the template PHP file, capturing output
        $raw = $this->executeTemplate();

        // Parse and replace jdoc:include tags
        return $this->processJdocTags($raw);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Check if module positions have content.
     * Stub: always returns 0 (no modules loaded yet).
     */
    public function countModules(string $condition): int
    {
        return 0;
    }

    /**
     * Add a script to the document head.
     * Stub: records for head rendering.
     */
    public function addScript(string $url): void
    {
        $this->headData['scripts'][] = $url;
    }

    /**
     * Register stub classes for HubZero facades used in templates.
     */
    private function registerStubs(): void
    {
        if (!defined('_HZEXEC_')) {
            define('_HZEXEC_', 1);
        }

        // Only register stubs if the classes don't already exist
        // (they won't in a pure Laravel context)
        $stubs = $this->getStubDefinitions();

        foreach ($stubs as $className => $definition) {
            if (!class_exists($className, false)) {
                eval($definition);
            }
        }
    }

    /**
     * Return PHP class definitions for HubZero facade stubs.
     */
    private function getStubDefinitions(): array
    {
        $siteName = config('app.name', 'Hubzero');

        return [
            'Html' => <<<'PHP'
                class Html {
                    public static function behavior(...$args) {}
                }
            PHP,

            'Lang' => <<<PHP
                class Lang {
                    public static function txt(string \$key, ...\$args): string {
                        // Return readable fallback from translation key
                        \$map = [
                            'TPL_HUBZERO_SKIP_TO_CONTENT' => 'Skip to content',
                            'TPL_HUBZERO_MAINMENU' => 'Main menu',
                            'TPL_HUBZERO_TOGGLE_MENU' => 'Toggle menu',
                            'TPL_HUBZERO_ACCOUNT_MENU' => 'Account menu',
                            'TPL_HUBZERO_SEARCH' => 'Search',
                            'TPL_HUBZERO_LOGIN' => 'Log in',
                            'TPL_HUBZERO_LOGOUT' => 'Log out',
                            'TPL_HUBZERO_REGISTER' => 'Register',
                            'TPL_HUBZERO_TAGLINE' => '{$siteName}',
                            'TPL_HUBZERO_HELP' => 'Help',
                            'TPL_HUBZERO_NEED_HELP' => 'Need help?',
                            'TPL_HUBZERO_ACCOUNT_DASHBOARD' => 'Dashboard',
                            'TPL_HUBZERO_ACCOUNT_PROFILE' => 'Profile',
                            'TPL_HUBZERO_ACCOUNT_VIEWING_AS_ADMIN' => 'Admin',
                            'TPL_HUBZERO_ACCOUNT_ADMIN' => 'Admin',
                        ];
                        return \$map[\$key] ?? \$key;
                    }
                }
            PHP,

            'Config' => <<<PHP
                class Config {
                    public static function get(string \$key, mixed \$default = null): mixed {
                        \$map = [
                            'sitename' => '{$siteName}',
                        ];
                        return \$map[\$key] ?? \$default;
                    }
                }
            PHP,

            'User' => <<<'PHP'
                class User {
                    public static function isGuest(): bool { return true; }
                    public static function authorise(...$args): bool { return false; }
                    public static function get(string $key, $default = null) { return $default; }
                    public static function link(): string { return '#'; }
                    public static function picture(): string { return ''; }
                }
            PHP,

            'Request' => <<<'PHP'
                class Request {
                    public static function getString(string $key, string $default = '', string $source = ''): string {
                        if ($source === 'server') {
                            return $_SERVER[strtoupper($key)] ?? $default;
                        }
                        return request()->input($key, $default);
                    }
                    public static function root(): string {
                        return url('/');
                    }
                    public static function getCmd(string $key, string $default = ''): string {
                        return request()->input($key, $default);
                    }
                }
            PHP,

            'App' => <<<'PHP'
                class App {
                    public static function get(string $key) {
                        if ($key === 'menu') {
                            return new class {
                                public function getActive() { return null; }
                                public function getDefault() { return null; }
                            };
                        }
                        return null;
                    }
                }
            PHP,

            'Component' => <<<'PHP'
                class Component {
                    public static function params(string $name) {
                        return new class {
                            public function get(string $key, $default = null) { return $default; }
                        };
                    }
                }
            PHP,
        ];

        // Note: HubZero's Route facade conflicts with Laravel's Route.
        // We handle this by defining it only within the template execution
        // scope where Laravel's Route isn't used.
    }

    /**
     * Execute the template PHP file and capture its output.
     */
    private function executeTemplate(): string
    {
        // The HubZero Route facade must be defined here because it
        // conflicts with Laravel's Route facade. We define it in the
        // global namespace only if it hasn't been defined yet.
        if (!class_exists('Route', false)) {
            eval(<<<'PHP'
                class Route {
                    public static function url(string $url, bool $xhtml = true): string {
                        return $url;
                    }
                }
            PHP);
        }

        ob_start();
        // Template uses $this to access renderer properties/methods
        require $this->templatePath . '/index.php';
        return ob_get_clean();
    }

    /**
     * Parse jdoc:include tags and replace with rendered content.
     */
    private function processJdocTags(string $html): string
    {
        $pattern = '#<jdoc:include\s+type="([^"]+)"\s*(.*?)\s*/>#i';

        return preg_replace_callback($pattern, function ($matches) {
            $type = $matches[1];
            $attribs = $this->parseAttributes($matches[2] ?? '');

            return match ($type) {
                'head' => $this->renderHead(),
                'component' => $this->componentOutput,
                'message' => $this->renderMessage(),
                'modules', 'module' => '', // stub: no module content yet
                default => '',
            };
        }, $html);
    }

    /**
     * Parse XML-style attributes from a jdoc tag.
     */
    private function parseAttributes(string $string): array
    {
        $attribs = [];
        preg_match_all('/(\w+)="([^"]*)"/', $string, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $attribs[$match[1]] = $match[2];
        }

        return $attribs;
    }

    /**
     * Render the <head> content (title, meta, scripts).
     */
    private function renderHead(): string
    {
        $lines = [];
        $lines[] = '<title>' . htmlspecialchars($this->title) . '</title>';
        $lines[] = '<meta name="generator" content="Hubzero - Laravel" />';

        foreach ($this->headData['scripts'] ?? [] as $src) {
            $lines[] = '<script src="' . htmlspecialchars($src) . '"></script>';
        }

        return implode("\n        ", $lines);
    }

    /**
     * Render system messages (flash messages from Laravel session).
     */
    private function renderMessage(): string
    {
        $messages = [];

        if (session()->has('status')) {
            $messages[] = session('status');
        }

        if (empty($messages)) {
            return '';
        }

        $html = '<div id="system-message-container"><dl>';
        foreach ($messages as $msg) {
            $html .= '<dt>Info</dt><dd><ul><li>' . htmlspecialchars($msg) . '</li></ul></dd>';
        }
        $html .= '</dl></div>';

        return $html;
    }
}
