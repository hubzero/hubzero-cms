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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<ul class="team-listing cf <?php echo (isset($this->cls)) ? $this->cls : ''; ?>">
	<?php foreach ($this->members as $member) : ?>
		<?php 
			$profile = $member->getProfile();
			$me      = ($profile->get('uidNumber') == User::get('id')) ? true : false;
		?>
		<li <?php echo ($me) ? 'class="me"' : ''; ?>>
			<a href="<?php echo $profile->link(); ?>" class="tooltips" title="<?php echo $profile->get('name'); ?> <?php echo ($me) ? '(You)' : ''; ?>">
				<img src="<?php echo $profile->picture(0, true); ?>" alt="" />
				<span><?php echo $profile->get('name'); ?></span>
			</a>
			<?php if (!$me) : ?>
				<a class="btn btn-danger btn-secondary remove confirm" data-txt-confirm="<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TEAM_MEMBER_REMOVE_CONFIRM'); ?>" href="<?php echo Route::url($member->link('remove')); ?>">
					<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TEAM_MEMBER_REMOVE'); ?>
				</a>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ul>