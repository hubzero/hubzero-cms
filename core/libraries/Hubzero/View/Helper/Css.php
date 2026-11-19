<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\View\Helper;

use Hubzero\Document\Asset\Stylesheet;
use Hubzero\Plugin\View as PluginView;
use Hubzero\Facades\Document;
use Hubzero\Facades\Request;

/**
 * Helper for pushing styles to the document.
 */
class Css extends AbstractHelper
{
    /**
     * Push CSS to the document
     *
     * @param   string  $stylesheet  Stylesheet or styles to add
     * @param   string|null  $extension   Extension name, e.g.: com_example, mod_example, plg_example_test
     * @param   string|null  $element     Plugin element. Only used for plugins and if first argument is folder name.
     * @return  \Hubzero\View\View|null
     */
    public function __invoke($stylesheet = '', $extension = null, $element = null)
    {
        $extension = $extension ?: $this->extension();

        if ($element) {
            $extension = 'plg_' . $extension . '_' . $element;
        }

        $asset = new Stylesheet($extension, $stylesheet);

        $asset = $this->isSuperGroupAsset($asset);

        if ($asset->exists()) {
            if ($asset->isDeclaration()) {
                Document::addStyleDeclaration($asset->contents());
            } else {
                Document::addStyleSheet($asset->link());
            }
        }
        return $this->getView();
    }

    /**
     * Determine the extension the view is being called from
     *
     * @return  string
     */
    private function extension()
    {
        $view = $this->getView();
        if ($view instanceof PluginView) {
            return 'plg_' . $view->getFolder() . '_' . $view->getElement();
        }
        return $view ? $view->get('option', Request::getCmd('option')) : Request::getCmd('option');
    }

    /**
     * Modify paths if in a super group
     *
     * @param   \Hubzero\Document\Asset\Stylesheet  $asset  Asset
     * @return  \Hubzero\Document\Asset\Stylesheet
     */
    protected function isSuperGroupAsset($asset)
    {
        if (
            $asset->extensionType() != 'components'
            || $asset->isDeclaration()
            || $asset->isExternal()
        ) {
            return $asset;
        }

        if (defined('JPATH_GROUPCOMPONENT')) {
            $base = JPATH_GROUPCOMPONENT;

            $asset->setPath('source', $base . DS . 'assets' . DS . $asset->type() . DS . $asset->file());
            //$asset->setPath('source', $base . DS . $asset->file());
        }

        return $asset;
    }
}
