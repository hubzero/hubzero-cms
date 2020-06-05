<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<div class="group-page group-page-notice notice-info">
	<h4><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_NOT_PUBLISHED'); ?></h4>
	<p><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_NOT_PUBLISHED_DESC'); ?></p>
	<?php
		$link = Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=publish&pageid='.$this->page->get('id'));
	?>
	<p><a href="<?php echo $link . '&return=' . base64_encode(Request::current(true)); ?>"><?php echo rtrim(Request::base(), '/') . $link; ?></a></p>
</div>