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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// add styles & scripts
$this->css()
     ->js()
     ->css('jquery.colpick.css', 'system')
     ->js('jquery.colpick', 'system');

$title = Lang::txt('COM_GROUPS_PAGES_ADD_CATEGORY');
if ($this->category->get('id'))
{
	$title = Lang::txt('COM_GROUPS_PAGES_EDIT_CATEGORY');
}
?>
<?php if (!Request::getInt('no_html', 0)) : ?>
<header id="content-header">
	<h2><?php echo Lang::txt($title); ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li><a class="icon-prev prev btn" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages#categories'); ?>">
				<?php echo Lang::txt('COM_GROUPS_ACTION_BACK_TO_MANAGE_PAGES'); ?>
			</a></li>
		</ul>
	</div>
</header>
<?php endif; ?>

<section class="main section">
	<?php foreach ($this->notifications as $notification) { ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $notification['message']; ?></p>
	<?php } ?>

	<form action="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=categories&task=savecategory'); ?>" method="POST" id="hubForm" class="full editcategory">
		<fieldset>
			<legend><?php echo Lang::txt('COM_GROUPS_PAGES_CATEGORY_DETAILS'); ?></legend>

			<label for="field-category-title">
				<strong><?php echo Lang::txt('COM_GROUPS_PAGES_CATEGORY_TITLE')?>:</strong> <span class="required"><?php echo Lang::txt('COM_GROUPS_FIELD_REQUIRED'); ?></span>
				<input type="text" name="category[title]" id="field-category-title" value="<?php echo $this->escape($this->category->get('title')); ?>" />
			</label>

			<label for="field-category-color">
				<strong><?php echo Lang::txt('COM_GROUPS_PAGES_CATEGORY_COLOR')?>:</strong> <span class="optional"><?php echo Lang::txt('COM_GROUPS_FIELD_OPTIONAL'); ?></span>
				<input type="text" maxlength="6" name="category[color]" id="field-category-color" value="<?php echo $this->escape($this->category->get('color')); ?>" />
			</label>
		</fieldset>

		<p class="submit">
			<button type="submit" class="btn btn-info save icon-save"><?php echo Lang::txt('COM_GROUPS_PAGES_SAVE_CATEGORY'); ?></button>
			<a href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages#categories'); ?>" class="btn cancel"><?php echo Lang::txt('COM_GROUPS_PAGES_CANCEL'); ?></a>
		</p>
		<input type="hidden" name="option" value="com_groups" />
		<input type="hidden" name="controller" value="categories" />
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="category[id]" value="<?php echo $this->category->get('id'); ?>" />
	</form>
</section>