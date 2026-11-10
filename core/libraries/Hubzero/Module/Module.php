<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Module;

use Hubzero\Document\Assets;
use Hubzero\Utility\Date;
use Hubzero\Facades\App;

/**
 * Base class for modules
 */
class Module
{
    use \Hubzero\Base\Traits\ErrorBag;
    use \Hubzero\Base\Traits\AssetAware;
    use \Hubzero\Base\Traits\Escapable;

    /**
     * Registry
     *
     * @var  object
     */
    public $params = null;

    /**
     * Database row
     *
     * @var  object
     */
    public $module = null;

    /**
     * Constructor
     *
     * @param   object  $params  Registry
     * @param   object  $module  Database row
     * @return  void
     */
    public function __construct($params, $module)
    {
        $this->params = $params;
        $this->module = $module;
    }

    /**
     * Display module
     *
     * The loader calls this. A module that caches its output overrides it
     * to return a stored copy from getCacheContent() and call run() only
     * when there is none; any other module can override either method.
     *
     * @return  void
     */
    public function display()
    {
        $this->run();
    }

    /**
     * Get the path of a layout for this module
     *
     * @param   string  $layout  The layout name
     * @return  string
     */
    public function getLayoutPath($layout = 'default')
    {
        return App::get('module')->getLayoutPath($this->module->module, $layout);
    }

    /**
     * Run the module logic and output content
     *
     * This is the work getCacheContent() buffers on a cache miss, so it
     * must not call display(). The default renders the layout named by the
     * `layout` parameter.
     *
     * @return  void
     */
    public function run()
    {
        require $this->getLayoutPath($this->params->get('layout', 'default'));
    }

    /**
     * Get the cached contents of a module
     * caching it, if it doesn't already exist
     *
     * @return  string
     */
    public function getCacheContent()
    {
        $content = '';

        if (!App::has('cache.store') || !$this->params->get('cache')) {
            return $content;
        }

        $debug = App::get('config')->get('debug');
        $key   = 'modules.' . $this->module->id;
        $ttl   = intval($this->params->get('cache_time', 0));

        if ($debug || !$ttl) {
            return $content;
        }

        if (!($content = App::get('cache.store')->get($key))) {
            ob_start();
            $this->run();
            $content = ob_get_contents();
            ob_end_clean();

            $content .= '<!-- cached ' . with(new Date('now'))->toSql() . ' -->';

            // Module time is in seconds, cache time is in minutes
            // Some module times may have been set in minutes so we
            // need to account for that.
            $ttl = $ttl <= 120 ? $ttl : ($ttl / 60);

            App::get('cache.store')->put($key, $content, $ttl);
        }

        return $content;
    }
}
