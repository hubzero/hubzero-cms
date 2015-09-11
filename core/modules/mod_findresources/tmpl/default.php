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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<div<?php echo ($this->params->get('cssId')) ? ' id="' . $this->params->get('cssId') . '"' : ''; ?>>
	<form action="<?php echo Route::url('index.php?option=com_search'); ?>" method="get" class="search">
		<fieldset>
			<p>
				<label for="rsearchword"><?php echo Lang::txt('MOD_FINDRESOURCES_SEARCH_LABEL'); ?></label>
				<input type="text" name="terms" id="rsearchword" value="" />
				<input type="hidden" name="domain" value="resources" />
				<input type="submit" value="<?php echo Lang::txt('MOD_FINDRESOURCES_SEARCH'); ?>" />
			</p>
		</fieldset>
	</form>
<?php if (count($this->tags) > 0) { ?>
	<ol class="tags">
		<li><?php echo Lang::txt('MOD_FINDRESOURCES_POPULAR_TAGS'); ?></li>
	<?php foreach ($this->tags as $tag) { ?>
		<li><a href="<?php echo Route::url('index.php?option=com_tags&tag=' . $tag->tag); ?>"><?php echo $this->escape(stripslashes($tag->raw_tag)); ?></a></li>
	<?php } ?>
		<li><a href="<?php echo Route::url('index.php?option=com_tags'); ?>" class="showmore"><?php echo Lang::txt('MOD_FINDRESOURCES_MORE_TAGS'); ?></a></li>
	</ol>
<?php } else { ?>
	<p><?php echo Lang::txt('MOD_FINDRESOURCES_NO_TAGS'); ?></p>
<?php } ?>

<?php if (count($this->categories) > 0) { ?>
	<p>
<?php
	$i = 0;
	foreach ($this->categories as $category)
	{
		$i++;
		$normalized = preg_replace("/[^a-zA-Z0-9]/", '', strtolower($category->type));

		if (substr($normalized, -3) == 'ies') {
			$cls = $normalized;
		} else {
			$cls = substr($normalized, 0, -1);
		}
?>
		<a href="<?php echo Route::url('index.php?option=com_resources&type=' . $normalized); ?>"><?php echo $this->escape(stripslashes($category->type)); ?></a><?php echo ($i == count($this->categories)) ? '...' : ', '; ?>
<?php
	}
?>
		<a href="<?php echo Route::url('index.php?option=com_resources'); ?>" class="showmore"><?php echo Lang::txt('MOD_FINDRESOURCES_ALL_CATEGORIES'); ?></a>
	</p>
<?php
}
?>
	<div class="uploadcontent">
		<h4><?php echo Lang::txt('MOD_FINDRESOURCES_UPLOAD_CONTENT'); ?> <span><a href="<?php echo Route::url('index.php?option=com_resources&task=new'); ?>" class="contributelink"><?php echo Lang::txt('MOD_FINDRESOURCES_GET_STARTED'); ?></a></span></h4>
	</div>
</div><!-- / <?php echo ($this->params->get('cssId')) ? '#' . $this->params->get('cssId') : ''; ?> -->
