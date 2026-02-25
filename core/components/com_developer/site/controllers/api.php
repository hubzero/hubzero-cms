<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Developer\Site\Controllers;

use Hubzero\Component\SiteController;
use Hubzero\Api\Doc\Generator;
use Hubzero\Facades\Request;
use Hubzero\Facades\Pathway;
use Hubzero\Facades\Config;
use Hubzero\Facades\Lang;

/**
 * API Controller
 */
class Api extends SiteController
{
    /**
     * General intro display
     *
     * @return  void
     */
    public function displayTask()
    {
        $this->buildPathway();

        $this->view->display();
    }

    /**
     * Documentation
     *
     * @return  void
     */
    public function docsTask()
    {
        // build pathway
        $this->buildPathway();

        // generate documentation
        $generator = new Generator(!Config::get('debug'));

        // render view
        $this->view
            ->set('documentation', $generator->output('array'))
            ->set('tokens', $this->getUserTokens())
            ->display();
    }

    /**
     * Endpoint Documentation
     *
     * @return  void
     */
    public function endpointTask()
    {
        // build pathway
        $this->buildPathway();

        $active = Request::getString('active', '');

        // generate documentation
        $generator = new Generator(!Config::get('debug'));
        $documentation = $generator->output('array');

        if (!isset($documentation['sections'][$active])) {
            throw new \Exception(Lang::txt('Endpoint not found'), 404);
        }

        // render view
        $this->view
            ->set('active', $active)
            ->set('documentation', $documentation)
            ->set('tokens', $this->getUserTokens())
            ->display();
    }

    /**
     * Console
     *
     * @return  void
     */
    public function consoleTask()
    {
        // build pathway
        $this->buildPathway();

        // render view
        $this->view->display();
    }

    /**
     * Status
     *
     * @return  void
     */
    public function statusTask()
    {
        // build pathway
        $this->buildPathway();

        // render view
        $this->view->display();
    }

    /**
     * Build Pathway
     *
     * @return  void
     */
    private function buildPathway()
    {
        // create breadcrumbs
        if (Pathway::count() <= 0) {
            Pathway::append(
                Lang::txt(strtoupper($this->_option)),
                'index.php?option=' . $this->_option
            );
        }

        Pathway::append(
            Lang::txt(strtoupper($this->_option . '_' . $this->_controller)),
            'index.php?option=' . $this->_option . '&controller=' . $this->_controller
        );

        if (isset($this->_task) && $this->_task != '') {
            Pathway::append(
                Lang::txt(strtoupper($this->_option . '_' . $this->_controller . '_' . $this->_task)),
                'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task
            );
        }
    }

    /**
     * Get all active tokens for the current user
     *
     * @return  array
     */
    private function getUserTokens()
    {
        $tokens = array();

        if (!\Hubzero\Facades\User::isGuest()) {
            // Ensure model is loaded

            // Get all active tokens
            $tokens = \Components\Developer\Models\Accesstoken::all()
                ->whereEquals('uidNumber', \Hubzero\Facades\User::get('id'))
                ->where('expires', '>', \Hubzero\Utility\Date::of('now')->toSql())
                ->whereEquals('state', 1)
                ->order('created', 'desc')
                ->rows();
        }

        return $tokens;
    }
}
