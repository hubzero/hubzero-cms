<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

if (count($this->team) > 0) {
?>
	<ul class="team-selector" id="team-selector">
		<?php foreach ($this->team as $owner)
		{
			// Get profile thumb image
			$profile = User::getInstance($owner->userid);
			$actor   = User::getInstance(User::get('id'));
			$thumb   = $profile->get('id') ? $profile->picture() : $actor->picture(true);

			$org  = $owner->a_organization ? $owner->a_organization : $owner->organization;
			$name = $owner->a_name ? $owner->a_name : $owner->fullname;
			$name = trim($name) ? $name : $owner->invited_email;

			$username = $owner->username ? $owner->username : Lang::txt('PLG_PROJECTS_TEAM_SELECTOR_AUTHOR_UNCONFIRMED');

			// Already an author?
			$selected = !empty($this->selected) && in_array($owner->id, $this->selected) ? 1 : 0;
			$class = $selected ? '' : 'allowed';

			?>
			<li id="author-<?php echo $owner->id; ?>" class="type-author <?php echo $class; ?> <?php if ($selected) { echo ' selectedfilter preselected'; } ?>">
				<span class="item-info"><?php echo $org; ?></span>
				<img width="30" height="30" src="<?php echo $thumb; ?>" class="a-ima" alt="<?php echo htmlentities($name); ?>" />
				<span class="a-name"><?php echo $name; ?>
					<span class="a-username">(<?php echo $username; ?>)</span>
				</span>
			</li>
		<?php } ?>
	</ul>
<?php } else {  ?>
	<p class="noresults"><?php echo Lang::txt('PLG_PROJECTS_TEAM_SELECTOR_NO_MEMBERS'); ?></p>
<?php } ?>
