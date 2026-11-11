<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Module\Module $this
 */

if (!empty($version)) :
    echo '<p class="cms-version">' . $version . '</p>';
endif;
