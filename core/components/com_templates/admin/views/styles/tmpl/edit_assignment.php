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
		<fieldset class="adminform">
			<legend><?php echo Lang::txt('COM_TEMPLATES_MENUS_ASSIGNMENT'); ?></legend>
			<label id="jform_menuselect-lbl" for="jform_menuselect"><?php echo Lang::txt('JGLOBAL_MENU_SELECTION'); ?></label>

			<button type="button" class="jform-rightbtn" onclick="$('.chk-menulink').each(function(i, el) { el.checked = !el.checked; });">
				<?php echo Lang::txt('JGLOBAL_SELECTION_INVERT'); ?>
			</button>
			<div class="clr"></div>

			<div id="menu-assignment">
				<?php echo Html::tabs('start', 'module-menu-assignment-tabs', array('useCookie'=>1));?>
				<?php foreach ($menuTypes as &$type) : ?>
					<?php echo Html::tabs('panel', $type->title ? $type->title : $type->menutype, $type->menutype.'-details'); ?>
					<ul class="menu-links">
						<h3><?php echo $type->title ? $type->title : $type->menutype; ?></h3>
						<?php foreach ($type->links as $link) :?>
						<li class="menu-link">
							<input type="checkbox" name="jform[assigned][]" value="<?php echo (int) $link->value;?>" id="link<?php echo (int) $link->value;?>"<?php if ($link->template_style_id == $this->item->id):?> checked="checked"<?php endif;?><?php if ($link->checked_out && $link->checked_out != User::get('id')):?> disabled="disabled"<?php else:?> class="chk-menulink "<?php endif;?> />
							<label for="link<?php echo (int) $link->value;?>" >
								<?php echo $link->text; ?>
							</label>
						</li>
						<?php endforeach; ?>
					</ul>
					<div class="clr"></div>
				<?php endforeach; ?>
				<?php echo Html::tabs('end');?>
			</div>
		</fieldset>
