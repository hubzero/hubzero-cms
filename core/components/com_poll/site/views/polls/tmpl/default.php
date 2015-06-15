<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_JEXEC') or die('Restricted access');

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

<form action="<?php echo Route::url('index.php?option=com_poll&view=poll'); ?>" method="post" name="poll" id="poll">
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