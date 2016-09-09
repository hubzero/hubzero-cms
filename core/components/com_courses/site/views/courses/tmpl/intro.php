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

$this->css('introduction', 'system')
     ->css('intro.css');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<?php if ($this->config->get('access-create-course')) { ?>
	<div id="content-header-extra">
		<p>
			<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=course&task=new'); ?>"><?php echo Lang::txt('COM_COURSES_CREATE_COURSE'); ?></a>
		</p>
	</div><!-- / #content-header-extra -->
	<?php } ?>
</header>

<?php
if (count($this->notifications) > 0)
{
	foreach ($this->notifications as $notification)
	{
		echo '<p class="' . $this->escape($notification['type']) . '">' . $notification['message'] . '</p>';
	}
}
?>

<section id="introduction" class="section">
	<form class="section-inner" action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=browse'); ?>" method="get">
		<div class="grid">
			<div class="col span8">
				<div class="container data-entry">
					<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('COM_COURSES_SEARCH'); ?>" />
					<fieldset class="entry-search">
						<label for="entry-search-field"><?php echo Lang::txt('COM_COURSES_SEARCH_INTRO_LABEL'); ?></label>
						<input type="text" name="search" id="entry-search-field" value="" placeholder="<?php echo Lang::txt('COM_COURSES_SEARCH_INTRO_PLACEHOLDER'); ?>" />
					</fieldset>
				</div><!-- / .container -->
				<p><?php echo Lang::txt('COM_COURSES_WATCH_LEARN_TEST_EARN'); ?></p>
				<p><a class="popup" href="<?php echo Route::url('index.php?option=com_help&component=' . substr($this->option, 4) . '&page=index'); ?>"><?php echo Lang::txt('COM_COURSES_HOW_COURSES_WORK'); ?></a></p>
			</div>
			<div class="col span3 offset1 omega">
				<div>
					<a class="btn icon-browse" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=browse'); ?>">
						<?php echo Lang::txt('COM_COURSES_BROWSE_CATALOG'); ?>
					</a>
				</div>
			</div>
		</div>
	</form>
</section><!-- / #introduction.section -->

<section class="section">
	<div class="section-inner">
	<?php if ($this->config->get("intro_popularcourses", 1)) : ?>
		<?php if (count($this->popularcourses) > 0) { ?>
			<div class="popularcourses">
				<?php
				$count = 0;
				foreach ($this->popularcourses as $course)
				{
					if ($count == 0)
					{
						echo '<div class="grid">';
					}
					$this->view('_course')
					     ->set('count', $count)
					     ->set('columns', 2)
					     ->set('course', $course)
					     ->display();

					if ($count == 2)
					{
						$count = 0;
						echo '</div>';
					}
					else
					{
						$count++;
					}
				}
				?>
			</div><!-- / .popularcourses clearfix top -->
		<?php } else { ?>
			<p class="info"><?php echo Lang::txt('COM_COURSES_NO_POPULAR_COURSES'); ?></p>
		<?php } ?>
	<?php endif; ?>
	</div>
</section><!-- / .section -->
