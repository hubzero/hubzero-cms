<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// Initiasile related data.
require_once Component::path('com_menus') . '/helpers/menus.php';
$menuTypes = Components\Menus\Helpers\Menus::getMenuLinks();

$assignment = $this->item->disableCaching()->purgeCache()->menuAssignment();
?>
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_MODULES_MENU_ASSIGNMENT'); ?></span></legend>

			<div class="input-wrap">
				<label id="jform_menus-lbl" for="jform_assignment"><?php echo Lang::txt('COM_MODULES_MODULE_ASSIGN'); ?></label>
			<!-- <fieldset id="jform_menus" class="radio"> -->
				<select name="menu[assignment]" id="jform_assignment">
					<?php echo Html::select('options', Components\Modules\Helpers\Modules::getAssignmentOptions($this->item->client_id), 'value', 'text', $assignment, true);?>
				</select>
			<!-- </fieldset> -->
			</div>

			<div class="input-wrap">
				<label id="jform_menuselect-lbl" for="jform_menuselect"><?php echo Lang::txt('JGLOBAL_MENU_SELECTION'); ?></label>

				<button type="button" class="jform-assignments-button jform-rightbtn" onclick="$('.chkbox').each(function(i, el) { el.checked = !el.checked; });">
					<?php echo Lang::txt('JGLOBAL_SELECTION_INVERT'); ?>
				</button>

				<button type="button" class="jform-assignments-button jform-rightbtn" onclick="$('.chkbox').each(function(i, el) { el.checked = false; });">
					<?php echo Lang::txt('JGLOBAL_SELECTION_NONE'); ?>
				</button>

				<button type="button" class="jform-assignments-button jform-rightbtn" onclick="$('.chkbox').each(function(i, el) { el.checked = true; });">
					<?php echo Lang::txt('JGLOBAL_SELECTION_ALL'); ?>
				</button>
			</div>

			<div class="clr"></div>

			<div id="menu-assignment">

			<?php echo Html::tabs('start', 'module-menu-assignment-tabs', array('useCookie' => 1));?>

			<?php foreach ($menuTypes as &$type) :
				echo Html::tabs('panel', $type->title ? $type->title : $type->menutype, $type->menutype.'-details');

				$chkbox_class = 'chk-menulink-' . $type->id; ?>

				<button type="button" class="jform-assignments-button jform-rightbtn" onclick="$('.<?php echo $chkbox_class; ?>').each(function(i, el) { el.checked = !el.checked; });">
					<?php echo Lang::txt('JGLOBAL_SELECTION_INVERT'); ?>
				</button>

				<button type="button" class="jform-assignments-button jform-rightbtn" onclick="$('.<?php echo $chkbox_class; ?>').each(function(i, el) { el.checked = false; });">
					<?php echo Lang::txt('JGLOBAL_SELECTION_NONE'); ?>
				</button>

				<button type="button" class="jform-assignments-button jform-rightbtn" onclick="$('.<?php echo $chkbox_class; ?>').each(function(i, el) { el.checked = true; });">
					<?php echo Lang::txt('JGLOBAL_SELECTION_ALL'); ?>
				</button>

				<div class="clr"></div>

				<?php
				$count = count($type->links);
				$i     = 0;
				if ($count) :
				?>
				<ul class="menu-links">
					<?php
					foreach ($type->links as $link) :
						if (trim($assignment) == '-'):
							$checked = '';
						elseif ($assignment == 0):
							$checked = ' checked="checked"';
						elseif ($assignment < 0):
							$checked = in_array(-$link->value, $this->item->menuAssigned()) ? ' checked="checked"' : '';
						elseif ($assignment > 0) :
							$checked = in_array($link->value, $this->item->menuAssigned()) ? ' checked="checked"' : '';
						endif;
					?>
					<li class="menu-link">
						<input type="checkbox" class="chkbox <?php echo $chkbox_class; ?>" name="menu[assigned][]" value="<?php echo (int) $link->value;?>" id="link<?php echo (int) $link->value;?>"<?php echo $checked;?>/>
						<label for="link<?php echo (int) $link->value;?>">
							<?php echo $link->text; ?>
						</label>
					</li>
					<?php if ($count > 20 && ++$i == ceil($count/2)) :?>
					</ul><ul class="menu-links">
					<?php endif; ?>
					<?php endforeach; ?>
				</ul>
				<div class="clr"></div>
				<?php endif; ?>
			<?php endforeach; ?>

			<?php echo Html::tabs('end');?>

			</div>
		</fieldset>
