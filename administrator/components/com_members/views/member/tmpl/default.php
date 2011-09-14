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

$text = ( $this->task == 'edit' ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );

JToolBarHelper::title( JText::_( 'MEMBER' ).': <small><small>[ '. $text.' ]</small></small>', 'user.png' );
JToolBarHelper::apply();
JToolBarHelper::save();
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
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	
	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm">
	<div class="col width-60">
		<fieldset class="adminform">
			<legend><?php echo JText::_('MEMBERS_PROFILE'); ?></legend>
			
			<input type="hidden" name="id" value="<?php echo $this->profile->get('uidNumber'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="save" />
			
			<table class="admintable">
			 <tbody>
			  <tr>
			   <td class="key"><label for="public"><?php echo JText::_('PUBLIC_PROFILE'); ?>:</label></td>
			   <td><input type="checkbox" name="profile[public]" id="public" value="1"<?php if ($this->profile->get('public') == 1) { echo ' checked="checked"'; } ?> /></td>
			  </tr>  
			  <tr>
			   <td class="key"><?php echo JText::_('USERNAME'); ?></td>
			   <td><?php echo $this->profile->get('username');?> (<?php echo $this->profile->get('uidNumber'); ?>)</td>
			  </tr>
			  <tr>
			   <td class="key"><?php echo JText::_('CURRENT_PASSWORD'); ?></td>
			   <td><?php echo $this->profile->get('userPassword'); ?></td>
			  </tr>
			  <tr>
			   <td class="key"><label for="vip"><?php echo JText::_('NEW_PASSWORD'); ?>:</label></td>
			   <td>
				<input type="password" name="newpass" id="newpass" value="" /><br />
				<strong>NOTE:</strong> Entering anything here will reset the user's password.
			   </td>
			  </tr>
			  <tr>
			   <td class="key"><label for="vip"><?php echo JText::_('VIP'); ?>:</label></td>
			   <td><input type="checkbox" name="profile[vip]" id="vip" value="1"<?php if ($this->profile->get('vip') == 1) { echo ' checked="checked"'; } ?> /></td>
			  </tr>
			  <tr>
			   <td class="key"><label for="givenName"><?php echo JText::_('FIRST_NAME'); ?>:</label></td>
			   <td><input type="text" name="profile[givenName]" id="givenName" value="<?php echo htmlentities($givenName,ENT_COMPAT,'UTF-8'); ?>" size="50" /></td>
			  </tr>
			  <tr>
			   <td class="key"><label for="middleName"><?php echo JText::_('MIDDLE_NAME'); ?>:</label></td>
			   <td><input type="text" name="profile[middleName]" id="middleName" value="<?php echo htmlentities($middleName,ENT_COMPAT,'UTF-8'); ?>" size="50" /></td>
			  </tr>
			  <tr>
			   <td class="key"><label for="surname"><?php echo JText::_('LAST_NAME'); ?>:</label></td>
			   <td><input type="text" name="profile[surname]" id="surname" value="<?php echo htmlentities($surname,ENT_COMPAT,'UTF-8'); ?>" size="50" /></td>
			  </tr>
			<tr>
			   <td class="key"><label for="orgtype"><?php echo JText::_('COL_EMPLOYMENT_STATUS'); ?>:</label></td>
			   <td>
				<?php
				$html  = "\t\t".'<select name="profile[orgtype]" id="orgtype">'."\n";
				if (!$this->profile->get('orgtype')) {
					$html .= "\t\t\t".'<option value="" selected="selected">'.JText::_('(select from list)').'</option>'."\n";
				}
				$html .= "\t\t\t".'<option value="universityundergraduate"';
				if ($this->profile->get('orgtype') == 'universityundergraduate') {
					$html .= ' selected="selected"';
				}
				$html .= '>'.JText::_('University / College Undergraduate').'</option>'."\n";
				$html .= "\t\t\t".'<option value="universitygraduate"';
				if ($this->profile->get('orgtype') == 'universitygraduate') {
					$html .= ' selected="selected"';
				}
				$html .= '>'.JText::_('University / College Graduate Student').'</option>'."\n";
				$html .= "\t\t\t".'<option value="universityfaculty"';
				if ($this->profile->get('orgtype') == 'universityfaculty' || $this->profile->get('orgtype') == 'university') {
					$html .= ' selected="selected"';
				}
				$html .= '>'.JText::_('University / College Faculty').'</option>'."\n";
				$html .= "\t\t\t".'<option value="universitystaff"';
				if ($this->profile->get('orgtype') == 'universitystaff') {
					$html .= ' selected="selected"';
				}
				$html .= '>'.JText::_('University / College Staff').'</option>'."\n";
				$html .= "\t\t\t".'<option value="precollegestudent"';
				if ($this->profile->get('orgtype') == 'precollegestudent') {
					$html .= ' selected="selected"';
				}
				$html .= '>'.JText::_('K-12 (Pre-College) Student').'</option>'."\n";
				$html .= "\t\t\t".'<option value="precollegefacultystaff"';
				if ($this->profile->get('orgtype') == 'precollege' || $this->profile->get('orgtype') == 'precollegefacultystaff') {
					$html .= ' selected="selected"';
				}
				$html .= '>'.JText::_('K-12 (Pre-College) Faculty/Staff').'</option>'."\n";
				$html .= "\t\t\t".'<option value="industry"';
				if ($this->profile->get('orgtype') == 'industry') {
					$html .= ' selected="selected"';
				}
				$html .= '>'.JText::_('Industry / Private Company').'</option>'."\n";
				$html .= "\t\t\t".'<option value="government"';
				if ($this->profile->get('orgtype') == 'government') {
					$html .= ' selected="selected"';
				}
				$html .= '>'.JText::_('Government Agency').'</option>'."\n";
				$html .= "\t\t\t".'<option value="military"';
				if ($this->profile->get('orgtype') == 'military') {
					$html .= ' selected="selected"';
				}
				$html .= '>'.JText::_('Military').'</option>'."\n";
				$html .= "\t\t\t".'<option value="unemployed"';
				if ($this->profile->get('orgtype') == 'unemployed') {
					$html .= ' selected="selected"';
				}
				$html .= '>'.JText::_('Retired / Unemployed').'</option>'."\n";
				$html .= "\t\t".'</select>'."\n";
				echo $html;
				?>
				</td>
			  </tr>  
			<tr>
			   <td class="key"><label for="organization"><?php echo JText::_('ORGANIZATION'); ?>:</label></td>
			   <td><input type="text" name="profile[organization]" id="organization" value="<?php echo htmlentities(stripslashes($this->profile->get('organization')),ENT_COMPAT,'UTF-8'); ?>" size="50" /></td>
			  </tr>
			  <tr>
		 	   <td class="key"><label for="url"><?php echo JText::_('WEBSITE'); ?>:</label></td>
		 	   <td><input type="text" name="profile[url]" id="url" value="<?php echo htmlentities(stripslashes($this->profile->get('url')),ENT_COMPAT,'UTF-8'); ?>" size="50" /></td>
		 	  </tr>
			  <tr>
			   <td class="key"><?php echo JText::_('COL_TELEPHONE'); ?>:</td>
			   <td><input type="text" name="profile[phone]" id="phone" value="<?php echo htmlentities($this->profile->get('phone'),ENT_COMPAT,'UTF-8'); ?>" size="50" /></td>
			  </tr>
			  <tr>
		 	   <td class="key"><label for="tags"><?php echo JText::_('INTERESTS'); ?>:</label></td>
		 	   <td><input type="text" name="tags" id="tags" value="<?php echo $this->tags; ?>" size="50" /></td>
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
					<label><input type="checkbox" id="mailPreferenceOption" name="profile[mailPreferenceOption]" <?php echo ($this->profile->get('mailPreferenceOption')) ? ' checked="checked"' : ''; ?> value="1" /> Yes, I would like to receive newsletters and other updates by e-mail.</label>
				</td>
			</tr>
			</tbody>
			</table>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('MEMBERS_DEMOGRAPHICS'); ?></legend>
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
						<input name="profile[disabilities][other]" id="disabilityother" type="text" value="<?php echo htmlentities($disother,ENT_COMPAT,'UTF-8'); ?>" /></label>
					</fieldset>
					<label><input type="radio" class="option" name="profile[disability]" id="disabilityno" value="no" <?php echo (in_array('no',$this->profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('NO_NONE'); ?></label><br />
					<label><input type="radio" class="option" name="profile[disability]" id="disabilityrefused" value="refused" <?php echo (in_array('refused',$this->profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('REFUSED'); ?></label>
			   </td>
			  </tr>
			<tr>
			   <td class="key" valign="top"><?php echo JText::_('COL_RACE'); ?>:</td>
			   <td>
				<label><input type="checkbox" class="option" name="profile[race][nativeamerican]" id="racenativeamerican" value="nativeamerican" <?php echo (in_array('nativeamerican',$this->profile->get('race'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_RACE_NATIVE_AMERICAN'); ?></label><br />
				<label style="margin-left: 3em;"><?php echo JText::_('COL_RACE_TRIBE'); ?>: <input name="racenativetribe" id="profile[nativeTribe]" type="text" value="<?php echo htmlentities($this->profile->get('nativeTribe'),ENT_COMPAT,'UTF-8'); ?>" /></label><br />
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
						<label><?php echo JText::_('COL_HISPANIC_OTHER'); ?>: <input name="profile[hispanics][other]" id="hispanicother" type="text" value="<?php echo htmlentities($hisother,ENT_COMPAT,'UTF-8'); ?>" /></label>
					</fieldset>
					<label><input type="radio" class="option" name="profile[hispanic]" id="hispanicno" value="no" <?php echo (in_array('no',$this->profile->get('hispanic'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_HISPANIC_NO'); ?></label><br />
					<label><input type="radio" class="option" name="profile[hispanic]" id="hispanicrefused" value="refused" <?php echo (in_array('refused',$this->profile->get('hispanic'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('REFUSED'); ?></label>
				</td>
			</tr>
			 </tbody>
			</table>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('DETAILS'); ?></legend>
			
			<?php
			$html  = "\t".'<table class="admintable" summary="'.JText::_('ADMIN_PROFILE_TBL_SUMMARY').'">'."\n";
			$html .= "\t\t".'<tbody>'."\n";
			$html .= "\t\t\t".'<tr class="even">'."\n";
			$html .= "\t\t\t\t".'<td class="key">'.JText::_('COL_EXPIRE').'</td>'."\n";
			if ( $this->profile->get('shadowExpire') > 0 )
				$html .= "\t\t\t\t".'<td> <label><input type="checkbox" name="shadowExpire" id="shadowExpire" value="1" checked="checked"/></label></td>';
			else
			     $html .= "\t\t\t\t".'<td> <label><input type="checkbox" name="shadowExpire" id="shadowExpire" value="1"/></label></td>';
			$html .= "\t\t\t".'</tr>'."\n";
			//$html .= "\t\t\t".'<tr class="even">'."\n";
			//$html .= "\t\t\t\t".'<td class="key">'.JText::_('COL_PASSWORD').'</td>'."\n";
			//$html .= "\t\t\t\t".'<td><a href="/password/lost">'.JText::_('RESET_PASSWORD').'</a></td>'."\n";
			//$html .= "\t\t\t".'</tr>'."\n";
			$html .= "\t\t\t".'<tr class="odd">'."\n";
			$html .= "\t\t\t\t".'<td class="key">'.JText::_('COL_JOBS_ALLOWED').'</td>'."\n";
			$html .= "\t\t\t\t".'<td><input type="text" name="profile[jobsAllowed]" id="jobsAllowed" value="'.$this->profile->get('jobsAllowed').'" size="10" /></td>'."\n";
			$html .= "\t\t\t".'</tr>'."\n";
			$html .= "\t\t\t".'<tr class="odd">'."\n";
			$html .= "\t\t\t\t".'<td class="key">'.JText::_('COL_CITIZENSHIP').'</td>'."\n";
			$html .= "\t\t\t\t".'<td>'.$this->profile->get('countryorigin').'</td>'."\n";
			$html .= "\t\t\t".'</tr>'."\n";
			$html .= "\t\t\t".'<tr class="even">'."\n";
			$html .= "\t\t\t\t".'<td class="key">'.JText::_('COL_RESIDENCE').'</td>'."\n";
			$html .= "\t\t\t\t".'<td>'.$this->profile->get('countryresident').'</td>'."\n";
			$html .= "\t\t\t".'</tr>'."\n";
			/*$html .= "\t\t\t".'<tr class="odd">'."\n";
			$html .= "\t\t\t\t".'<td class="key">'.JText::_('COL_EMPLOYMENT_STATUS').'</td>'."\n";
			$html .= "\t\t\t\t".'<td>';
			$html .= ($this->profile->get('orgtype')) ? JText::_(strtoupper($this->profile->get('orgtype'))) : '';
			$html .= '</td>'."\n";
			$html .= "\t\t\t".'</tr>'."\n";*/
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
			$html .= "\t\t\t".'<tr class="even">'."\n";
			$html .= "\t\t\t\t".'<td class="key">'.JText::_('COL_EMAIL').'</td>'."\n";
			$html .= "\t\t\t\t".'<td>';
			if ($this->profile->get('email')) {
				//$html .= '<a href="mailto:'.stripslashes($this->profile->get('email')).'">'.stripslashes($this->profile->get('email')).'</a> ('.$confirmed.')'."\n";
				$html .= '<input type="text" name="profile[email]" id="email" value="'.htmlentities(stripslashes($this->profile->get('email')),ENT_COMPAT,'UTF-8').'" size="20" /> ('.$confirmed.')'."\n";
			} else {
				$html .= '<span style="color:#c00;">'.JText::_('EMAIL_NONE_ON_FILE').'</span>';
				$html .= '<br /><input type="text" name="profile[email]" id="email" value="" size="20" /> <label><input type="checkbox" name="emailConfirmed" id="emailConfirmed" value="1" /> '.JText::_('EMAIL_CONFIRM').'</label>';
			}
			$html .= '</td>'."\n";
			$html .= "\t\t\t".'</tr>'."\n";
			$html .= "\t\t\t".'<tr class="odd">'."\n";
			$html .= "\t\t\t\t".'<td class="key">'.JText::_('COL_ADMINISTRATOR').'</td>'."\n";
			$html .= "\t\t\t\t".'<td>'.implode(', ',$this->profile->get('admin')).'</td>'."\n";
			$html .= "\t\t\t".'</tr>'."\n";
			$html .= "\t\t\t".'<tr class="odd">'."\n";
			$html .= "\t\t\t\t".'<td class="key">'.JText::_('COL_HOMEDIRECTORY').'</td>'."\n";
			$html .= "\t\t\t\t".'<td>'.$this->profile->get('homeDirectory').'</td>'."\n";
			$html .= "\t\t\t".'</tr>'."\n";
			$html .= "\t\t\t".'<tr class="even">'."\n";
			$html .= "\t\t\t\t".'<td class="key">'.JText::_('COL_REGHOST').'</td>'."\n";
			$html .= "\t\t\t\t".'<td>';
			$html .= ($this->profile->get('regHost')) ? $this->profile->get('regHost') : '-' ;
			$html .= ($this->profile->get('regIP')) ?' ('.$this->profile->get('regIP').')' : '';
			$html .= '</td>'."\n";
			$html .= "\t\t\t".'</tr>'."\n";
			$html .= "\t\t\t".'<tr class="even">'."\n";
			$html .= "\t\t\t\t".'<td class="key">'.JText::_('COL_MODIFIED').'</td>'."\n";
			$html .= "\t\t\t\t".'<td>'.$this->profile->get('modifiedDate').'</td>'."\n";
			$html .= "\t\t\t".'</tr>'."\n";
			$html .= "\t\t".'</tbody>'."\n";
			$html .= "\t".'</table>'."\n";
			echo $html;
			?>
		</fieldset>
	</div>
	<div class="col width-40">
		<table width="100%" style="border: 1px dashed silver; padding: 5px; margin-bottom: 10px;">
			<tbody>
				<?php
				$database =& JFactory::getDBO();
				$database->setQuery("SELECT du.*, d.domain FROM #__xdomain_users AS du, #__xdomains AS d WHERE du.domain_id=d.domain_id AND du.uidNumber=".$this->profile->get('uidNumber'));
				$domains = $database->loadObjectList();
				if ($domains) {
					foreach ($domains as $d)
					{
						?>
						<tr>
							<td><?php echo $d->domain; ?></td>
							<td><?php echo $d->domain_username; ?></td>
						</tr>
						<?php
					}
				} else {
					?>
					<tr>
						<td><?php echo JText::_('No domains found'); ?></td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		
		<fieldset class="adminform">
			<legend><?php echo JText::_('IMAGE'); ?></legend>
			
			<?php
			if ($this->profile->get('uidNumber') != '') {
				$pics = stripslashes($this->profile->get('picture'));
				$pics = explode(DS, $pics);
				$file = end($pics);
			?>
			<iframe width="100%" height="350" name="filer" id="filer" frameborder="0" src="index3.php?option=<?php echo $this->option; ?>&amp;task=img&amp;file=<?php echo $file; ?>&amp;id=<?php echo $this->profile->get('uidNumber'); ?>"></iframe>
			<?php
			} else {
				echo '<p class="warning">'.JText::_('MEMBER_PICTURE_ADDED_LATER').'</p>';
			}
			?>
		</fieldset>
		
		<fieldset class="adminform">
			<legend><?php echo JText::_('GROUPS'); ?></legend>
			
			<iframe width="100%" height="200" name="grouper" id="grouper" frameborder="0" src="index3.php?option=<?php echo $this->option; ?>&amp;task=group&amp;id=<?php echo $this->profile->get('uidNumber'); ?>"></iframe>
		</fieldset>
		
		<fieldset class="adminform">
			<legend><?php echo JText::_('HOSTS'); ?></legend>

			<iframe width="100%" height="200" name="hosts" id="hosts" frameborder="0" src="index3.php?option=<?php echo $this->option; ?>&amp;task=hosts&amp;id=<?php echo $this->profile->get('uidNumber'); ?>"></iframe>
		</fieldset>
		
		<fieldset class="adminform">
			<legend><?php echo JText::_('Managers'); ?></legend>

			<iframe width="100%" height="200" name="managers" id="managers" frameborder="0" src="index3.php?option=<?php echo $this->option; ?>&amp;task=managers&amp;id=<?php echo $this->profile->get('uidNumber'); ?>"></iframe>
		</fieldset>
	</div>
	<div class="clr"></div>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

