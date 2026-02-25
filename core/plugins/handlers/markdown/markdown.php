<?php

namespace Plugins\Handlers\Markdown;

use Hubzero\Plugin\Plugin;

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Plugin class for MD file handling
 */
class Markdown extends Plugin
{
    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var  boolean
     */
// @phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_autoloadLanguage = true;

    /**
     * Determines if the given collection can be handled by this plugin
     *
     * @param   \Hubzero\Filesystem\Collection  $collection  The file collection to assess
     * @return  boolean
     **/
    public function canHandle(\Hubzero\Filesystem\Collection $collection)
    {
        $need = [
            'md' => 1
        ];

        // Check extension to make sure we can proceed
        if (!$collection->hasExtensions($need)) {
            return false;
        }

        return true;
    }

    /**
     * Handles view events for files
     *
     * @param   \Hubzero\Filesystem\Collection  $collection  The file collection to view
     * @return  void
     **/
    public function onHandleView(\Hubzero\Filesystem\Collection $collection)
    {
        if (!$this->canHandle($collection)) {
            return false;
        }

        $file = $collection->findFirstWithExtension('md');

        if (!$file || !($file instanceof \Hubzero\Filesystem\File)) {
            return false;
        }

        $source = rtrim($file->read());

        $cls = '\\cebe\\markdown\\' . $this->params->get('style', 'Markdown');

        $parser = new $cls();

        $rendered = $parser->parse($source);

        $view = $this->view('view', 'markdown');

        if (!$rendered) {
            $view->setError(Lang::txt('PLG_HANDLERS_MARKDOWN_ERROR_RENDER_FAILED'));
            $rendered = $source;
        }

        $view->rendered = $rendered;

        return $view;
    }
}
