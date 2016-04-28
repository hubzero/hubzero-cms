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

$base = $this->member->link() . '&active=' . $this->name;

$identifier = $this->post->item()->get('title');
if (!$identifier)
{
	$identifier = $this->post->item()->description('clean');
	if (!$identifier)
	{
		$identifier = '#' . $this->post->item()->get('id');
	}
}

$this->css();
?>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	<form action="<?php echo Route::url($base . '&task=post/' . $this->post->get('id') . '/delete'); ?>" method="post" id="hubForm" class="full">
		<fieldset>
			<legend><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE_HEADER'); ?></legend>

	 		<p class="warning"><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE_WARNING', $this->escape(stripslashes($identifier))); ?></p>

			<label>
				<input type="checkbox" class="option" name="confirmdel" value="1" />
				<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE_CONFIRM'); ?>
			</label>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="id" value="<?php echo $this->member->get('id'); ?>" />
		<input type="hidden" name="process" value="1" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="active" value="<?php echo $this->name; ?>" />
		<input type="hidden" name="action" value="delete" />
		<input type="hidden" name="post" value="<?php echo $this->escape($this->post->get('id')); ?>" />
		<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />

		<?php echo Html::input('token'); ?>

		<p class="submit">
			<input type="submit" class="btn btn-danger" value="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE'); ?>" />

			<?php if (!$this->no_html) { ?>
				<a class="btn btn-secondary" href="<?php echo Route::url($base . '&task=' . $this->collection->get('alias')); ?>"><?php echo Lang::txt('Cancel'); ?></a>
			<?php } ?>
		</p>
	</form>
