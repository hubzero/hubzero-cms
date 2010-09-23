<?php
// No direct access
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
