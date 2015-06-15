<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_JEXEC') or die('Restricted access');

$plugins = Event::trigger('courses.onCourse', array(
	$this->course,
	$this->offering,
	true
));
?>
<div id="guide-overlay" class="guide-wrap" data-action="<?php echo Route::url($this->offering->link() . '&active=' . $this->plugin . '&unit=mark'); ?>">
	<div class="guide-content">

		<div class="grid">
			<div class="col span-half">
				<div class="guide-nav">
					<ul>
						<?php
						foreach ($plugins as $k => $plugin)
						{
							//do we want to show category in menu?
							if (!$plugin->get('display_menu_tab'))
							{
								continue;
							}
							?>
							<li>
								<strong class="<?php echo $plugin->get('name'); ?>"><?php echo $plugin->get('title'); ?></strong> <span><?php echo Lang::txt('PLG_COURSES_' . strtoupper($plugin->get('name')) . '_BLURB'); ?></span>
							</li>
							<?php
						}
						?>
					</ul>
				</div>
			</div>
			<div class="col span-half omega">
				<div class="guide-about">
					<h3><?php echo Lang::txt('Welcome to the course!'); ?></h3>
					<p><?php echo Lang::txt('We\'ve tried to organize things to group related content and make it easier to find what you need. Feel free to explore the various menu options.'); ?></p>
					<p><?php echo Lang::txt('You can always get back to the %s by clicking the link found under the title of this course.', '<a href="' . Route::url($this->course->link()) . '">Course overview</a>'); ?></p>
					<p class="guide-dismiss">
						<?php echo Lang::txt('Click anywhere to dismiss this guide and get started!'); ?>
					</p>
				</div>

				<div class="guide-onemorething">
					<p><?php echo Lang::txt('Oh, and one more thing:'); ?></p>
					<p class="guide-luck"><?php echo Lang::txt('Good Luck!'); ?></p>
				</div>
			</div>
		</div>

	</div><!-- / .guide-content -->
</div><!-- / .guide-wrap -->