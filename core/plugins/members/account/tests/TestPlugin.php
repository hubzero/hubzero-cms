<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     * @var  string|null
     */
    public $base = null;

    /**
     * The member the plugin is acting for
     *
     * Declared protected on the plugin; widened here so the test can set it
     * without a dispatcher, which is all newInstanceWithoutConstructor leaves.
     *
     * @var  \Components\Members\Models\Member
     */
    public $member;

    /**
     * {@inheritdoc}
     */
    protected function webdavHome()
    {
        return (string) $this->base;
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
