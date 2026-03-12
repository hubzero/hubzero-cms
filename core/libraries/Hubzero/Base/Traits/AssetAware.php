<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Traits;

use Hubzero\Document\Asset\Image;
use Hubzero\Document\Asset\Javascript;
use Hubzero\Document\Asset\Stylesheet;
use Hubzero\Component\ControllerInterface;
use Hubzero\Plugin\Plugin;
use Hubzero\Module\Module;

/**
 * Asset Aware trait.
 * Adds helpers for pushing CSS and JS assets to the document.
 */
trait AssetAware
{
    /**
     * Push CSS to the document
     *
     * @param   string  $stylesheet  Stylesheet or styles to add
     * @param   string  $extension   Extension name, e.g.: com_example, mod_example, plg_example_test
     * @param   array   $attributes  Attributes
     * @return  object
     */
    public function css($stylesheet = '', $extension = null, $attributes = array())
    {
        $extension = $extension ?: $this->detectExtensionName();
        $isDefault = ($stylesheet === '' || $stylesheet === null);

        $attr = array_merge(array(
            'type'    => 'text/css',
            'media'   => null,
            'attribs' => array()
        ), $attributes);

        $asset = new Stylesheet($extension, $stylesheet);

        if ($isDefault) {
            $asset = $this->resolveBladeAsset(Stylesheet::class, $extension, $asset);
        }

        $asset = $this->isSuperGroupAsset($asset);

        if ($asset->exists()) {
            if ($asset->isDeclaration()) {
                \Hubzero\Facades\App::get('document')->addStyleDeclaration($asset->contents());
            } else {
                \Hubzero\Facades\App::get('document')->addStyleSheet($asset->link(), $attr['type'], $attr['media'], $attr['attribs']);
            }
        }

        return $this;
    }

    /**
     * Push JS to the document
     *
     * @param   string  $asset       Script to add
     * @param   string  $extension   Extension name, e.g.: com_example, mod_example, plg_example_test
     * @param   array   $attributes  Attributes
     * @return  object
     */
    public function js($asset = '', $extension = null, $attributes = array())
    {
        $extension = $extension ?: $this->detectExtensionName();
        $isDefault = ($asset === '' || $asset === null);

        $attr = array_merge(array(
            'type'  => 'text/javascript',
            'defer' => false,
            'async' => false
        ), $attributes);

        $asset = new Javascript($extension, $asset);

        if ($isDefault) {
            $asset = $this->resolveBladeAsset(Javascript::class, $extension, $asset);
        }

        $asset = $this->isSuperGroupAsset($asset);

        if ($asset->exists()) {
            if ($asset->isDeclaration()) {
                \Hubzero\Facades\App::get('document')->addScriptDeclaration($asset->contents());
            } else {
                \Hubzero\Facades\App::get('document')->addScript($asset->link(), $attr['type'], $attr['defer'], $attr['async']);
            }
        }

        return $this;
    }

    /**
     * Get the path to an image
     *
     * @param   string  $asset      Image name
     * @param   string  $extension  Extension name, e.g.: com_example, mod_example, plg_example_test
     * @return  string
     */
    public function img($asset, $extension = null)
    {
        $extension = $extension ?: $this->detectExtensionName();

        $asset = new Image($extension, $asset);

        return $asset->link();
    }

    /**
     * In daisyUI mode, substitute the blade variant of a CSS/JS
     * asset. Default calls (no explicit filename) get the blade
     * variant; named calls are suppressed entirely — if the
     * current mode's file doesn't exist, load nothing.
     *
     * @param   string  $class      Asset class (Javascript or Stylesheet)
     * @param   string  $extension  Extension name
     * @param   object  $original   The original constructed asset
     * @return  object
     */
    private function resolveBladeAsset($class, $extension, $original)
    {
        if (\Hubzero\Facades\Document::getCssFramework() !== 'daisyui') {
            return $original;
        }

        // In daisyUI mode, swap to the .blade.css/.blade.js variant.
        // If it doesn't exist, the caller's exists() check will
        // skip loading — no fallback to legacy assets.
        $file = $original->file();
        $bladeFile = preg_replace('/\.(\w+)$/', '.blade.$1', $file);

        return new $class($extension, $bladeFile);
    }

    /**
     * Determine the extension the view is being called from
     *
     * @return  string
     */
    private function detectExtensionName()
    {
        if ($this instanceof Plugin) {
            return 'plg_' . $this->_type . '_' . $this->_name;
        } elseif ($this instanceof ControllerInterface) {
            $option = property_exists($this, '_option') ? $this->_option : null;
            return \Hubzero\Facades\Request::getCmd('option', $option);
        } elseif ($this instanceof Module) {
            return $this->module->module;
        }

        return '';
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
