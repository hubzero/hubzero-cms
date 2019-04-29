<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
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
				<a href="<?php echo Route::url($data->editUrl . '&action=deleteitem&aid=' . $data->id . '&p=' . $data->props); ?>" class="item-remove" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_REMOVE'); ?>">&nbsp;</a>
			</span>
		<?php } ?>
		</span>
		<span class="item-title data-type">
			 <a href="<?php echo $row->path; ?>" rel="external"><?php echo $title; ?></a>
			<span class="item-details"><?php echo $details; ?></span>
		</span>
	</li>