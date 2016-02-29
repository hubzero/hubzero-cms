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

?>
<div class="public-list-header">
	<h3><?php echo Lang::txt('PLG_PROJECTS_TEAM_TEAM'); ?></h3>
</div>
<div id="team-horiz" class="public-list-wrap">
	<?php if (count($this->team) > 0) { ?>
		<ul>
			<?php foreach ($this->team as $owner)
			{
				if (!$owner->userid || $owner->status != 1)
				{
					continue;
				}
				// Get profile thumb image
				$profile = \Hubzero\User\Profile::getInstance($owner->userid);
				$actor   = \Hubzero\User\Profile::getInstance(User::get('id'));
				if (!$actor)
				{
					$actor = new \Hubzero\User\Profile();
				}
				$thumb   = $profile ? $profile->getPicture() : $actor->getPicture(true);
			?>
			<li>
				<img width="50" height="50" src="<?php echo $thumb; ?>" alt="<?php echo $this->escape($owner->fullname); ?>" />
				<span class="block"><a href="<?php echo Route::url('index.php?option=com_members&id=' . $owner->userid); ?>"><?php echo $this->escape($owner->fullname); ?></a></span>
			</li>
			<?php } ?>
			<li class="clear">&nbsp;</li>
		</ul>
	<?php } else { ?>
		<div class="noresults"><?php echo Lang::txt('PLG_PROJECTS_TEAM_EXTERNAL_NO_TEAM'); ?></div>
	<?php } ?>
	<div class="clear"></div>
</div>
