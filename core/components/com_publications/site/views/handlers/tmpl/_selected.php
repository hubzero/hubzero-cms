<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{configs: mixed, handler: mixed} $this
 */
?>
<div class="handlertype-<?php echo $this->handler->get('_name'); ?>">
    <h3><?php echo $this->escape($this->configs->label); ?></h3>
    <p><?php echo $this->configs->about; ?></p>
</div>