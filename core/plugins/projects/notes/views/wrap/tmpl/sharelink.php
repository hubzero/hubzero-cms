<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$stamp = $this->publicStamp ? $this->publicStamp->stamp : null;

if ($stamp) { ?>
	<p class="publink">
		<?php echo Lang::txt('COM_PROJECTS_NOTES_PUB_LINK') . ' <a href="' . trim(Request::base(), DS) . Route::url('index.php?option=' . $this->option . '&action=get') . '?s=' . $stamp .'" rel="external">' . trim(Request::base(), DS) . Route::url('index.php?option=' . $this->option . '&action=get&s=' . $stamp) . '</a>'; ?>
		<?php if ($this->project->isPublic()) {
			$act = $this->publicStamp->listed ? 'unlist' : 'publist'; ?>
		<span><?php echo Lang::txt('COM_PROJECTS_NOTES_THIS_PAGE_IS'); ?> <strong class="<?php echo $this->publicStamp->listed ? 'green' : 'urgency'; ?>"><?php echo $this->publicStamp->listed ? Lang::txt('COM_PROJECTS_NOTES_LISTED') : Lang::txt('COM_PROJECTS_NOTES_UNLISTED'); ?></strong>. <a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->project->get('alias') . '&active=notes&p=' . $this->page->get('id')) . '&amp;action=share'; ?>" class="showinbox"><?php echo Lang::txt('COM_PROJECTS_NOTES_SHARE_SETTINGS'); ?> &rsaquo;</a></span>
		<?php } ?>
	</p>
<?php } else { ?>
	<p class="publink"><?php echo Lang::txt('COM_PROJECTS_NOTES_SHARE_GET_LINK'); ?> <a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->project->get('alias') . '&active=notes&p=' . $this->page->get('id')) . '&amp;action=share'; ?>" class="showinbox"><?php echo Lang::txt('COM_PROJECTS_NOTES_SHARE_GENERATE_LINK'); ?></a></p>
<?php }
