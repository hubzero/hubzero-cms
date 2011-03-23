<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<?php if($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>


<form action="" method="post" id="hubForm<?php if ($this->no_html) { echo '-ajax'; }; ?>"> 
	<fieldset>
		<h3>Assign Member Role</h3>
		<label>
			<input type="hidden" name="uid" value="<?php echo $this->uid; ?>" id="uid" />
			<?php
				$u = new Hubzero_User_Profile();
				$u->load( $this->uid );
				
				$current_roles = array();
				$roles = $u->getGroupMemberRoles($u->get('uidNumber'));
				if($roles) {
					foreach($roles as $role) {
						$current_roles[] = $role['role'];
					}
				}
			?>
			<strong>Member: </strong> <?php echo $u->get('name'); ?>
		</label>
		<label><strong>Select a Role</strong>
			<select name="role" id="role">
				<option value="">Select a Member Role...</option>
				<?php foreach($this->roles as $role) { ?>
					<?php if(!in_array($role['role'],$current_roles)) { ?>
						<option value="<?php echo $role['id']; ?>"><?php echo $role['role']; ?></option>
					<?php } ?>
				<?php } ?>
			</select>
		</label>
	</fieldset>
	<input type="hidden" name="active" value="members" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="submitrole" />
	<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />
	<p class="submit"><input type="submit" name="submit" value="Assign Role" /></p>
</form>