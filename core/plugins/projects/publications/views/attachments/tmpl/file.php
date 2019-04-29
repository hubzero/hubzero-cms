<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$data = $this->data;
$pub  = $data->get('pub');

$details = $data->get('localPath');
$details.= $data->getSize() ? ' | ' . $data->getSize('formatted') : '';
if ($data->get('viewer') != 'freeze')
{
	$details.= !$data->exists() ? ' | ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_MISSING_FILE') : '';
}
?>
	<li>
		<span class="item-options">
			<?php if ($data->get('viewer') == 'edit') { ?>
			<span>
				<?php if ($data->exists()) { ?>
				<a href="<?php echo $data->get('downloadUrl'); ?>" class="item-download" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DOWNLOAD'); ?>">&nbsp;</a>
				<?php } ?>
				<a href="<?php echo Route::url($pub->link('editversion') . '&action=edititem&aid=' . $data->get('id') . '&p=' . $data->get('props')); ?>" class="showinbox item-edit" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_RELABEL'); ?>">&nbsp;</a>
				<a href="<?php echo Route::url($pub->link('editversion') . '&action=deleteitem&aid=' . $data->get('id') . '&p=' . $data->get('props')); ?>" class="item-remove" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_REMOVE'); ?>">&nbsp;</a>
			</span>
			<?php } elseif ($data->exists()) { ?>
				<span><a href="<?php echo $data->get('downloadUrl'); ?>" class="item-download" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DOWNLOAD'); ?>">&nbsp;</a></span>
			<?php } ?>
		</span>
		<span class="item-title" id="<?php echo 'file-'.$data->get('id'); ?>">
			<?php echo $data::drawIcon($data->get('ext')); ?> <?php echo $data->get('title'); ?>
		</span>
		<span class="item-details"><?php echo $details; ?></span>
	</li>
