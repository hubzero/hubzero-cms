<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$html = \Modules\QuickIcon\Icons::buttons($buttons);

if (!empty($html)): ?>
	<?php $this->css(); ?>
	<div class="cpanel <?php echo $this->module->module; ?>"><?php echo $html; ?></div>
<?php endif;
