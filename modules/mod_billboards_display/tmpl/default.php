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

// No direct access
defined('_HZEXEC_') or die();
?>

<?php foreach ($this->slides as $slide) { ?>

	<div class="item cf">
		<div class="cf">
			<div class="thumb">
				<img src="<?php echo $this->image_location; ?><?php echo $slide->background_img; ?>"/>
			</div>
			<div class="content">

				<?php
				if (!empty($slide->header))
				{
					?>
					<h3>
						<?php
						if (!empty($slide->learn_more_target))
						{
							echo '<a href="' . $slide->learn_more_target . '">';
						}
						?>

						<?php echo $slide->header; ?>

						<?php
						if (!empty($slide->learn_more_target))
						{
							echo '</a>';
						}
						?>
					</h3>
					<?php
				}
				?>
			</div>
		</div>

		<div class="main">

			<?php
			if (empty($slide->header) && !empty($slide->learn_more_target))
			{
				echo '<a href="' . $slide->learn_more_target . '">';
			}
			?>
			<?php echo $slide->text; ?>
			<?php
			if (empty($slide->header) && !empty($slide->learn_more_target))
			{
				echo '</a>';
			}
			?>

			<?php
			if (!empty($slide->learn_more_text) && !empty($slide->learn_more_target))
			{?>

				<p class="learn-more"><a class="<?php echo $slide->learn_more_class; ?>" href="<?php echo $slide->learn_more_target; ?>">
						<?php echo $slide->learn_more_text; ?>
					</a></p>

				<?php
			}
			?>
		</div>
	</div>
<?php } ?>