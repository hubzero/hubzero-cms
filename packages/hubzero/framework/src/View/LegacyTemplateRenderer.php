<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Framework\View;

/**
 * Renders legacy HubZero PHP templates within Laravel.
 *
 * Processes a legacy template file (with jdoc:include tags and embedded
 * PHP using HubZero facades) and returns the complete HTML page.
 *
 * Facade aliases (Document::, Lang::, Config::, etc.) are registered by
 * FacadeRegistrar in the FrameworkServiceProvider, so templates can use
 * them directly without eval stubs.
 */
class LegacyTemplateRenderer
{
    public string $template;
    public string $baseurl;
    public string $direction = 'ltr';
    public string $language = 'en-gb';
    public TemplateParams $params;

    private string $templatePath;
    private string $title;
    private string $componentOutput;

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

        // Also sync title from Document singleton if it was set by the component
        if (app()->bound('hubzero.document')) {
            $document = app('hubzero.document');
            if ($document->getTitle()) {
                $this->title = $document->getTitle();
            }
        }

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
     */
    public function countModules(string $condition): int
    {
        if (app()->bound('hubzero.module')) {
            return app('hubzero.module')->count($condition);
        }
        return 0;
    }

    /**
     * Add a script to the document head.
     */
    public function addScript(string $url): void
    {
        if (app()->bound('hubzero.document')) {
            app('hubzero.document')->addScript($url);
        }
    }

    /**
     * Execute the template PHP file and capture its output.
     */
    private function executeTemplate(): string
    {
        // The template uses Route::url() and Request::getString() which
        // would otherwise resolve to Laravel's facades via AliasLoader.
        // Register HubZero legacy facades before the template runs.
        if (!class_exists('Route', false)) {
            class_alias(\Hubzero\Framework\Facades\LegacyRoute::class, 'Route');
        }
        if (!class_exists('Request', false)) {
            class_alias(\Hubzero\Framework\Facades\RequestFacade::class, 'Request');
        }
        if (!class_exists('App', false)) {
            class_alias(\Hubzero\Framework\Facades\AppFacade::class, 'App');
        }

        // Load template language file
        if (app()->bound('hubzero.lang')) {
            $langFile = $this->templatePath . '/language/en-GB/en-GB.tpl_' . $this->template . '.ini';
            if (is_file($langFile)) {
                app('hubzero.lang')->load('tpl_' . $this->template, $this->templatePath);
            }
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

            return match ($type) {
                'head' => $this->renderHead(),
                'component' => $this->componentOutput,
                'message' => $this->renderMessage(),
                'modules', 'module' => $this->renderModules($matches[2] ?? ''),
                default => '',
            };
        }, $html);
    }

    /**
     * Render modules for a given position from jdoc attributes.
     */
    private function renderModules(string $attributes): string
    {
        // Parse name="..." from the jdoc tag attributes
        if (!preg_match('/name="([^"]+)"/', $attributes, $m)) {
            return '';
        }

        $position = $m[1];

        if (!app()->bound('hubzero.module')) {
            return '';
        }

        return app('hubzero.module')->position($position);
    }

    /**
     * Render the <head> content (title, meta, stylesheets, scripts).
     */
    private function renderHead(): string
    {
        $lines = [];

        // Base tag for relative URLs (matches legacy behavior)
        $baseUrl = rtrim(request()->getSchemeAndHttpHost() . request()->getBaseUrl(), '/');
        $lines[] = '<base href="' . htmlspecialchars($baseUrl . request()->getPathInfo()) . '" />';

        $lines[] = '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';

        // Meta tags from legacy config
        $keywords = config('hubzero.meta.MetaKeys', 'hub, hubzero, research, collaboration');
        if ($keywords) {
            $lines[] = '<meta name="keywords" content="' . htmlspecialchars($keywords) . '" />';
        }

        $description = config('hubzero.meta.MetaDesc', '');
        if ($description) {
            $lines[] = '<meta name="description" content="' . htmlspecialchars($description) . '" />';
        }

        $lines[] = '<meta name="generator" content="HUBzero - The open source platform for scientific and educational collaboration" />';
        $lines[] = '<title>' . htmlspecialchars($this->title) . '</title>';

        // Read from Document singleton if available
        if (app()->bound('hubzero.document')) {
            $headData = app('hubzero.document')->getHeadData();

            foreach ($headData['metadata'] as $meta) {
                if ($meta['http_equiv']) {
                    $lines[] = '<meta http-equiv="' . htmlspecialchars($meta['name']) . '" content="' . htmlspecialchars($meta['content']) . '" />';
                } else {
                    $lines[] = '<meta name="' . htmlspecialchars($meta['name']) . '" content="' . htmlspecialchars($meta['content']) . '" />';
                }
            }

            foreach ($headData['stylesheets'] as $sheet) {
                $attrs = 'rel="stylesheet" href="' . htmlspecialchars($sheet['url']) . '"';
                if ($sheet['media']) {
                    $attrs .= ' media="' . htmlspecialchars($sheet['media']) . '"';
                }
                $lines[] = '<link ' . $attrs . ' />';
            }

            foreach ($headData['styleDeclarations'] as $style) {
                $lines[] = '<style>' . $style['content'] . '</style>';
            }

            foreach ($headData['scripts'] as $script) {
                $attrs = 'src="' . htmlspecialchars($script['url']) . '"';
                if ($script['defer']) {
                    $attrs .= ' defer';
                }
                if ($script['async']) {
                    $attrs .= ' async';
                }
                $lines[] = '<script ' . $attrs . '></script>';
            }

            foreach ($headData['scriptDeclarations'] as $script) {
                $lines[] = '<script>' . $script['content'] . '</script>';
            }

            foreach ($headData['customTags'] as $tag) {
                $lines[] = $tag;
            }
        }

        return implode("\n        ", $lines);
    }

    /**
     * Render system messages (flash messages from Laravel session + HubZero Notify).
     */
    private function renderMessage(): string
    {
        $messages = [];

        // Laravel session flash
        if (session()->has('status')) {
            $messages[] = ['message' => session('status'), 'type' => 'info'];
        }

        // HubZero Notify service
        if (app()->bound('hubzero.notify')) {
            foreach (app('hubzero.notify')->messages() as $msg) {
                $messages[] = $msg;
            }
        }

        if (empty($messages)) {
            return '<div id="system-message-container">' . "\n" . '</div>';
        }

        // Group by type
        $grouped = [];
        foreach ($messages as $msg) {
            $type = $msg['type'] ?? 'info';
            $grouped[$type][] = $msg['message'];
        }

        $html = '<div id="system-message-container"><dl>';
        foreach ($grouped as $type => $items) {
            $label = ucfirst($type);
            $html .= '<dt class="' . htmlspecialchars($type) . '">' . $label . '</dt>';
            $html .= '<dd class="' . htmlspecialchars($type) . ' message"><ul>';
            foreach ($items as $item) {
                $html .= '<li>' . htmlspecialchars($item) . '</li>';
            }
            $html .= '</ul></dd>';
        }
        $html .= '</dl></div>';

        return $html;
    }
}
