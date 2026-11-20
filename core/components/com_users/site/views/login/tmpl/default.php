<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{user: mixed} $this
 */

if ($this->user->get('guest')) :
    // The user is not logged in.
    echo $this->loadTemplate('login');
else :
    // The user is already logged in.
        echo $this->loadTemplate('logout');
endif;
