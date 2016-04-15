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

// no direct access
defined('_HZEXEC_') or die();

// Push the module CSS to the template
$this->css();

?>
<div<?php echo ($this->moduleclass) ? ' class="' . $this->moduleclass . '"' : '';?>>
	<?php if ($this->params->get('button_show_all', 1)) { ?>
	<ul class="module-nav">
		<li>
			<a class="icon-browse" href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=activity'); ?>">
				<?php echo Lang::txt('MOD_MYACTIVITY_ALL_ACTIVITY'); ?>
			</a>
		</li>
	</ul>
	<?php } ?>

	<?php if ($this->rows->count()) { ?>
		<ul class="compactlist" data-url="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=activity'); ?>">
			<?php
			foreach ($this->rows as $row)
			{
				require $this->getLayoutPath('default_item');
			}
			?>
		</ul>
	<?php } else { ?>
		<p class="no-results"><?php echo Lang::txt('MOD_MYACTIVITY_NO_RESULTS'); ?></p>
	<?php } ?>
</div>
