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
     * @param   string  $extension   Extension name, e.g.: com_example, mod_example, plg_example_test
     * @param   string  $element     Plugin element. Only used for plugins and if first argument is folder name.
     * @return  object
     */
    public function __invoke($stylesheet = '', $extension = null, $element = null)
    {
        $extension = $extension ?: $this->extension();

        if ($element) {
            $extension = 'plg_' . $extension . '_' . $element;
        }

        $isDefault = ($stylesheet === '' || $stylesheet === null);

        $asset = new Stylesheet($extension, $stylesheet);

        if ($isDefault) {
            $asset = $this->resolveBladeAsset(Stylesheet::class, $extension, $asset);
        }

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
        if ($this->getView() instanceof PluginView) {
            return 'plg_' . $this->getView()->getFolder() . '_' . $this->getView()->getElement();
        }
        return $this->getView()->get('option', Request::getCmd('option'));
    }

    /**
     * For default css() calls, substitute the blade variant in daisyUI mode.
     *
     * @param   string  $class      Asset class
     * @param   string  $extension  Extension name
     * @param   object  $original   The original constructed asset
     * @return  object
     */
    private function resolveBladeAsset($class, $extension, $original)
    {
        if (Document::getCssFramework() === 'daisyui') {
            $file = $original->file();
            $bladeFile = preg_replace('/\.(\w+)$/', '.blade.$1', $file);

            return new $class($extension, $bladeFile);
        }

        return $original;
    }

    /**
     * Modify paths if in a super group
     *
     * @param   object  $asset  Asset
     * @return  object
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
