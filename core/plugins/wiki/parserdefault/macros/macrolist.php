<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Wiki macro class for displaying all available macros and their documentation
 */
namespace Plugins\Wiki\Parserdefault\Macros;

use Plugins\Wiki\Parserdefault\WikiMacro;

/**
 * Wiki macro class for displaying all available macros and their documentation
 */
class Macrolist extends WikiMacro
{
    /**
     * Returns description of macro, use, and accepted arguments
     *
     * @return     array
     */
    public function description()
    {
        $txt = array();
        $txt['wiki'] = 'Displays a list of all installed Wiki macros, including documentation if available. Optionally, the name of a specific macro can be provided as an argument. In that case, only the documentation for that macro will be rendered.';
        $txt['html'] = '<p>Displays a list of all installed Wiki macros, including documentation if available. Optionally, the name of a specific macro can be provided as an argument. In that case, only the documentation for that macro will be rendered.</p>';
        return $txt['html'];
    }

    /**
     * Macros that are not for page authors: the group macro base class
     * and the smoke-test macro
     *
     * @var  array
     */
    protected $hidden = array('group', 'helloworld');

    /**
     * Generate macro output
     *
     * @return     string
     */
    public function render()
    {
        $path = dirname(__FILE__);

        $entries = array();

        // [[BR]] is handled by the parser itself, so it has no file here
        $entries['br'] = $this->entry('BR', '<p>Insert a line break.</p>');

        foreach ($this->files($path) as $name => $file) {
            include_once $file;

            // Macros live at the top level as FooMacro, or under a
            // sub-directory as a namespaced Plugins\...\Macros\Dir\Foo
            $pieces = explode('.', $name);
            $cls = ucfirst($name) . 'Macro';

            if (count($pieces) > 1 || !class_exists($cls)) {
                $cls = '\\Plugins\\Wiki\\Parserdefault\\Macros\\' . implode('\\', array_map('ucfirst', $pieces));
            }

            if (!class_exists($cls)) {
                continue;
            }

            $macro = new $cls();

            // The class carries the macro's proper capitalisation (TitleIndex, not Titleindex)
            $label = (new ReflectionClass($macro))->getShortName();
            $label = preg_replace('/Macro$/', '', $label);
            if (count($pieces) > 1) {
                $label = implode('.', array_map('ucfirst', array_slice($pieces, 0, -1))) . '.' . $label;
            }

            $description = trim($macro->description());

            // The parser only runs these inside a group wiki
            if (strtolower(substr($label, 0, 5)) == 'group') {
                $description .= '<p><em>Group wikis only.</em></p>';
            }

            $entries[strtolower($label)] = $this->entry($label, $description);
        }

        ksort($entries);

        $txt  = '<dl>' . "\n";
        $txt .= implode("\n", $entries);
        $txt .= '</dl>' . "\n";

        return $txt;
    }

    /**
     * Find the macro files, keyed by macro name (Dir.Name for sub-directories)
     *
     * @param   string  $path  The macros directory
     * @return  array
     */
    protected function files($path)
    {
        $files = array();

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() != 'php') {
                continue;
            }

            $name = substr($file->getPathname(), strlen($path) + 1, -4);
            $name = strtolower(str_replace(DS, '.', $name));

            // Skip anything in a macro's own assets directory
            if (strpos($name, 'macro-assets') === 0 || in_array($name, $this->hidden)) {
                continue;
            }

            $files[$name] = $file->getPathname();
        }

        return $files;
    }

    /**
     * One entry of the list
     *
     * @param   string  $label        Macro name as written in a page
     * @param   string  $description  Its description (HTML)
     * @return  string
     */
    protected function entry($label, $description)
    {
        $id = strtolower(str_replace('.', '-', $label));

        return '<dt id="macro-' . $id . '"><code>&#91;&#91;' . $label . '&#93;&#93;</code></dt><dd>' . $description . '</dd>';
    }
}
