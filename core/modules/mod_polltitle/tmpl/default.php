<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Module\Module&object{poll: mixed} $this
 */

if ($this->poll->title) {
    echo stripslashes($this->poll->title);
} else {
    echo stripslashes($this->params->get('message'));
}
