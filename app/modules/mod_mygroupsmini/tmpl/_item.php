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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<li class="group<?php if ($group->published == 2) { echo ' archived'; } ?>">
   <div class="grpdisp">
   <?php
	 $group1 = Hubzero\User\Group::getInstance($group->gidNumber);
	 $path = PATH_APP . '/site/groups/' . $group1->get('gidNumber') . '/uploads/' . $group1->get('logo');
   ?>
	 <?php if ($group1->get('logo') && is_file($path)) { ?>
	 <?php echo '  <a class="group-img" href="' . Route::url('index.php?option=com_groups&cn='. $group1->get('cn')) . '">'; ?>
	 <?php echo '      <img src="' . with(new Hubzero\Content\Moderator($path))->getUrl() . '" alt="' . $this->escape(stripslashes($group1->get('description'))) . '" />'; ?>
	 <?php echo '  </a>'; ?>
   <?php }
   else { ?>
   <?php echo '  <a class="group-img" href="' . Route::url('index.php?option=com_groups&cn='. $group1->get('cn')) . '">'; ?>
   <?php echo '  <img src="/core/components/com_groups/site/assets/img/group_default_logo.png"/>'; ?>
   <?php echo '  </a>'; ?>
   <?php } ?>
   <div class="group-name"><a href="<?php echo Route::url('index.php?option=com_groups&cn=' . $group->cn); ?>"><?php echo $this->escape(stripslashes($group->description));?></a></div>
   </div>

  <?php if (!$group->approved) : ?>
		<span class="status pending-approval"><?php echo Lang::txt('MOD_MYGROUPSMINI_GROUP_STATUS_PENDING'); ?></span>
	<?php endif; ?>
	<?php if ($group->published == 2) : ?>
		<span class="status archived"><?php echo Lang::txt('MOD_MYGROUPSMINI_GROUP_STATUS_ARCHIVED'); ?></span>
	<?php endif; ?>
	<?php if ($group->regconfirmed && !$group->registered) : ?>
		<span class="actions">
			<a class="action-accept" href="<?php echo Route::url('index.php?option=com_groups&cn=' . $group->cn . '&task=accept'); ?>">
				<?php echo Lang::txt('MOD_MYGROUPSMINI_ACTION_ACCEPT'); ?>
			</a>
			<a class="action-cancel" href="<?php echo Route::url('index.php?option=com_groups&cn=' . $group->cn . '&task=cancel'); ?>">
				<?php echo Lang::txt('MOD_MYGROUPSMINI_ACTION_DECLINE'); ?>
			</a>
		</span>
	<?php endif; ?>
</li>
