<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$showwarning = ($this->version=='current' or !$this->status['published']) ? 0 : 1;

?>
	<div class="explaination">
		<h4><?php echo Lang::txt('COM_TOOLS_TAGS_WHAT_ARE_TAGS'); ?></h4>
		<p><?php echo Lang::txt('COM_TOOLS_TAGS_EXPLANATION'); ?></p>
	</div>
	<fieldset>
		<legend><?php echo Lang::txt('COM_TOOLS_TAGS_ADD'); ?></legend>
<?php if (!empty($this->fats)) { ?>
		<fieldset>
			<legend><?php echo Lang::txt('COM_TOOLS_TAGS_SELECT_FOCUS_AREA'); ?>:</legend>
			<?php
			foreach ($this->fats as $key => $value)
			{
				?>
				<label>
					<input class="option" type="radio" name="tagfa" value="<?php echo $value; ?>"<?php if ($this->tagfa == $value) { echo ' checked="checked "'; } ?> />
					<?php echo $key; ?>
				</label>
				<?php
			}
			?>
		</fieldset>
<?php } ?>
		<label>
			<?php echo Lang::txt('COM_TOOLS_TAGS_ASSIGNED'); ?>:
			<?php
			$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $this->tags)));

			if (count($tf) > 0) {
				echo $tf[0];
			} else {
				echo '<textarea name="tags" id="tags-men" rows="6" cols="35">'. $this->tags .'</textarea>';
			}
			?>
		</label>
		<p><?php echo Lang::txt('COM_TOOLS_TAGS_NEW_EXPLANATION'); ?></p>
	</fieldset><div class="clear"></div>