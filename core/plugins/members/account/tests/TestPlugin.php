<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Members\Account\Tests;

/**
 * Plugin with the webdav mount pointed at a temporary directory and the path
 * helpers exposed, so they can be exercised without a hub
 */
class TestPlugin extends \Plugins\Members\Account\Account
{
    /**
     * Stand-in for /webdav/home
     *
     * @var  string
     */
    public $base = null;

    /**
     * The member the plugin is acting for
     *
     * Declared protected on the plugin; widened here so the test can set it
     * without a dispatcher, which is all newInstanceWithoutConstructor leaves.
     *
     * @var  object
     */
    public $member = null;

    /**
     * {@inheritdoc}
     */
    protected function webdavHome()
    {
        return $this->base;
    }

    /**
     * {@inheritdoc}
     */
    public function sshDirectory($create = true, &$error = null)
    {
        return parent::sshDirectory($create, $error);
    }

    /**
     * {@inheritdoc}
     */
    public function authorizedKeysPath($ssh, &$error = null)
    {
        return parent::authorizedKeysPath($ssh, $error);
    }

    /**
     * {@inheritdoc}
     */
    public function writeAuthorizedKeys($auth, $content)
    {
        return parent::writeAuthorizedKeys($auth, $content);
    }
}
