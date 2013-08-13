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
<?php if($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>


<form action="index.php" method="post" id="hubForm<?php if ($this->no_html) { echo '-ajax'; }; ?>"> 
	<fieldset>
		<legend>Assign Member Role</legend>
		<label>
			<input type="hidden" name="uid" value="<?php echo $this->uid; ?>" id="uid" />
			<?php
				$u = new Hubzero_User_Profile();
				$u->load( $this->uid );

				$current_roles = array();
				$roles = $u->getGroupMemberRoles($u->get('uidNumber'), $this->group->get('gidNumber'));
				if($roles) {
					foreach($roles as $role) {
						$current_roles[] = $role['role'];
					}
				}
			?>
			<strong>Member: </strong> <?php echo $u->get('name'); ?>
		</label>
		<label><strong>Select a Role</strong>
			<select name="role" id="roles">
				<option value="">Select a Member Role...</option>
				<?php foreach($this->roles as $role) { ?>
					<?php if(!in_array($role['role'],$current_roles)) { ?>
						<option value="<?php echo $role['id']; ?>"><?php echo $role['role']; ?></option>
					<?php } ?>
				<?php } ?>
			</select>
		</label>
	</fieldset>
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
	<input type="hidden" name="active" value="members" />
	<input type="hidden" name="action" value="submitrole" />
	<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />
	<p class="submit">
		<input type="submit" name="submit" value="Assign Role" />
	</p>
</form>
