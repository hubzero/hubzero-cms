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

defined('_HZEXEC_') or die();

$this->css('poll_bars.css');
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_POLL'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-stats btn" href="<?php echo Route::url('index.php?option=com_poll&view=latest'); ?>">
				<?php echo Lang::txt('COM_POLL_TAKE_LATEST_POLL'); ?>
			</a>
		</p>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<form action="<?php echo Route::url('index.php?option=com_poll&view=poll'); ?>" method="get" name="poll" id="poll">
	<section class="main section">
		<label for="id">
			<?php echo Lang::txt('COM_POLL_SELECT'); ?>
			<?php echo $this->lists['polls']; ?>
		</label>
	</section>
	<section class="below section">
		<?php
		$this->view('default_graph')
			->set('first_vote', $this->first_vote)
			->set('last_vote', $this->last_vote)
			->set('lists', $this->lists)
			->set('params', $this->params)
			->set('poll', $this->poll)
			->set('votes', $this->votes)
			->display();
		?>
	</section><!-- / .main section -->
</form>