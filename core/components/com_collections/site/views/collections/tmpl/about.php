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

$this->css()
     ->css('help.css');

$base = 'index.php?option=' . $this->option;
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_COLLECTIONS'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-info btn popup" href="<?php echo Route::url('index.php?option=com_help&component=' . substr($this->option, 4) . '&page=index'); ?>">
				<span><?php echo Lang::txt('COM_COLLECTIONS_GETTING_STARTED'); ?></span>
			</a>
		</p>
	</div>
</header>

<form method="get" action="<?php echo Route::url($base . '&controller=' . $this->controller . '&task=posts'); ?>" id="collections">
	<?php
	$this->view('_submenu')
	     ->set('option', $this->option)
	     ->set('active', 'about')
	     ->set('collections', $this->collections)
	     ->set('posts', $this->total)
	     ->display();
	?>

	<section class="main section about">

		<p class="tagline"><?php echo Lang::txt('COM_COLLECTIONS_TAGLINE'); ?></p>

		<div class="about-odd posts">
			<h3><?php echo Lang::txt('COM_COLLECTIONS_POST'); ?></h3>
			<p>
				<?php echo Lang::txt('COM_COLLECTIONS_POST_EXPLANATION'); ?>
			</p>
		</div>

		<div class="about-even collections">
			<h3><?php echo Lang::txt('COM_COLLECTIONS_COLLECTION'); ?></h3>
			<p>
				<?php echo Lang::txt('COM_COLLECTIONS_COLLECTION_EXPLANATION'); ?>
			</p>
		</div>

		<div class="about-odd following">
			<h3><?php echo Lang::txt('COM_COLLECTIONS_FOLLOW'); ?></h3>
			<p>
				<?php echo Lang::txt('COM_COLLECTIONS_FOLLOW_EXPLANATION', Route::url('index.php?option=com_members&task=myaccount/collections'), Route::url('index.php?option=com_members&task=myaccount'), Route::url('index.php?option=com_members&task=myaccount/collections')); ?>
			</p>
		</div>

		<div class="about-even unfollowing">
			<h3><?php echo Lang::txt('COM_COLLECTIONS_UNFOLLOW'); ?></h3>
			<p>
				<?php echo Lang::txt('COM_COLLECTIONS_UNFOLLOW_EXPLANATION', Route::url('index.php?option=com_members&task=myaccount/collections')); ?>
			</p>
		</div>

		<div class="about-odd livefeed">
			<h3><?php echo Lang::txt('COM_COLLECTIONS_LIVE_FEED'); ?></h3>
			<p>
				<?php echo Lang::txt('COM_COLLECTIONS_LIVE_FEED_EXPLANATION', Route::url('index.php?option=com_members&task=myaccount/collections')); ?>
			</p>
		</div>

	</section>
</form>