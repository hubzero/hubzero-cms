<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\System\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Facades\Route;
use Hubzero\Facades\Lang;
use Hubzero\Facades\App;
use Hubzero\Facades\Notify;

/**
 * Controller for OPcache and APCu cache information
 */
class Cache extends AdminController
{
    /**
     * OPcache + APCu overview
     *
     * @return  void
     */
    public function displayTask()
    {
        $opcache = null;
        if (function_exists('opcache_get_status')) {
            $opcache = @opcache_get_status(false);
        }

        $opcacheConfig = null;
        if (function_exists('opcache_get_configuration')) {
            $opcacheConfig = opcache_get_configuration();
        }

        $apcu = null;
        $apcuMem = null;
        if (function_exists('apcu_cache_info')) {
            $apcu = @apcu_cache_info(true);
            $apcuMem = @apcu_sma_info(true);
        }

        $this->view
            ->set('opcache', $opcache)
            ->set('opcacheConfig', $opcacheConfig)
            ->set('apcu', $apcu)
            ->set('apcuMem', $apcuMem)
            ->display();
    }

    /**
     * OPcache cached scripts detail
     *
     * @return  void
     */
    public function opcacheTask()
    {
        $opcache = null;
        if (function_exists('opcache_get_status')) {
            $opcache = @opcache_get_status(true);
        }

        $this->view
            ->set('opcache', $opcache)
            ->setLayout('opcache')
            ->display();
    }

    /**
     * APCu cache detail
     *
     * @return  void
     */
    public function apcuTask()
    {
        $apcu = null;
        $apcuMem = null;
        if (function_exists('apcu_cache_info')) {
            $apcu = @apcu_cache_info(false);
            $apcuMem = @apcu_sma_info(true);
        }

        $this->view
            ->set('apcu', $apcu)
            ->set('apcuMem', $apcuMem)
            ->setLayout('apcu')
            ->display();
    }

    /**
     * Reset OPcache
     *
     * @return  void
     */
    public function resetopcacheTask()
    {
        if (function_exists('opcache_reset')) {
            opcache_reset();
            Notify::success(Lang::txt('COM_SYSTEM_CACHE_RESET_OPCACHE_SUCCESS'));
        }

        App::redirect(
            Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
        );
    }

    /**
     * Clear APCu cache
     *
     * @return  void
     */
    public function resetapcuTask()
    {
        if (function_exists('apcu_clear_cache')) {
            apcu_clear_cache();
            Notify::success(Lang::txt('COM_SYSTEM_CACHE_RESET_APCU_SUCCESS'));
        }

        App::redirect(
            Route::url(
                'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=apcu',
                false
            )
        );
    }
}
