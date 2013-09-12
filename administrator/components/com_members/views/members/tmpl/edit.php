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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = MembersHelper::getActions('component');

$text = ($this->task == 'edit' ? JText::_('EDIT') : JText::_('NEW'));

JToolBarHelper::title(JText::_('MEMBER') . ': <small><small>[ ' . $text . ' ]</small></small>', 'user.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::apply();
	JToolBarHelper::save();
}
JToolBarHelper::cancel();

$name = stripslashes($this->profile->get('name'));
$surname = stripslashes($this->profile->get('surname'));
$givenName = stripslashes($this->profile->get('givenName'));
$middleName = stripslashes($this->profile->get('middleName'));

if (!$surname) {
	$bits = explode(' ', $name);
	$surname = array_pop($bits);
	if (count($bits) >= 1) {
		$givenName = array_shift($bits);
	}
	if (count($bits) >= 1) {
		$middleName = implode(' ',$bits);
	}
}

//jimport('joomla.html.pane');
//$tabs =& JPane::getInstance('sliders');

JHtml::_('behavior.switcher');
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	
	submitform(pressbutton);
}
</script>

<form action="index.php" method="post" name="adminForm">
	

	<div class="col width-60 fltlft">
		<nav role="navigation" class="sub-navigation">
		<div id="submenu-box">
			<div class="submenu-box">
				<div class="submenu-pad">
					<ul id="submenu" class="configuration">
						<li><a href="#" onclick="return false;" id="profile" class="active">Profile</a></li>
						<li><a href="#" onclick="return false;" id="demographics">Demographics</a></li>
						<li><a href="#" onclick="return false;" id="password">Password</a></li>
						<li><a href="#" onclick="return false;" id="groups">Groups</a></li>
						<li><a href="#" onclick="return false;" id="hosts">Hosts</a></li>
						<li><a href="#" onclick="return false;" id="managers">Managers</a></li>
					</ul>
					<div class="clr"></div>
				</div>
			</div>
			<div class="clr"></div>
		</div>
	</nav><!-- / .sub-navigation -->
		<div id="config-document">
		<div id="page-profile" class="tab">

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('MEMBERS_PROFILE'); ?></span></legend>

			<input type="hidden" name="id" value="<?php echo $this->profile->get('uidNumber'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />

			<table class="admintable">
			 <tbody>
			  <tr>
			   <td class="key"><label for="public"><?php echo JText::_('PUBLIC_PROFILE'); ?>:</label></td>
			   <td><input type="checkbox" name="profile[public]" id="public" value="1"<?php if ($this->profile->get('public') == 1) { echo ' checked="checked"'; } ?> /></td>
			  </tr>
			  <tr>
			   <td class="key"><label for="vip"><?php echo JText::_('VIP'); ?>:</label></td>
			   <td><input type="checkbox" name="profile[vip]" id="vip" value="1"<?php if ($this->profile->get('vip') == 1) { echo ' checked="checked"'; } ?> /></td>
			  </tr>
			  <tr>
			   <td class="key"><label for="givenName"><?php echo JText::_('FIRST_NAME'); ?>:</label></td>
			   <td><input type="text" name="profile[givenName]" id="givenName" value="<?php echo $this->escape($givenName); ?>" size="50" /></td>
			  </tr>
			  <tr>
			   <td class="key"><label for="middleName"><?php echo JText::_('MIDDLE_NAME'); ?>:</label></td>
			   <td><input type="text" name="profile[middleName]" id="middleName" value="<?php echo $this->escape($middleName); ?>" size="50" /></td>
			  </tr>
			  <tr>
			   <td class="key"><label for="surname"><?php echo JText::_('LAST_NAME'); ?>:</label></td>
			   <td><input type="text" name="profile[surname]" id="surname" value="<?php echo $this->escape($surname); ?>" size="50" /></td>
			  </tr>
			<tr>
			   <td class="key"><label for="orgtype"><?php echo JText::_('COL_EMPLOYMENT_STATUS'); ?>:</label></td>
			   <td>
					<select name="profile[orgtype]" id="orgtype">
						<option value=""<?php if (!$this->profile->get('orgtype')) { echo ' selected="selected"'; } ?>><?php echo JText::_('(select from list)'); ?></option>
				<?php
				
				include_once(JPATH_ROOT . DS . 'components' . DS . 'com_register' . DS . 'tables' . DS . 'organizationtype.php');

				$rot = new RegisterOrganizationType(JFactory::getDBO());
				$types = $rot->getTypes();

				if (!$types || count($types) <= 0) 
				{
					$types = array(
						'universityundergraduate' => JText::_('University / College Undergraduate'),
						'universitygraduate'      => JText::_('University / College Graduate Student'),
						'universityfaculty'       => JText::_('University / College Faculty'), // university
						'universitystaff'         => JText::_('University / College Staff'),
						'precollegestudent'       => JText::_('K-12 (Pre-College) Student'),
						'precollegefacultystaff'  => JText::_('K-12 (Pre-College) Faculty/Staff'), // precollege
						'nationallab'             => JText::_('National Laboratory'),
						'industry'                => JText::_('Industry / Private Company'),
						'government'              => JText::_('Government Agency'),
						'military'                => JText::_('Military'),
						'unemployed'              => JText::_('Retired / Unemployed')
					);
				}
				foreach ($types as $type => $title) 
				{
					echo '<option value="' . $type . '"';
					if ($this->profile->get('orgtype') == $type) 
					{
						echo ' selected="selected"';
					}
					echo '>' . $this->escape(stripslashes($title)) . '</option>' . "\n";
				}
					?>
					</select>
				</td>
			  </tr>  
			<tr>
			   <td class="key"><label for="organization"><?php echo JText::_('ORGANIZATION'); ?>:</label></td>
			   <td><input type="text" name="profile[organization]" id="organization" value="<?php echo $this->escape(stripslashes($this->profile->get('organization'))); ?>" size="50" /></td>
			  </tr>
			  <tr>
		 	   <td class="key"><label for="url"><?php echo JText::_('WEBSITE'); ?>:</label></td>
		 	   <td><input type="text" name="profile[url]" id="url" value="<?php echo $this->escape(stripslashes($this->profile->get('url'))); ?>" size="50" /></td>
		 	  </tr>
			  <tr>
			   <td class="key"><?php echo JText::_('COL_TELEPHONE'); ?>:</td>
			   <td><input type="text" name="profile[phone]" id="phone" value="<?php echo $this->escape($this->profile->get('phone')); ?>" size="50" /></td>
			  </tr>
			  <tr>
		 	   <td class="key"><label for="tags"><?php echo JText::_('INTERESTS'); ?>:</label></td>
		 	   <td><input type="text" name="tags" id="tags" value="<?php echo $this->escape($this->tags); ?>" size="50" /></td>
		 	  </tr>
		 	  <tr>
			   <td class="key" valign="top"><label for="bio"><?php echo JText::_('BIO'); ?>:</label></td>
			   <td>
			        <?php
					jimport('joomla.html.editor');
					$editor = &JEditor::getInstance();
					echo $editor->display('profile[bio]', stripslashes($this->profile->get('bio')), '360px', '200px', '40', '10');
			        ?>
			  </td>
			  </tr>
			<tr>
				<td class="key" valign="top"><?php echo JText::_('COL_CONTACT_ME'); ?>:</td>
				<td>
					<label>
						Would you like to receive email updates (newsletters, etc.)?
						<?php
							$options = array(
								'-1' => '- Select email option &mdash;',
								'1'  => 'Yes, send me emails',
								'0'  => 'No, don\'t send me emails'
							);
						?>
						<select name="profile[mailPreferenceOption]">
							<?php foreach ($options as $key => $value) : ?>
								<?php $sel = ($key == $this->profile->get('mailPreferenceOption')) ? 'selected="selected"' : ''; ?>
								<option <?php echo $sel; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
							<?php endforeach; ?>
						</select>
					</label>
				</td>
			</tr>
			</tbody>
			</table>
		</fieldset>
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('IMAGE'); ?></span></legend>
			
			<?php
			if ($this->profile->get('uidNumber') != '') {
				$pics = stripslashes($this->profile->get('picture'));
				$pics = explode(DS, $pics);
				$file = end($pics);
			?>
			<iframe width="100%" height="350" name="filer" id="filer" frameborder="0" src="index.php?option=<?php echo $this->option; ?>&amp;controller=media&amp;tmpl=component&amp;file=<?php echo $file; ?>&amp;id=<?php echo $this->profile->get('uidNumber'); ?>"></iframe>
			<?php
			} else {
				echo '<p class="warning">'.JText::_('MEMBER_PICTURE_ADDED_LATER').'</p>';
			}
			?>
		</fieldset>

		</div>
		<div id="page-demographics" class="tab">

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('MEMBERS_DEMOGRAPHICS'); ?></span></legend>
			<table class="admintable">
				<tbody>
			<tr>
			   <td class="key" valign="top"><?php echo JText::_('COL_GENDER'); ?>:</td>
			   <td>
				<input type="radio" name="profile[gender]" id="gender_male" value="male" <?php echo ($this->profile->get('gender') == 'male') ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_GENDER_MALE'); ?><br />
				<input type="radio" name="profile[gender]" id="gender_female" value="female" <?php echo ($this->profile->get('gender') == 'female') ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_GENDER_FEMALE'); ?><br />
				<input type="radio" name="profile[gender]" id="gender_refused" value="refused" <?php echo ($this->profile->get('gender') == 'refused') ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_GENDER_REFUSED'); ?>
			   </td>
			  </tr>
			<tr>
			   <td class="key" valign="top"><?php echo JText::_('COL_DISABILITY'); ?>:</td>
			   <td>
				<?php
				$dises = array('no','yes','refused','vocal','blind','deaf','physical','learning');
				$dis = $this->profile->get('disability');
				$disother = '';
				foreach ($dis as $d)
				{
					if (!in_array($d, $dises)) {
						$disother = $d;
					}
				}
				$dis_noanswer = (is_array($dis) && count($dis) <= 1 && empty($dis[0]));

				?>
					<fieldset>
						<legend><label><input type="radio" class="option" name="profile[disability]" id="disabilityyes" value="yes" <?php echo (!$dis_noanswer && !in_array('no',$this->profile->get('disability')) && !in_array('refused',$this->profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('YES'); ?></label></legend><br />
						<label><input type="checkbox" class="option" name="profile[disabilities][blind]" id="disabilityblind" value="blind" <?php echo (in_array('blind',$this->profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_DISABILITY_BLIND'); ?></label><br />
						<label><input type="checkbox" class="option" name="profile[disabilities][deaf]" id="disabilitydeaf" value="deaf" <?php echo (in_array('deaf',$this->profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_DISABILITY_DEAF'); ?></label><br />
						<label><input type="checkbox" class="option" name="profile[disabilities][physical]" id="disabilityphysical" value="physical" <?php echo (in_array('physical',$this->profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_DISABILITY_PHYSICAL'); ?></label><br />
						<label><input type="checkbox" class="option" name="profile[disabilities][learning]" id="disabilitylearning" value="learning" <?php echo (in_array('learning',$this->profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_DISABILITY_LEARNING'); ?></label><br />
						<label><input type="checkbox" class="option" name="profile[disabilities][vocal]" id="disabilityvocal" value="vocal" <?php echo (in_array('vocal',$this->profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_DISABILITY_VOCAL'); ?></label><br />
						<label>Other (please specify):
						<input name="profile[disabilities][other]" id="disabilityother" type="text" value="<?php echo $this->escape($disother); ?>" /></label>
					</fieldset>
					<label><input type="radio" class="option" name="profile[disability]" id="disabilityno" value="no" <?php echo (in_array('no',$this->profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('NO_NONE'); ?></label><br />
					<label><input type="radio" class="option" name="profile[disability]" id="disabilityrefused" value="refused" <?php echo (in_array('refused',$this->profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('REFUSED'); ?></label>
			   </td>
			  </tr>
			<tr>
			   <td class="key" valign="top"><?php echo JText::_('COL_RACE'); ?>:</td>
			   <td>
				<label><input type="checkbox" class="option" name="profile[race][nativeamerican]" id="racenativeamerican" value="nativeamerican" <?php echo (in_array('nativeamerican',$this->profile->get('race'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_RACE_NATIVE_AMERICAN'); ?></label><br />
				<label style="margin-left: 3em;"><?php echo JText::_('COL_RACE_TRIBE'); ?>: <input name="racenativetribe" id="profile[nativeTribe]" type="text" value="<?php echo $this->escape($this->profile->get('nativeTribe')); ?>" /></label><br />
				<label><input type="checkbox" class="option" name="profile[race][asian]" id="raceasian" valu="asian" <?php echo (in_array('asian',$this->profile->get('race'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_RACE_ASIAN'); ?></label><br />
				<label><input type="checkbox" class="option" name="profile[race][black]" id="raceblack" value="black" <?php echo (in_array('black',$this->profile->get('race'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_RACE_BLACK'); ?></label><br />
				<label><input type="checkbox" class="option" name="profile[race][hawaiian]" id="racehawaiian" value="hawaiian" <?php echo (in_array('hawaiian',$this->profile->get('race'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_RACE_PACIFIC_ISLANDER'); ?></label><br />
				<label><input type="checkbox" class="option" name="profile[race][white]" id="racewhite" value="white" <?php echo (in_array('white',$this->profile->get('race'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_RACE_WHITE'); ?></label><br />
				<label><input type="checkbox" class="option" name="profile[race][refused]" id="racerefused" value="refused" <?php echo (in_array('refused',$this->profile->get('race'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('REFUSED'); ?></label>
			   </td>
			  </tr>
			<tr>
				<td class="key" valign="top"><?php echo JText::_('COL_HISPANIC'); ?>:</td>
				<td>
					<?php
					$hises = array('no','yes','refused','cuban','mexican','deaf','puertorican');
					$his = $this->profile->get('disability');
					$hisother = '';
					foreach ($his as $h)
					{
						if (!in_array($h, $hises)) {
							$hisother = $h;
						}
					}
					$his_noanswer = (is_array($his) && count($his) <= 1 && empty($his[0]));
					$hispanic = false;
					if (!empty($his) && !$his_noanswer && !in_array('no',$this->profile->get('hispanic')) && !in_array('refused',$this->profile->get('hispanic'))) {
						$hispanic = true;
					}
					?>
					<fieldset>
						<legend><label><input type="radio" class="option" name="profile[hispanic]" id="hispanicyes" value="yes"  <?php echo ($hispanic) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_HISPANIC_YES'); ?></label></legend>
						<label><input type="checkbox" class="option" name="profile[hispanics][cuban]" id="hispaniccuban" value="cuban" <?php echo (in_array('cuban',$this->profile->get('hispanic'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_HISPANIC_CUBAN'); ?></label><br />
						<label><input type="checkbox" class="option" name="profile[hispanics][mexican]" id="hispanicmexican" value="mexican" <?php echo (in_array('mexican',$this->profile->get('hispanic'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_HISPANIC_CHICANO'); ?></label><br />
						<label><input type="checkbox" class="option" name="profile[hispanics][puertorican]" id="hispanicpuertorican" value="puertorican" <?php echo (in_array('puertorican',$this->profile->get('hispanic'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_HISPANIC_PUERTORICAN'); ?></label><br />
						<label><?php echo JText::_('COL_HISPANIC_OTHER'); ?>: <input name="profile[hispanics][other]" id="hispanicother" type="text" value="<?php echo $this->escape($hisother); ?>" /></label>
					</fieldset>
					<label><input type="radio" class="option" name="profile[hispanic]" id="hispanicno" value="no" <?php echo (in_array('no',$this->profile->get('hispanic'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_HISPANIC_NO'); ?></label><br />
					<label><input type="radio" class="option" name="profile[hispanic]" id="hispanicrefused" value="refused" <?php echo (in_array('refused',$this->profile->get('hispanic'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('REFUSED'); ?></label>
				</td>
			</tr>
			 </tbody>
			</table>
		</fieldset>
			</div>
			<div id="page-password" class="tab">

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('PASSWORD'); ?></span></legend>

			<table class="admintable" summary="<?php echo JText::_('ADMIN_PROFILE_TBL_SUMMARY'); ?>">
				<tbody>
					<tr class="odd">
						<td class="key"><?php echo JText::_('CURRENT_PASSWORD'); ?>:</td>
						<td><input type="text" name="profile[currentpassword]" disabled="disabled" <?php echo ($this->profile->get('userPassword')) ? "value=\"{$this->profile->get('userPassword')}\"" : 'placeholder="no local password set"'; ?> /></td>
					</tr>
					<tr class="even">
						<td class="key"><label for="newpass"><?php echo JText::_('NEW_PASSWORD'); ?>:</label></th>
						<td>
							<input type="password" name="newpass" id="newpass" value="" /><br />
							<strong>NOTE:</strong> Entering anything above will reset the user's password.
						</td>
					</tr>
					<tr class="edd">
						<td title="shadowLastChange" class="key"><?php echo JText::_('SHADOW_LAST_CHANGE'); ?>:</td>
						<td>
							<?php
								if (is_object($this->password) && $this->password->get('shadowLastChange'))
								{
									$shadowLastChange = $this->password->get('shadowLastChange')*86400;
									echo date("Y-m-d", $shadowLastChange); 
									echo " ({$this->password->get('shadowLastChange')})";
									echo " - " . intval((time()/86400) - ($shadowLastChange/86400)) . " days ago";
								}
								else
								{
									echo "never";
								}
							?>
						</td>
					</tr>
					<tr class="even">
						<td title="shadowMax" class="key"><?php echo JText::_('SHADOW_MAX'); ?>:</td>
						<td><input type="text" name="shadowMax" value="<?php echo $this->password->get('shadowMax'); ?>" /></td>
					</tr>
					<tr class="odd">
						<td title="shadowWarning" class="key"><?php echo JText::_('SHADOW_WARNING'); ?>:</td>
						<td><input type="text" name="shadowWarning" value="<?php echo $this->password->get('shadowWarning'); ?>" /></td>
					</tr>
					<tr class="even">
						<td title="shadowExpire" class="key"><?php echo JText::_('SHADOW_EXPIRE'); ?>:</td>
						<td><input type="text" name="shadowExpire" value="<?php echo $this->password->get('shadowExpire'); ?>" placeholder="Expiration date (past or future) - Format: 'yyyy-mm-dd' or days since epoch" /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>

			</div>
			<div id="page-groups" class="tab">
				<fieldset class="adminform">
					<legend><span><?php echo JText::_('GROUPS'); ?></span></legend>
					
					<iframe width="100%" height="500" name="grouper" id="grouper" frameborder="0" src="index.php?option=<?php echo $this->option; ?>&amp;controller=groups&amp;tmpl=component&amp;id=<?php echo $this->profile->get('uidNumber'); ?>"></iframe>
				</fieldset>
			</div>
			<div id="page-hosts" class="tab">
				<fieldset class="adminform">
					<legend><span><?php echo JText::_('HOSTS'); ?></span></legend>

					<iframe width="100%" height="500" name="hosts" id="hosts" frameborder="0" src="index.php?option=<?php echo $this->option; ?>&amp;controller=hosts&amp;tmpl=component&amp;id=<?php echo $this->profile->get('uidNumber'); ?>"></iframe>
				</fieldset>
			</div>
			<div id="page-managers" class="tab">
				<fieldset class="adminform">
					<legend><span><?php echo JText::_('Managers'); ?></span></legend>

					<iframe width="100%" height="500" name="managers" id="managers" frameborder="0" src="index.php?option=<?php echo $this->option; ?>&amp;controller=managers&amp;tmpl=component&amp;id=<?php echo $this->profile->get('uidNumber'); ?>"></iframe>
				</fieldset>
			</div>
		</div>
		<div class="clr"></div>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta" summary="Metadata">
			<tbody>
				<tr>
					<th><?php echo JText::_('ID'); ?></th>
					<td><?php echo $this->profile->get('uidNumber'); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('USERNAME'); ?></th>
					<td><?php echo $this->profile->get('username'); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COL_CITIZENSHIP'); ?></th>
					<th><?php echo $this->profile->get('countryorigin'); ?></th>
				</tr>
				<tr>
					<th><?php echo JText::_('COL_RESIDENCE'); ?></th>
					<th><?php echo $this->profile->get('countryresident'); ?></th>
				</tr>
				<tr>
					<th><?php echo JText::_('COL_REGHOST'); ?></th>
					<td><?php 
						echo ($this->profile->get('regHost')) ? $this->profile->get('regHost').'<br />' : '';
						echo ($this->profile->get('regIP')) ? $this->profile->get('regIP') : '';
					?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COL_MODIFIED'); ?></th>
					<th><?php echo $this->profile->get('modifiedDate'); ?></th>
				</tr>
				<?php
				$database =& JFactory::getDBO();
				$database->setQuery("SELECT du.*, d.domain FROM #__xdomain_users AS du, #__xdomains AS d WHERE du.domain_id=d.domain_id AND du.uidNumber=".$this->profile->get('uidNumber'));
				$domains = $database->loadObjectList();
				if ($domains) {
					foreach ($domains as $d)
					{
						?>
						<tr>
							<th><?php echo $d->domain; ?></th>
							<td><?php echo $d->domain_username; ?></td>
						</tr>
						<?php
					}
				} else {
					?>
					<tr>
						<th><?php echo JText::_('Domains'); ?></th>
						<td><?php echo JText::_('(none)'); ?></td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('DETAILS'); ?></span></legend>
			
			<table class="admintable" summary="<?php echo JText::_('ADMIN_PROFILE_TBL_SUMMARY'); ?>">
				<tbody>
					<tr class="odd">
						<td class="key"><?php echo JText::_('COL_EMAIL'); ?></td>
						<td>
			<?php 
			if ($this->profile->get('emailConfirmed') == 1) {
				$confirmed = '<label><input type="checkbox" name="emailConfirmed" id="emailConfirmed" value="1" checked="checked" /> '.JText::_('EMAIL_CONFIRMED').'</label>';
			} elseif ($this->profile->get('emailConfirmed') == 2) {
				$confirmed = JText::_('EMAIL_GRANDFATHERED').'<input type="hidden" name="emailConfirmed" id="emailConfirmed" value="2" />';
			} elseif ($this->profile->get('emailConfirmed') == 3) {
				$confirmed = JText::_('EMAIL_DOMAIN_SUPPLIED').'<input type="hidden" name="emailConfirmed" id="emailConfirmed" value="3" />';
			} elseif ($this->profile->get('emailConfirmed') < 0) {
				if ($this->profile->get('email')) {
					$confirmed  = JText::_('EMAIL_AWAITING_CONFIRMATION');
					$confirmed .= '<br />[code: ' . -$this->profile->get('emailConfirmed') . '] <label><input type="checkbox" name="emailConfirmed" id="emailConfirmed" value="1" /> '.JText::_('EMAIL_CONFIRM').'</label>';
				} else {
					$confirmed  = JText::_('EMAIL_NONE_ON_FILE');
				}
			} else {
				$confirmed  = '['.JText::_('EMAIL_UNKNOWN_STATUS').'] <label><input type="checkbox" name="emailConfirmed" id="emailConfirmed" value="1" /> '.JText::_('EMAIL_CONFIRM').'</label>';
			} 
			?>
			<?php if ($this->profile->get('email')) { ?>
							<input type="text" name="profile[email]" id="email" value="<?php echo $this->escape(stripslashes($this->profile->get('email'))); ?>" size="20" /> (<?php echo $confirmed; ?>)
			<?php } else { ?>
							<span style="color:#c00;"><?php echo JText::_('EMAIL_NONE_ON_FILE'); ?></span><br />
							<input type="text" name="profile[email]" id="email" value="" size="20" /> <label><input type="checkbox" name="emailConfirmed" id="emailConfirmed" value="1" /> <?php echo JText::_('EMAIL_CONFIRM'); ?></label>
			<?php } ?>
						</td>
					</tr>
					<tr class="odd">
						<td class="key"><?php echo JText::_('COL_JOBS_ALLOWED'); ?></td>
						<td><input type="text" name="profile[jobsAllowed]" id="jobsAllowed" value="<?php echo $this->profile->get('jobsAllowed'); ?>" size="10" /></td>
					</tr>
					<tr class="even">
						<td class="key"><?php echo JText::_('COL_HOMEDIRECTORY'); ?></th>
						<td><input type="text" name="profile[homeDirectory]" id="homeDirectory" value="<?php echo $this->profile->get('homeDirectory'); ?>" size="10" /></td>
					</tr>
					<tr class="even">
						<td class="key"><?php echo JText::_('COL_LOGINSHELL'); ?></th>
						<td><input type="text" name="profile[loginShell]" id="loginShell" value="<?php echo $this->profile->get('loginShell'); ?>" size="10" /></td>
					</tr>
					<tr class="even">
						<td class="key"><?php echo JText::_('COL_ADMINISTRATOR'); ?></td>
						<td><?php echo ($this->profile->get('admin') ? implode(', ', $this->profile->get('admin')) : '--'); ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>
	<?php echo JHTML::_('form.token'); ?>
</form>

