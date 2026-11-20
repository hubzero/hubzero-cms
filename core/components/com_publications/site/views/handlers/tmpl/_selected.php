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
 * @var \Hubzero\Component\View&object{configs: mixed, handler: mixed} $this
 */
?>
<div class="handlertype-<?php echo $this->handler->getName(); ?>">
    <h3><?php echo $this->escape($this->configs->label); ?></h3>
    <p><?php echo $this->configs->about; ?></p>
</div>