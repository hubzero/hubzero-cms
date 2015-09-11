<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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