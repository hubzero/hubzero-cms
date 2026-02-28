<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Framework\Component;

use Hubzero\Framework\Base\Obj;

/**
 * Component view — renders PHP template files.
 *
 * Simplified port of core/libraries/Hubzero/View/View.php.
 * Templates are plain PHP files included with $this scope, so
 * they can access view properties via $this->propertyName.
 */
class View extends Obj
{
    protected ?string $_name = null;
    protected string $_basePath = '';
    protected string $_layout = 'default';
    protected string $_layoutExt = 'php';
    protected array $_path = ['template' => []];
    protected ?string $_template = null;
    protected ?string $_output = null;

    public function __construct(array $config = [])
    {
        if (isset($config['name'])) {
            $this->_name = $config['name'];
        }

        if (isset($config['base_path'])) {
            $this->_basePath = $config['base_path'];
        }

        if (isset($config['layout'])) {
            $this->_layout = $config['layout'];
        }

        // Build template search paths
        $templatePath = $this->_basePath . '/views/' . $this->getName() . '/tmpl';
        $this->_path['template'] = [];

        if (is_dir($templatePath)) {
            $this->_path['template'][] = $templatePath;
        }

        // Also check parent directory (views/{name}/ without /tmpl)
        $parentPath = dirname($templatePath);
        if (is_dir($parentPath) && $parentPath !== $templatePath) {
            $this->_path['template'][] = $parentPath;
        }
    }

    /**
     * Render and echo the template.
     */
    public function display(?string $tpl = null): void
    {
        $result = $this->loadTemplate($tpl);
        echo $result;
    }

    /**
     * Load and render the template file.
     */
    public function loadTemplate(?string $tpl = null): string
    {
        $this->_output = null;

        $layout = $this->_layout;
        $file = $tpl ? $layout . '_' . $tpl : $layout;
        $file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);

        // Find the template file
        $this->_template = $this->findTemplate($file);

        // Fall back to 'default' layout
        if (!$this->_template && $file !== 'default') {
            $fallback = $tpl ? 'default_' . $tpl : 'default';
            $this->_template = $this->findTemplate($fallback);
        }

        if (!$this->_template) {
            throw new \RuntimeException(sprintf('Layout "%s" not found in %s', $file, implode(', ', $this->_path['template'])), 404);
        }

        ob_start();
        include $this->_template;
        $this->_output = ob_get_clean();

        return $this->_output;
    }

    public function getName(): string
    {
        return $this->_name ?? '';
    }

    public function setName(string $name): static
    {
        $this->_name = $name;
        return $this;
    }

    public function getLayout(): string
    {
        return $this->_layout;
    }

    public function setLayout(string $layout): static
    {
        if (str_contains($layout, ':')) {
            $parts = explode(':', $layout);
            $this->_layout = $parts[1];
        } else {
            $this->_layout = $layout;
        }
        return $this;
    }

    public function getBasePath(): string
    {
        return $this->_basePath;
    }

    /**
     * Push CSS to the document.
     *
     * No args: auto-loads component CSS.
     * String containing '{' or ':': treated as inline style declaration.
     * Otherwise: treated as stylesheet URL.
     */
    public function css(string $stylesheet = '', ?string $extension = null): static
    {
        if (!app()->bound('hubzero.document')) {
            return $this;
        }

        $doc = app('hubzero.document');

        if ($stylesheet === '') {
            // Auto-load: /components/com_poll/assets/css/com_poll.css
            $option = $this->get('option', '');
            if ($option) {
                $path = '/components/' . $option . '/assets/css/' . $option . '.css';
                $doc->addStyleSheet($path);
            }
        } elseif (str_contains($stylesheet, '{')) {
            // Inline CSS declaration
            $doc->addStyleDeclaration($stylesheet);
        } else {
            // Stylesheet URL
            $doc->addStyleSheet($stylesheet);
        }

        return $this;
    }

    /**
     * Create a sub-view (partial) for rendering within templates.
     */
    public function view(string $layout, ?string $name = null): static
    {
        $view = new self([
            'base_path' => $this->_basePath,
            'name'      => $name ?? $this->_name,
            'layout'    => $layout,
        ]);

        $view->set('option', $this->get('option'))
             ->set('controller', $this->get('controller'))
             ->set('task', $this->get('task'));

        return $view;
    }

    /**
     * Escape a string for safe HTML output.
     */
    public function escape(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Find a template file in the search paths.
     */
    private function findTemplate(string $file): ?string
    {
        $filename = $file . '.' . $this->_layoutExt;

        foreach ($this->_path['template'] as $path) {
            $fullPath = $path . '/' . $filename;
            if (is_file($fullPath)) {
                return $fullPath;
            }
        }

        return null;
    }
}
