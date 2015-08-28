<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @author	Shawn Rice <zooley@purdue.edu>, Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('citations.css')
	 ->js();

$base = 'index.php?option=com_members&id=' . $this->member->get('uidNumber') . '&active=citations';

if (isset($this->messages))
{
	foreach ($this->messages as $message)
	{
		echo "<p class=\"{$message['type']}\">" . $message['message'] . "</p>";
	}
}
?>

<div id="content-header-extra"><!-- Citation management buttons -->
	<?php if ($this->isAdmin) : ?>
		<a class="btn icon-add" href="<?php echo Route::url($base. '&action=add'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SUBMIT_CITATION'); ?>
		</a>
		<a class="btn icon-upload" href="<?php echo Route::url($base. '&action=import'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_CITATION'); ?>
		</a>
		<a class="btn icon-settings" href="<?php echo Route::url($base. '&action=settings'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SET_FORMAT'); ?>
		</a>
	<?php endif; ?>
</div><!-- / Citations management buttons -->

<div id="intro-container">
<div id="citations-introduction">
	<div class="instructions">
	<h2 id="instructions-title"><?php echo Lang::txt('PLG_MEMBERS_CITATIONS'); ?></h2>
	<p id="noCitations"> <?php echo Lang::txt('PLG_MEMBERS_CITATIONS_NO_CITATIONS_FOUND'); ?></p>
	<?php if ($this->isAdmin): ?>
	<p id="who"><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_MEMBER_MAY'); ?></p>
	<ul>
		<li>
			<div class="instruction">
			<a class="btn icon-add" href="<?php echo Route::url($base. '&action=add'); ?>">
				<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SUBMIT_CITATION'); ?>
		</a>
		<span class="description"><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_MANUALLY_ENTER'); ?></span>
		</div>
	</li>
 		<li>
		<div class="instruction">
		<a class="btn icon-upload" href="<?php echo Route::url($base. '&action=import'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_CITATION'); ?>
		</a>
		<span class="description">  Import a list of citations.</span>
		</div>
		</li>
		<li>
		<span class="or">or</span>
		</li>
 		<li>
		<div class="instruction">
			<a class="btn icon-settings" href="<?php echo Route::url($base. '&action=settings'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SET_FORMAT'); ?>
		 </a>
		<span class="description"><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_DESCRIPTION'); ?></span>
		</div>
			</li>
 </ul>
 <?php endif; ?>
</div><!-- / .instructions -->
	<div class="questions">
	<p><strong><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_INTRO_WHAT_IS_THIS'); ?></strong></p>
	<p><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_INTRO_WHAT_IS_THIS_EXPLANATION'); ?></p>
	</div>
	</div>

</div> <!-- /#intro-container --> 
