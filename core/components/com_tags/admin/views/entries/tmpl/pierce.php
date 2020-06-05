<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = Components\Tags\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_TAGS') . ': ' . Lang::txt('COM_TAGS_PIERCE'), 'tags');
if ($canDo->get('core.edit'))
{
	Toolbar::save('pierce');
}
Toolbar::cancel();

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" class="editform" id="item-form">
	<p class="warning"><?php echo Lang::txt('COM_TAGS_PIERCED_EXPLANATION'); ?></p>

	<div class="grid">
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_TAGS_PIERCING'); ?></span></legend>

				<div class="input-wrap">
					<ul>
						<?php
						foreach ($this->tags as $tag)
						{
							echo '<li>' . $this->escape(stripslashes($tag->get('raw_tag'))) . ' (' . $this->escape($tag->get('tag')) . ' - ' . $tag->objects()->total() . ')</li>' . "\n";
						}
						?>
					</ul>
				</div>
			</fieldset>
		</div>
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_TAGS_PIERCE_TO'); ?></span></legend>

				<div class="input-wrap">
					<label for="newtag"><?php echo Lang::txt('COM_TAGS_NEW_TAG'); ?>:</label><br />
					<?php
					$tf = Event::trigger(
						'hubzero.onGetMultiEntry',
						array(
							array('tags', 'newtag', 'newtag')
						)
					);
					echo (count($tf)) ? implode("\n", $tf) : '<input type="text" name="newtag" id="newtag" size="25" value="" />';
					?>
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="ids" value="<?php echo $this->idstr; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="step" value="<?php echo $this->step; ?>" />
	<input type="hidden" name="task" value="pierce" />

	<?php echo Html::input('token'); ?>
</form>