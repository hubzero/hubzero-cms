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

$base = rtrim(Request::base(true), '/');

if ($this->quote)
{
	?>
	<div class="<?php echo $this->module->module; ?>"<?php if ($this->params->get('moduleid')) { echo ' id="' . $this->params->get('moduleid') . '"'; } ?>>
		<blockquote cite="<?php echo $this->escape(stripslashes($this->quote->get('fullname'))); ?>">
			<p>
				<?php
				$text = stripslashes($this->escape($this->quote->get('quote'))) . ' ';
				$text = substr($text, 0, $this->charlimit);
				$text = substr($text, 0, strrpos($text, ' '));

				echo $text;
				?>
				<?php if (strlen($this->quote->get('quote')) > $this->charlimit) { ?>
					<a href="<?php echo $base; ?>/about/quotes/?quoteid=<?php echo $this->quote->get('id'); ?>" title="<?php echo Lang::txt('MOD_RANDOMQUOTE_VIEW_FULL', $this->escape(stripslashes($this->quote->get('fullname')))); ?>" class="showfullquote">
						<?php echo Lang::txt('MOD_RANDOMQUOTE_VIEW'); ?>
					</a>
				<?php } ?>
			</p>
		</blockquote>
		<p class="cite">
			<cite><?php echo $this->escape(stripslashes($this->quote->get('fullname'))); ?></cite>,
			<?php echo $this->escape(stripslashes($this->quote->get('org'))); ?>
			<span>-</span>
			<span><?php echo Lang::txt('MOD_RANDOMQUOTE_IN', $base . '/about/quotes'); ?></span>
		</p>
	</div>
	<?php
}