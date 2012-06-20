<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->profile->get('uidNumber')); ?>"><?php echo JText::_('My Account'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?> </p>
	<?php } ?>

	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->profile->get('uidNumber').'&task=changepassword'); ?>" method="post" id="hubForm">
		<div class="explaination">
			<p><?php echo JText::_('MEMBERS_CHANGEPASSWORD_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<label<?php echo ($this->change && (!$this->oldpass || Hubzero_User_Helper::encrypt_password($this->oldpass) != $this->profile->get('userPassword'))) ? ' class="fieldWithErrors"' : ''; ?>>
				<?php echo JText::_('MEMBER_FIELD_CURRENT_PASS'); ?>
				<input name="oldpass" id="oldpass" type="password" value="" />
			</label>
<?php
					if ($this->change && !$this->oldpass) {
						echo '<p class="error">'.JText::_('MEMBERS_PASS_BLANK').'</p>';
					}
					if ($this->change && $this->oldpass && Hubzero_User_Helper::encrypt_password($this->oldpass) != $this->profile->get('userPassword')) {
						echo '<p class="error">'.JText::_('MEMBERS_PASS_INCORRECT').'</p>';
					}
?>
			<div class="group twoup">
				<label<?php echo ($this->change && (!$this->newpass || $this->newpass != $this->newpass2)) ? ' class="fieldWithErrors"' : ''; ?>>
					<?php echo JText::_('MEMBER_FIELD_NEW_PASS'); ?>
					<input name="newpass" id="newpass" type="password" value="" />
<?php
					if ($this->change && !$this->newpass) {
						echo '<span class="error">'.JText::_('MEMBERS_PASS_BLANK').'</span>';
					}
?>
				</label>

				<label<?php echo ($this->change && (!$this->newpass2 || $this->newpass != $this->newpass2)) ? ' class="fieldWithErrors"' : ''; ?>>
					<?php echo JText::_('MEMBER_FIELD_PASS_CONFIRM'); ?>
					<input name="newpass2" id="newpass2" type="password" value="" />
<?php
					if ($this->change && !$this->newpass2) {
						echo '<span class="error">'.JText::_('MEMBERS_PASS_MUST_CONFIRM').'</span>';
					}
					if ($this->change && $this->newpass && $this->newpass2 && ($this->newpass != $this->newpass2)) {
						echo '<span class="error">'.JText::_('MEMBERS_PASS_NEW_CONFIRMATION_MISMATCH').'</span>';
					}
?>
				</label>
			</div>
<?php
			if (count($this->password_rules) > 0) {
				echo "\t\t<ul>\n";
				foreach ($this->password_rules as $rule) {
					if (!empty($rule)) {
						if (is_array($this->validated)) {
							$err = in_array($rule, $this->validated);
						} else {
							$err = '';
						}

						$mclass = ($err)  ? ' class="error"' : '';
						echo "\t\t\t<li $mclass>".$rule."</li>\n";
					}
				}
				if (is_array($this->validated)) {
					foreach ($this->validated as $msg) {
						if (!in_array($msg,$this->password_rules)) {
							echo "\t\t\t".'<li class="error">'.$msg."</li>\n";
						}
					}
				}
				echo "\t\t\t</ul>\n";
			}
?>
		</fieldset><div class="clear"></div>
		<p class="submit">
			<input name="change" type="submit" value="<?php echo JText::_('CHANGEPASSWORD'); ?>" />
		</p>
	</form>
</div><!-- / .main section -->
