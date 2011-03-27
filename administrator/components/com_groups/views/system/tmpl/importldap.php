<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
JToolBarHelper::title( JText::_( 'GROUPS' ).': <small><small>[ '.JText::_('System').' ]</small></small>', 'user.png' );

if (empty($this->post)) {
	JToolBarHelper::custom('importldap', 'apply', '', 'Execute', false);
	JToolBarHelper::cancel();
?>

<form action="index.php" method="post" name="adminForm">
  <div class="col width-50">
    <fieldset class="adminform">
      <legend>LDAP Import Settings</legend>
      <table class="admintable">
        <tbody>
          <tr>
            <td class="key">
              <span class="editlinktip hasTip" title="Indicate whether or not to replace existing group data in MySQL if a group already exists (recommended: yes).">
                Replace
              </span>
            </td>
            <td>
              <input type="radio" name="replace" id="replace0" value="0"  
              checked="checked" class="inputbox" />
              <label for="replace0">No</label>
              <input type="radio" name="replace" id="replace1" value="1" 
              class="inputbox" />
              <label for="replace1">Yes</label>
            </td>
          </tr>
          <tr>
            <td class="key">
              <span class="editlinktip hasTip" title="Indicate whether or not to update existing group data in MySQL if a group already exists (recommended: yes).">
                Update
              </span>
            </td>
            <td>
              <input type="radio" name="update" id="update0" value="0"  
              checked="checked" class="inputbox" />
              <label for="update0">No</label>
              <input type="radio" name="update" id="update1" value="1" 
              class="inputbox" />
              <label for="update1">Yes</label>
            </td>
          </tr>
          <tr>
            <td class="key">
              <span class="editlinktip hasTip" title="Indicate which objectClass to use. hubGroup (obsolete/legacy HUBzero schema) or posixGroup (rfc2307 schema) (recommended: posixGroup). ">
                Use objectClass
              </span>
            </td>
            <td>
              <select name="objectclass" id="objectclass" class="inputbox" size="1">
                <option value="posixgroup" selected="selected">posixGroup</option>
                <option value="hubgroup">hubGroup</option>
              </select>
            </td>
          </tr>
          <tr>
            <td class="key">
              <span class="editlinktip hasTip" title="Indicate whether to import extended group data when available. (recommend: no).">
                Import extended data
              </span>
            </td>
            <td>
              <input type="radio" name="extended" id="extended0" value="0"  
              checked="checked" class="inputbox" />
              <label for="extended0">No</label>
              <input type="radio" name="extended" id="extended1" value="1" 
              class="inputbox" />
              <label for="extended1">Yes</label>
            </td>
          </tr>
        </table>
      </fieldset>
    </div>
    <input type="hidden" name="option" value="com_groups">
    <input type="hidden" name="task" value="importldap">
    <?php echo JHTML::_( 'form.token' ); ?>
</form>

<?php 
}
else
{
?>

<br />
Successful import of LDAP data into MySQL.

<?php
}

