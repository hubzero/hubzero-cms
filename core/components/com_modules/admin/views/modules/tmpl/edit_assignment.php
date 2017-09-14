<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// Initiasile related data.
require_once PATH_CORE.'/components/com_menus/admin/helpers/menus.php';
$menuTypes = MenusHelper::getMenuLinks();
?>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				validate();
				$('select').on('change', function(e){validate();});
			});
			function validate(){
				var value = $('#jform_assignment').val(),
					list  = $('#menu-assignment');

				if (value == '-' || value == '0') {
					$('.jform-assignments-button').each(function(i, el) {
						$(el).prop('disabled', true);
					});
					list.find('input').each(function(i, el){
						$(el).prop('disabled', true);
						if (value == '-'){
							$(el).prop('checked', false);
						} else {
							$(el).prop('checked', true);
						}
					});
				} else {
					$('.jform-assignments-button').each(function(i, el) {
						$(el).prop('disabled', false);
					});
					list.find('input').each(function(i, el){
						$(el).prop('disabled', false);
					});
				}
			}
		</script>

		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_MODULES_MENU_ASSIGNMENT'); ?></span></legend>

			<div class="input-wrap">
				<label id="jform_menus-lbl" for="jform_assignment"><?php echo Lang::txt('COM_MODULES_MODULE_ASSIGN'); ?></label>
			<!-- <fieldset id="jform_menus" class="radio"> -->
				<select name="jform[assignment]" id="jform_assignment">
					<?php echo Html::select('options', Components\Modules\Helpers\Modules::getAssignmentOptions($this->item->client_id), 'value', 'text', $this->item->assignment, true);?>
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

			<?php echo Html::tabs('start', 'module-menu-assignment-tabs', array('useCookie'=>1));?>

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
						if (trim($this->item->assignment) == '-'):
							$checked = '';
						elseif ($this->item->assignment == 0):
							$checked = ' checked="checked"';
						elseif ($this->item->assignment < 0):
							$checked = in_array(-$link->value, $this->item->assigned) ? ' checked="checked"' : '';
						elseif ($this->item->assignment > 0) :
							$checked = in_array($link->value, $this->item->assigned) ? ' checked="checked"' : '';
						endif;
					?>
					<li class="menu-link">
						<input type="checkbox" class="chkbox <?php echo $chkbox_class; ?>" name="jform[assigned][]" value="<?php echo (int) $link->value;?>" id="link<?php echo (int) $link->value;?>"<?php echo $checked;?>/>
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
