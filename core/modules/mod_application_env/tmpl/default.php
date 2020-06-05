<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die;

$this->css();
?>
<div id="system-environment">
	<p class="<?php echo strtolower($this->environment); ?>"><?php echo Lang::txt('MOD_APPLICATION_ENV_' . strtoupper($this->environment)); ?></p>
</div>