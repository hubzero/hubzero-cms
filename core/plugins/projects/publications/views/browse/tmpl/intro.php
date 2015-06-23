<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<div id="pubintro">
	<h3><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_HOW_IT_WORKS'); ?> <?php if ($this->pub->config('documentation')) { ?>
	<span class="learnmore"><a href="<?php echo $this->pub->config('documentation'); ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_LEARN_MORE'); ?> &raquo;</a></span>
	<?php } ?></h3>

	<div class="columns three first">
		<h4><span class="num">1</span> <?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_INTRO_STEP_ONE'); ?></h4>
		<p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_INTRO_STEP_ONE_ABOUT'); ?></p>
	</div>
	<div class="columns three second">
		<h4><span class="num">2</span> <?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_INTRO_STEP_TWO'); ?></h4>
		<p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_INTRO_STEP_TWO_ABOUT'); ?></p>
	</div>
	<div class="columns three third">
		<h4><span class="num">3</span> <?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_INTRO_STEP_THREE'); ?></h4>
		<p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_INTRO_STEP_THREE_ABOUT'); ?></p>
	</div>
	<div class="clear"></div>
</div>