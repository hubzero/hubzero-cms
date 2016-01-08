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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$width  = '780';
$height = '460';

$source = with(new \Hubzero\Content\Moderator($this->compiled->getAbsolutePath()))->getUrl();
?>

<?php if ($this->getError()) : ?>
	<h3>
		<?php echo Lang::txt('PLG_HANDLERS_LATEX_PREVIEW_FAILED'); ?>
	</h3>
	<p class="witherror">
		<?php echo $this->getError(); ?>
		<pre>
			<?php if (!empty($this->log)) : ?>
				<?php echo $this->log; ?>
			<?php endif ; ?>
		</pre>
	</div>
<?php endif; ?>

<div id="compiled-doc" embed-src="<?php echo $source; ?>" embed-width="<?php echo $width; ?>" embed-height="<?php echo $height; ?>">
	<object class="width-container" width="<?php echo $width; ?>" height="<?php echo $height; ?>" type="<?php echo $this->compiled->getMimetype(); ?>" data="<?php echo $source; ?>" id="pdf_content">
		<embed src="<?php echo $source; ?>" type="<?php echo $this->compiled->getMimetype(); ?>" />
	</object>
</div>