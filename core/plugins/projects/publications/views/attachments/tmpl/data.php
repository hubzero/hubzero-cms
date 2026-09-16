<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$data    = $this->data;
$row     = $this->data->row;
$title   = $row->title ? $row->title : $row->path;
$details = $row->title ? $row->object_name : null;
$viewer  = $this->data->viewer;

?>
	<li>
		<span class="item-options">
		<?php if ($viewer == 'edit') { ?>
			<span>
				<form class="inline-form" method="post" action="<?php echo Route::url($data->editUrl . '&action=deleteitem&aid=' . $data->id . '&p=' . $data->props); ?>">
					<?php echo Html::input('token'); ?>
					<button type="submit" class="item-remove" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_REMOVE'); ?>">&nbsp;</button>
				</form>
			</span>
		<?php } ?>
		</span>
		<span class="item-title data-type">
			 <a href="<?php echo (preg_match('#^(https?://|dataviewer/)#i', $row->path) ? $this->escape($row->path) : ''); ?>" rel="external"><?php echo $this->escape($title); ?></a>
			<span class="item-details"><?php echo $this->escape($details); ?></span>
		</span>
	</li>