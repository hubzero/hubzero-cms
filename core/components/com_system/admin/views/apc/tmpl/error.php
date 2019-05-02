<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SYSTEM_APC'), 'config.png');

$this->css('apc.css');

if ($this->getError()) { ?>
<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php }
