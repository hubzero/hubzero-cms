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

if (!defined("n")) {
	define("t","\t");
	define("n","\n");
	define("br","<br />");
	define("sp","&#160;");
	define("a","&amp;");
}

class MembersHtml
{
	public function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------
	
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}
	
	//-----------
	
	public function browse( &$rows, &$pageNav, $option, $filters ) 
	{
		?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.getElementById('adminForm');
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			submitform( pressbutton );
		}
		</script>
	
		<form action="index.php" method="post" name="adminForm" id="adminForm">
			<fieldset id="filter">
					<?php echo JText::_('SEARCH'); ?>
					<select name="search_field">
						<option value="uidNumber"<?php if ($filters['search_field'] == 'uidNumber') { echo ' selected="selected"'; } ?>><?php echo JText::_('ID'); ?></option>
						<option value="email"<?php if ($filters['search_field'] == 'email') { echo ' selected="selected"'; } ?>><?php echo JText::_('EMAIL'); ?></option>
						<option value="username"<?php if ($filters['search_field'] == 'username') { echo ' selected="selected"'; } ?>><?php echo JText::_('USERNAME'); ?></option>
						<option value="surname"<?php if ($filters['search_field'] == 'surname') { echo ' selected="selected"'; } ?>><?php echo JText::_('LAST_NAME'); ?></option>
						<option value="givenName"<?php if ($filters['search_field'] == 'giveName') { echo ' selected="selected"'; } ?>><?php echo JText::_('FIRST_NAME'); ?></option>
						<option value="name"<?php if ($filters['search_field'] == 'name') { echo ' selected="selected"'; } ?>><?php echo JText::_('FULL_NAME'); ?></option>
					</select>
					for 
					<input type="text" name="search" value="<?php echo $filters['search']; ?>" />
				
			
				<label>
					<?php echo JText::_('SORT_BY'); ?>:
					<select name="sortby">
						<option value="uidNumber DESC"<?php if ($filters['sortby'] == 'uidNumber DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('ID DESC'); ?></option>
						<option value="uidNumber ASC"<?php if ($filters['sortby'] == 'uidNumber ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('ID ASC'); ?></option>
						<option value="username ASC"<?php if ($filters['sortby'] == 'username ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('USERNAME'); ?></option>
						<option value="surname"<?php if ($filters['sortby'] == 'surname') { echo ' selected="selected"'; } ?>><?php echo JText::_('LAST_NAME'); ?></option>
						<option value="givenName"<?php if ($filters['sortby'] == 'givenName') { echo ' selected="selected"'; } ?>><?php echo JText::_('FIRST_NAME'); ?></option>
						<option value="org"<?php if ($filters['sortby'] == 'org') { echo ' selected="selected"'; } ?>><?php echo JText::_('ORGANIZATION'); ?></option>
						<option value="total DESC"<?php if ($filters['sortby'] == 'total DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('NUMBER_OF_CONTRIBUTIONS'); ?></option>
					</select>
				</label>
				
				<input type="submit" value="<?php echo JText::_('GO'); ?>" />
			</fieldset>
		
				<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
					<thead>
				 		<tr>
							<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
							<th><?php echo JText::_('ID'); ?></th>
							<th><?php echo JText::_('NAME'); ?></th>
							<th><?php echo JText::_('USERNAME'); ?></th>
							<th><?php echo JText::_('ORGANIZATION'); ?></th>
							<th><?php echo JText::_('VIP'); ?></th>
							<th><?php echo JText::_('NUMBER_OF_CONTRIBUTIONS'); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="7">
								<?php echo $pageNav->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<tbody>
<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row = &$rows[$i];
			
			if (!$row->surname && !$row->givenName) {
				$bits = explode(' ', $row->name);
				$row->surname = array_pop($bits);
				if (count($bits) >= 1) {
					$row->givenName = array_shift($bits);
				}
				if (count($bits) >= 1) {
					$row->middleName = implode(' ',$bits);
				}
			}
?>
						<tr class="<?php echo "row$k"; ?>">
							<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->uidNumber ?>" onclick="isChecked(this.checked);" /></td>
							<td><?php echo $row->uidNumber; ?></td>
							<td><a href="index.php?option=<?php echo $option ?>&amp;task=edit&amp;id[]=<? echo $row->uidNumber; ?>"><?php echo stripslashes($row->surname).', '.stripslashes($row->givenName).' '.stripslashes($row->middleName); ?></a></td>
							<td><?php echo $row->username; ?></td>
							<td><?php echo ($row->organization) ? stripslashes($row->organization) : '&nbsp;';?></td>
							<td><?php echo ($row->vip == 1) ? '<span class="check">'.JText::_('YES').'</span>' : '&nbsp;'; ?></td>
							<td><?php echo $row->rcount; ?></td>
						</tr>
<?php
			$k = 1 - $k;
		}
?>
					</tbody>
				</table>
		
				<input type="hidden" name="option" value="<?php echo $option ?>" />
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
		</form>
<?php
	}

	//-----------

	public function add( $option )
	{
?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;
			
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			submitform( pressbutton );
		}
		</script>

		<form action="index.php" method="post" name="adminForm">
			<fieldset class="adminform">
				<label><?php echo JText::_('USERNAME'); ?>: 
				<input type="text" name="username" value="" /></label>
				<input type="submit" name="submit" value="<?php echo JText::_('NEXT'); ?>" />
				<p><?php echo JText::_('ADD_CONTRIBUTOR_EXPLANATION'); ?></p>
				<input type="hidden" name="option" value="<?php echo $option ?>" />
				<input type="hidden" name="task" value="edit" />
			</fieldset>
		</form>
<?php
	}

	//-----------
	
	public function edit( $profile, $option, $tags ) 
	{
		$name = stripslashes($profile->get('name'));
		$surname = stripslashes($profile->get('surname'));
		$givenName = stripslashes($profile->get('givenName'));
		$middleName = stripslashes($profile->get('middleName'));
		
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
					
					<input type="hidden" name="id" value="<?php echo $profile->get('uidNumber'); ?>" />
					<input type="hidden" name="option" value="<?php echo $option; ?>" />
					<input type="hidden" name="task" value="save" />
					
					<table class="admintable">
					 <tbody>
					  <tr>
					   <td class="key"><label for="public"><?php echo JText::_('PUBLIC_PROFILE'); ?>:</label></td>
					   <td><input type="checkbox" name="profile[public]" id="public" value="1"<?php if ($profile->get('public') == 1) { echo ' checked="checked"'; } ?> /></td>
					  </tr>  
					  <tr>
					   <td class="key"><?php echo JText::_('USERNAME'); ?></td>
					   <td><?php echo $profile->get('username');?> (<?php echo $profile->get('uidNumber'); ?>)</td>
					  </tr>
					  <tr>
					   <td class="key"><?php echo JText::_('CURRENT_PASSWORD'); ?></td>
					   <td><?php echo $profile->get('userPassword'); ?></td>
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
					   <td><input type="checkbox" name="profile[vip]" id="vip" value="1"<?php if ($profile->get('vip') == 1) { echo ' checked="checked"'; } ?> /></td>
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
						$html  = t.t.'<select name="profile[orgtype]" id="orgtype">'.n;
						if (!$profile->get('orgtype')) {
							$html .= t.t.t.'<option value="" selected="selected">'.JText::_('(select from list)').'</option>'.n;
						}
						$html .= t.t.t.'<option value="universityundergraduate"';
						if ($profile->get('orgtype') == 'universityundergraduate') {
							$html .= ' selected="selected"';
						}
						$html .= '>'.JText::_('University / College Undergraduate').'</option>'.n;
						$html .= t.t.t.'<option value="universitygraduate"';
						if ($profile->get('orgtype') == 'universitygraduate') {
							$html .= ' selected="selected"';
						}
						$html .= '>'.JText::_('University / College Graduate Student').'</option>'.n;
						$html .= t.t.t.'<option value="universityfaculty"';
						if ($profile->get('orgtype') == 'universityfaculty' || $profile->get('orgtype') == 'university') {
							$html .= ' selected="selected"';
						}
						$html .= '>'.JText::_('University / College Faculty').'</option>'.n;
						$html .= t.t.t.'<option value="universitystaff"';
						if ($profile->get('orgtype') == 'universitystaff') {
							$html .= ' selected="selected"';
						}
						$html .= '>'.JText::_('University / College Staff').'</option>'.n;
						$html .= t.t.t.'<option value="precollegestudent"';
						if ($profile->get('orgtype') == 'precollegestudent') {
							$html .= ' selected="selected"';
						}
						$html .= '>'.JText::_('K-12 (Pre-College) Student').'</option>'.n;
						$html .= t.t.t.'<option value="precollegefacultystaff"';
						if ($profile->get('orgtype') == 'precollege' || $profile->get('orgtype') == 'precollegefacultystaff') {
							$html .= ' selected="selected"';
						}
						$html .= '>'.JText::_('K-12 (Pre-College) Faculty/Staff').'</option>'.n;
						$html .= t.t.t.'<option value="industry"';
						if ($profile->get('orgtype') == 'industry') {
							$html .= ' selected="selected"';
						}
						$html .= '>'.JText::_('Industry / Private Company').'</option>'.n;
						$html .= t.t.t.'<option value="government"';
						if ($profile->get('orgtype') == 'government') {
							$html .= ' selected="selected"';
						}
						$html .= '>'.JText::_('Government Agency').'</option>'.n;
						$html .= t.t.t.'<option value="military"';
						if ($profile->get('orgtype') == 'military') {
							$html .= ' selected="selected"';
						}
						$html .= '>'.JText::_('Military').'</option>'.n;
						$html .= t.t.t.'<option value="unemployed"';
						if ($profile->get('orgtype') == 'unemployed') {
							$html .= ' selected="selected"';
						}
						$html .= '>'.JText::_('Retired / Unemployed').'</option>'.n;
						$html .= t.t.'</select>'.n;
						echo $html;
						?>
						</td>
					  </tr>  
					<tr>
					   <td class="key"><label for="organization"><?php echo JText::_('ORGANIZATION'); ?>:</label></td>
					   <td><input type="text" name="profile[organization]" id="organization" value="<?php echo htmlentities(stripslashes($profile->get('organization')),ENT_COMPAT,'UTF-8'); ?>" size="50" /></td>
					  </tr>
					  <tr>
				 	   <td class="key"><label for="url"><?php echo JText::_('WEBSITE'); ?>:</label></td>
				 	   <td><input type="text" name="profile[url]" id="url" value="<?php echo htmlentities(stripslashes($profile->get('url')),ENT_COMPAT,'UTF-8'); ?>" size="50" /></td>
				 	  </tr>
					  <tr>
					   <td class="key"><?php echo JText::_('COL_TELEPHONE'); ?>:</td>
					   <td><input type="text" name="profile[phone]" id="phone" value="<?php echo htmlentities($profile->get('phone'),ENT_COMPAT,'UTF-8'); ?>" size="50" /></td>
					  </tr>
					  <tr>
				 	   <td class="key"><label for="tags"><?php echo JText::_('INTERESTS'); ?>:</label></td>
				 	   <td><input type="text" name="tags" id="tags" value="<?php echo $tags; ?>" size="50" /></td>
				 	  </tr>
				 	  <tr>
					   <td class="key" valign="top"><label for="bio"><?php echo JText::_('BIO'); ?>:</label></td>
					   <td>
					        <?php
							jimport('joomla.html.editor');
							$editor = &JEditor::getInstance();
							echo $editor->display('profile[bio]', stripslashes($profile->get('bio')), '360px', '200px', '40', '10');
					        ?>
					  </td>
					  </tr>
					<tr>
						<td class="key" valign="top"><?php echo JText::_('COL_CONTACT_ME'); ?>:</td>
						<td>
							<label><input type="checkbox" id="mailPreferenceOption" name="profile[mailPreferenceOption]" <?php echo ($profile->get('mailPreferenceOption')) ? ' checked="checked"' : ''; ?> value="1" /> Yes, I would like to receive newsletters and other updates by e-mail.</label>
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
						<input type="radio" name="profile[gender]" id="gender_male" value="male" <?php echo ($profile->get('gender') == 'male') ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_GENDER_MALE'); ?><br />
						<input type="radio" name="profile[gender]" id="gender_female" value="female" <?php echo ($profile->get('gender') == 'female') ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_GENDER_FEMALE'); ?><br />
						<input type="radio" name="profile[gender]" id="gender_refused" value="refused" <?php echo ($profile->get('gender') == 'refused') ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_GENDER_REFUSED'); ?>
					   </td>
					  </tr>
					<tr>
					   <td class="key" valign="top"><?php echo JText::_('COL_DISABILITY'); ?>:</td>
					   <td>
						<?php
						$dises = array('no','yes','refused','vocal','blind','deaf','physical','learning');
						$dis = $profile->get('disability');
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
								<legend><label><input type="radio" class="option" name="profile[disability]" id="disabilityyes" value="yes" <?php echo (!$dis_noanswer && !in_array('no',$profile->get('disability')) && !in_array('refused',$profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('YES'); ?></label></legend><br />
								<label><input type="checkbox" class="option" name="profile[disabilities][blind]" id="disabilityblind" value="blind" <?php echo (in_array('blind',$profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_DISABILITY_BLIND'); ?></label><br />
								<label><input type="checkbox" class="option" name="profile[disabilities][deaf]" id="disabilitydeaf" value="deaf" <?php echo (in_array('deaf',$profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_DISABILITY_DEAF'); ?></label><br />
								<label><input type="checkbox" class="option" name="profile[disabilities][physical]" id="disabilityphysical" value="physical" <?php echo (in_array('physical',$profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_DISABILITY_PHYSICAL'); ?></label><br />
								<label><input type="checkbox" class="option" name="profile[disabilities][learning]" id="disabilitylearning" value="learning" <?php echo (in_array('learning',$profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_DISABILITY_LEARNING'); ?></label><br />
								<label><input type="checkbox" class="option" name="profile[disabilities][vocal]" id="disabilityvocal" value="vocal" <?php echo (in_array('vocal',$profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_DISABILITY_VOCAL'); ?></label><br />
								<label>Other (please specify):
								<input name="profile[disabilities][other]" id="disabilityother" type="text" value="<?php echo htmlentities($disother,ENT_COMPAT,'UTF-8'); ?>" /></label>
							</fieldset>
							<label><input type="radio" class="option" name="profile[disability]" id="disabilityno" value="no" <?php echo (in_array('no',$profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('NO_NONE'); ?></label><br />
							<label><input type="radio" class="option" name="profile[disability]" id="disabilityrefused" value="refused" <?php echo (in_array('refused',$profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('REFUSED'); ?></label>
					   </td>
					  </tr>
					<tr>
					   <td class="key" valign="top"><?php echo JText::_('COL_RACE'); ?>:</td>
					   <td>
						<label><input type="checkbox" class="option" name="profile[race][nativeamerican]" id="racenativeamerican" value="nativeamerican" <?php echo (in_array('nativeamerican',$profile->get('race'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_RACE_NATIVE_AMERICAN'); ?></label><br />
						<label style="margin-left: 3em;"><?php echo JText::_('COL_RACE_TRIBE'); ?>: <input name="racenativetribe" id="profile[nativeTribe]" type="text" value="<?php echo htmlentities($profile->get('nativeTribe'),ENT_COMPAT,'UTF-8'); ?>" /></label><br />
						<label><input type="checkbox" class="option" name="profile[race][asian]" id="raceasian" valu="asian" <?php echo (in_array('asian',$profile->get('race'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_RACE_ASIAN'); ?></label><br />
						<label><input type="checkbox" class="option" name="profile[race][black]" id="raceblack" value="black" <?php echo (in_array('black',$profile->get('race'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_RACE_BLACK'); ?></label><br />
						<label><input type="checkbox" class="option" name="profile[race][hawaiian]" id="racehawaiian" value="hawaiian" <?php echo (in_array('hawaiian',$profile->get('race'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_RACE_PACIFIC_ISLANDER'); ?></label><br />
						<label><input type="checkbox" class="option" name="profile[race][white]" id="racewhite" value="white" <?php echo (in_array('white',$profile->get('race'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_RACE_WHITE'); ?></label><br />
						<label><input type="checkbox" class="option" name="profile[race][refused]" id="racerefused" value="refused" <?php echo (in_array('refused',$profile->get('race'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('REFUSED'); ?></label>
					   </td>
					  </tr>
					<tr>
						<td class="key" valign="top"><?php echo JText::_('COL_HISPANIC'); ?>:</td>
						<td>
							<?php
							$hises = array('no','yes','refused','cuban','mexican','deaf','puertorican');
							$his = $profile->get('disability');
							$hisother = '';
							foreach ($his as $h) 
							{
								if (!in_array($h, $hises)) {
									$hisother = $h;
								}
							}
							$his_noanswer = (is_array($his) && count($his) <= 1 && empty($his[0]));
							$hispanic = false;
							if (!empty($his) && !$his_noanswer && !in_array('no',$profile->get('hispanic')) && !in_array('refused',$profile->get('hispanic'))) {
								$hispanic = true;
							}
							?>
							<fieldset>
								<legend><label><input type="radio" class="option" name="profile[hispanic]" id="hispanicyes" value="yes"  <?php echo ($hispanic) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_HISPANIC_YES'); ?></label></legend>
								<label><input type="checkbox" class="option" name="profile[hispanics][cuban]" id="hispaniccuban" value="cuban" <?php echo (in_array('cuban',$profile->get('hispanic'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_HISPANIC_CUBAN'); ?></label><br />
								<label><input type="checkbox" class="option" name="profile[hispanics][mexican]" id="hispanicmexican" value="mexican" <?php echo (in_array('mexican',$profile->get('hispanic'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_HISPANIC_CHICANO'); ?></label><br />
								<label><input type="checkbox" class="option" name="profile[hispanics][puertorican]" id="hispanicpuertorican" value="puertorican" <?php echo (in_array('puertorican',$profile->get('hispanic'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_HISPANIC_PUERTORICAN'); ?></label><br />
								<label><?php echo JText::_('COL_HISPANIC_OTHER'); ?>: <input name="profile[hispanics][other]" id="hispanicother" type="text" value="<?php echo htmlentities($hisother,ENT_COMPAT,'UTF-8'); ?>" /></label>
							</fieldset>
							<label><input type="radio" class="option" name="profile[hispanic]" id="hispanicno" value="no" <?php echo (in_array('no',$profile->get('hispanic'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('COL_HISPANIC_NO'); ?></label><br />
							<label><input type="radio" class="option" name="profile[hispanic]" id="hispanicrefused" value="refused" <?php echo (in_array('refused',$profile->get('hispanic'))) ? 'checked="checked" ' : ''; ?>/> <?php echo JText::_('REFUSED'); ?></label>
						</td>
					</tr>
					 </tbody>
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend><?php echo JText::_('DETAILS'); ?></legend>
					
					<?php
					$html  = t.'<table class="admintable" summary="'.JText::_('ADMIN_PROFILE_TBL_SUMMARY').'">'.n;
					$html .= t.t.'<tbody>'.n;
					$html .= t.t.t.'<tr class="even">'.n;
					$html .= t.t.t.t.'<td class="key">'.JText::_('COL_EXPIRE').'</td>'.n;
					if ( $profile->get('shadowExpire') > 0 )
						$html .= t.t.t.t.'<td> <label><input type="checkbox" name="shadowExpire" id="shadowExpire" value="1" checked="checked"/></label></td>';
					else
					     $html .= t.t.t.t.'<td> <label><input type="checkbox" name="shadowExpire" id="shadowExpire" value="1"/></label></td>';
					$html .= t.t.t.'</tr>'.n;
					//$html .= t.t.t.'<tr class="even">'.n;
					//$html .= t.t.t.t.'<td class="key">'.JText::_('COL_PASSWORD').'</td>'.n;
					//$html .= t.t.t.t.'<td><a href="/password/lost">'.JText::_('RESET_PASSWORD').'</a></td>'.n;
					//$html .= t.t.t.'</tr>'.n;
					$html .= t.t.t.'<tr class="odd">'.n;
					$html .= t.t.t.t.'<td class="key">'.JText::_('COL_JOBS_ALLOWED').'</td>'.n;
					$html .= t.t.t.t.'<td><input type="text" name="profile[jobsAllowed]" id="jobsAllowed" value="'.$profile->get('jobsAllowed').'" size="10" /></td>'.n;
					$html .= t.t.t.'</tr>'.n;
					$html .= t.t.t.'<tr class="odd">'.n;
					$html .= t.t.t.t.'<td class="key">'.JText::_('COL_CITIZENSHIP').'</td>'.n;
					$html .= t.t.t.t.'<td>'.$profile->get('countryorigin').'</td>'.n;
					$html .= t.t.t.'</tr>'.n;
					$html .= t.t.t.'<tr class="even">'.n;
					$html .= t.t.t.t.'<td class="key">'.JText::_('COL_RESIDENCE').'</td>'.n;
					$html .= t.t.t.t.'<td>'.$profile->get('countryresident').'</td>'.n;
					$html .= t.t.t.'</tr>'.n;
					/*$html .= t.t.t.'<tr class="odd">'.n;
					$html .= t.t.t.t.'<td class="key">'.JText::_('COL_EMPLOYMENT_STATUS').'</td>'.n;
					$html .= t.t.t.t.'<td>';
					$html .= ($profile->get('orgtype')) ? JText::_(strtoupper($profile->get('orgtype'))) : '';
					$html .= '</td>'.n;
					$html .= t.t.t.'</tr>'.n;*/
					if ($profile->get('emailConfirmed') == 1) {
						$confirmed = '<label><input type="checkbox" name="emailConfirmed" id="emailConfirmed" value="1" checked="checked" /> '.JText::_('EMAIL_CONFIRMED').'</label>';
					} elseif ($profile->get('emailConfirmed') == 2) {
						$confirmed = JText::_('EMAIL_GRANDFATHERED').'<input type="hidden" name="emailConfirmed" id="emailConfirmed" value="2" />';
					} elseif ($profile->get('emailConfirmed') == 3) {
						$confirmed = JText::_('EMAIL_DOMAIN_SUPPLIED').'<input type="hidden" name="emailConfirmed" id="emailConfirmed" value="3" />';
					} elseif ($profile->get('emailConfirmed') < 0) {
						if ($profile->get('email')) {
							$confirmed  = JText::_('EMAIL_AWAITING_CONFIRMATION');
							$confirmed .= '<br />[code: ' . -$profile->get('emailConfirmed') . '] <label><input type="checkbox" name="emailConfirmed" id="emailConfirmed" value="1" /> '.JText::_('EMAIL_CONFIRM').'</label>';
						} else {
							$confirmed  = JText::_('EMAIL_NONE_ON_FILE');
						}
					} else {
						$confirmed  = '['.JText::_('EMAIL_UNKNOWN_STATUS').'] <label><input type="checkbox" name="emailConfirmed" id="emailConfirmed" value="1" /> '.JText::_('EMAIL_CONFIRM').'</label>';
					}
					$html .= t.t.t.'<tr class="even">'.n;
					$html .= t.t.t.t.'<td class="key">'.JText::_('COL_EMAIL').'</td>'.n;
					$html .= t.t.t.t.'<td>';
					if ($profile->get('email')) {
						//$html .= '<a href="mailto:'.stripslashes($profile->get('email')).'">'.stripslashes($profile->get('email')).'</a> ('.$confirmed.')'.n;
						$html .= '<input type="text" name="profile[email]" id="email" value="'.htmlentities(stripslashes($profile->get('email')),ENT_COMPAT,'UTF-8').'" size="20" /> ('.$confirmed.')'.n;
					} else {
						$html .= '<span style="color:#c00;">'.JText::_('EMAIL_NONE_ON_FILE').'</span>';
						$html .= '<br /><input type="text" name="profile[email]" id="email" value="" size="20" /> <label><input type="checkbox" name="emailConfirmed" id="emailConfirmed" value="1" /> '.JText::_('EMAIL_CONFIRM').'</label>';
					}
					$html .= '</td>'.n;
					$html .= t.t.t.'</tr>'.n;
					$html .= t.t.t.'<tr class="odd">'.n;
					$html .= t.t.t.t.'<td class="key">'.JText::_('COL_ADMINISTRATOR').'</td>'.n;
					$html .= t.t.t.t.'<td>'.implode(', ',$profile->get('admin')).'</td>'.n;
					$html .= t.t.t.'</tr>'.n;
					$html .= t.t.t.'<tr class="odd">'.n;
					$html .= t.t.t.t.'<td class="key">'.JText::_('COL_HOMEDIRECTORY').'</td>'.n;
					$html .= t.t.t.t.'<td>'.$profile->get('homeDirectory').'</td>'.n;
					$html .= t.t.t.'</tr>'.n;
					$html .= t.t.t.'<tr class="even">'.n;
					$html .= t.t.t.t.'<td class="key">'.JText::_('COL_REGHOST').'</td>'.n;
					$html .= t.t.t.t.'<td>';
					$html .= ($profile->get('regHost')) ? $profile->get('regHost') : '-' ;
					$html .= ($profile->get('regIP')) ?' ('.$profile->get('regIP').')' : '';
					$html .= '</td>'.n;
					$html .= t.t.t.'</tr>'.n;
					$html .= t.t.t.'<tr class="even">'.n;
					$html .= t.t.t.t.'<td class="key">'.JText::_('COL_MODIFIED').'</td>'.n;
					$html .= t.t.t.t.'<td>'.$profile->get('modifiedDate').'</td>'.n;
					$html .= t.t.t.'</tr>'.n;
					$html .= t.t.'</tbody>'.n;
					$html .= t.'</table>'.n;
					echo $html;
					?>
				</fieldset>
			</div>
			<div class="col width-40">
				<table width="100%" style="border: 1px dashed silver; padding: 5px; margin-bottom: 10px;">
					<tbody>
						<?php
						$database =& JFactory::getDBO();
						$database->setQuery("SELECT du.*, d.domain FROM #__xdomain_users AS du, #__xdomains AS d WHERE du.domain_id=d.domain_id AND du.uidNumber=".$profile->get('uidNumber'));
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
					if ($profile->get('uidNumber') != '') {
						$pics = stripslashes($profile->get('picture'));
						$pics = explode(DS, $pics);
						$file = end($pics);
					?>
					<iframe width="100%" height="350" name="filer" id="filer" frameborder="0" src="index3.php?option=<?php echo $option; ?>&amp;task=img&amp;file=<?php echo $file; ?>&amp;id=<?php echo $profile->get('uidNumber'); ?>"></iframe>
					<?php
					} else {
						echo '<p class="warning">'.JText::_('MEMBER_PICTURE_ADDED_LATER').'</p>';
					}
					?>
				</fieldset>
				
				<fieldset class="adminform">
					<legend><?php echo JText::_('GROUPS'); ?></legend>
					
					<iframe width="100%" height="200" name="grouper" id="grouper" frameborder="0" src="index3.php?option=<?php echo $option; ?>&amp;task=group&amp;id=<?php echo $profile->get('uidNumber'); ?>"></iframe>
				</fieldset>
				
				<fieldset class="adminform">
					<legend><?php echo JText::_('HOSTS'); ?></legend>

					<iframe width="100%" height="200" name="hosts" id="hosts" frameborder="0" src="index3.php?option=<?php echo $option; ?>&amp;task=hosts&amp;id=<?php echo $profile->get('uidNumber'); ?>"></iframe>
				</fieldset>
				
				<fieldset class="adminform">
					<legend><?php echo JText::_('Managers'); ?></legend>

					<iframe width="100%" height="200" name="managers" id="managers" frameborder="0" src="index3.php?option=<?php echo $option; ?>&amp;task=managers&amp;id=<?php echo $profile->get('uidNumber'); ?>"></iframe>
				</fieldset>
			</div>
			<div class="clr"></div>
		</form>
		<?php
	}
	
	//-----------
	
	public function writeImage( $app, $option, $webpath, $defaultpic, $path, $file, $file_path, $id, $errors=array() )
	{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<title><?php echo JText::_('MEMBER_PICTURE'); ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<style type="text/css" media="screen">@import url(/templates/<?php echo $app->getTemplate(); ?>/css/main.css);</style>
	<style type="text/css" media="screen">
	body { min-width: 20px; background: #fff; margin: 0; padding: 0; }
	</style>
 </head>
 <body>
   <form action="index2.php" method="post" enctype="multipart/form-data" name="filelist" id="filelist">
	<table class="formed">
	 <thead>
	  <tr>
	   <th><label for="image"><?php echo JText::_('UPLOAD'); ?> <?php echo JText::_('WILL_REPLACE_EXISTING_IMAGE'); ?></label></th>
	  </tr>
	 </thead>
	 <tbody>
	  <tr>
	   <td>
	    <input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="no_html" value="1" />
		<input type="hidden" name="id" value="<?php echo $id; ?>" />
		<input type="hidden" name="task" value="upload" />
		
		<input type="file" name="upload" id="upload" size="17" />&nbsp;&nbsp;&nbsp;
		<input type="submit" value="<?php echo JText::_('UPLOAD'); ?>" />
	   </td>
	  </tr>
	 </tbody>
	</table>
	<?php
		if (count($errors) > 0) {
			echo MembersHtml::error( implode('<br />',$errors) ).n;
		}
	?>
	<table class="formed">
	 <thead>
	  <tr>
	   <th colspan="4"><label for="image"><?php echo JText::_('MEMBER_PICTURE'); ?></label></th>
	  </tr>
	 </thead>
	 <tbody>
<?php
	$k = 0;

	if ($file && file_exists( $file_path.DS.$file )) {
		$this_size = filesize($file_path.DS.$file);
		list($width, $height, $type, $attr) = getimagesize($file_path.DS.$file);
?>
	  <tr>
	   <td rowspan="6"><img src="<?php echo '../'.$webpath.DS.$path.DS.$file; ?>" alt="<?php echo JText::_('MEMBER_PICTURE'); ?>" id="conimage" /></td>
	   <td><?php echo JText::_('FILE'); ?>:</td>
	   <td><?php echo $file; ?></td>
	  </tr>
	  <tr>
	   <td><?php echo JText::_('SIZE'); ?>:</td>
	   <td><?php echo FileUploadUtils::formatsize($this_size); ?></td>
	  </tr>
	  <tr>
	   <td><?php echo JText::_('WIDTH'); ?>:</td>
	   <td><?php echo $width; ?> px</td>
	  </tr>
	  <tr>
	   <td><?php echo JText::_('HEIGHT'); ?>:</td>
	   <td><?php echo $height; ?> px</td>
	  </tr>
	  <tr>
	   <td><input type="hidden" name="currentfile" value="<?php echo $file; ?>" /></td>
	   <td><a href="index3.php?option=<?php echo $option; ?>&amp;task=deleteimg&amp;file=<?php echo $file; ?>&amp;id=<?php echo $id; ?>">[ <?php echo JText::_('DELETE'); ?> ]</a></td>
	  </tr>
<?php } else { ?>
	  <tr>
	   <td colspan="4"><img src="<?php echo '..'.$defaultpic; ?>" alt="<?php echo JText::_('NO_MEMBER_PICTURE'); ?>" />
		<input type="hidden" name="currentfile" value="" /></td>
	  </tr>
<?php } ?>
	 </tbody>
	</table>
   </form>
 </body>
</html>
<?php
	}

	//-----------

	public function writeGroups( $app, $option, $id, $rows, $errors=array() )
	{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<title><?php echo JText::_('MEMBER_GROUPS'); ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<style type="text/css" media="screen">@import url(/templates/<?php echo $app->getTemplate(); ?>/css/main.css);</style>
	<style type="text/css" media="screen">
	body { min-width: 20px; background: #fff; margin: 0; padding: 0; }
	</style>
 </head>
 <body>
	<form action="index2.php" method="post">
		<table>
		 <tbody>
		  <tr>
		   <td>
		    <input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="no_html" value="1" />
			<input type="hidden" name="id" value="<?php echo $id; ?>" />
			<input type="hidden" name="task" value="addgroup" />

			<select name="gid" style="width: 15em;">
				<option value=""><?php echo JText::_('Select...'); ?></option>
				<?php
				foreach ($rows as $row) 
				{
					echo '<option value="'.$row->gidNumber.'">'.$row->description.' ('.$row->cn.')</option>'.n;
				}
				?>
			</select>
			<select name="tbl">
				<option value="invitees"><?php echo JText::_('INVITEES'); ?></option>
				<option value="applicants"><?php echo JText::_('APPLICANTS'); ?></option>
				<option value="members" selected="selected"><?php echo JText::_('MEMBERS'); ?></option>
				<option value="managers"><?php echo JText::_('MANAGERS'); ?></option>
			</select>
			<input type="submit" value="<?php echo JText::_('ADD_GROUP'); ?>" />
		   </td>
		  </tr>
		 </tbody>
		</table>
		<br />
		<table class="paramlist admintable">
			<tbody>
		<?php
		ximport('xuserhelper');
		
		$applicants = XUserHelper::getGroups( $id, 'applicants' );
		$invitees = XUserHelper::getGroups( $id, 'invitees' );
		$members = XUserHelper::getGroups( $id, 'members' );
		$managers = XUserHelper::getGroups( $id, 'managers' );

		$applicants = (is_array($applicants)) ? $applicants : array();
		$invitees = (is_array($invitees)) ? $invitees : array();
		$members = (is_array($members)) ? $members : array();
		$managers = (is_array($managers)) ? $managers : array();

		$groups = array_merge($applicants, $invitees);
		$managerids = array();
		foreach ($managers as $manager) 
		{
			$groups[] = $manager;
			$managerids[] = $manager->cn;
		}
		foreach ($members as $mem) 
		{
			if (!in_array($mem->cn,$managerids)) {
				$groups[] = $mem;
			}
		}
		
		if (count($groups) > 0) {
			foreach ($groups as $group)
			{
				?>
				<tr>
					<td class="paramlist_key"><a href="index.php?option=com_groups&amp;task=manage&amp;gid=<?php echo $group->cn; ?>" target="_parent"><?php echo $group->description.' ('.$group->cn.')'; ?></a></td>
					<td class="paramlist_value"><?php 
					$seen[] = $group->cn;
					
					if ($group->registered) {
						$status = JText::_('applicant');
						if ($group->regconfirmed) {
							$status = JText::_('member');
							if ($group->manager) {
								$status = JText::_('manager');
							}
						}
					} else {
						$status = JText::_('invitee');
					}
					echo $status; ?></td>
				</tr>
				<?php
			}
		}
		?>
			</tbody>
		</table>
	</form>
 </body>
</html>
	<?php
	}
	
	//-----------

	public function writeHosts( $app, $option, $id, $rows, $errors=array() )
	{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<title><?php echo JText::_('MEMBER_HOSTS'); ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<style type="text/css" media="screen">@import url(/templates/<?php echo $app->getTemplate(); ?>/css/main.css);</style>
	<style type="text/css" media="screen">
	body { min-width: 20px; background: #fff; margin: 0; padding: 0; }
	</style>
 </head>
 <body>
	<form action="index.php" method="post">
		<table>
		 <tbody>
		  <tr>
		   <td>
		    <input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="no_html" value="1" />
			<input type="hidden" name="id" value="<?php echo $id; ?>" />
			<input type="hidden" name="task" value="addhost" />

			<input type="text" name="host" value="" /> 
			<input type="submit" value="<?php echo JText::_('ADD_HOST'); ?>" />
		   </td>
		  </tr>
		 </tbody>
		</table>
		<br />
		<table class="paramlist admintable">
			<tbody>
		<?php
		if (count($rows) > 0) {
			foreach ($rows as $row)
			{
				?>
				<tr>
					<td class="paramlist_key"><?php echo $row; ?></td>
					<td class="paramlist_value"><a href="index.php?option=<?php echo $option; ?>&amp;no_html=1&amp;task=deletehost&amp;host=<?php echo $row; ?>&amp;id=<?php echo $id; ?>"><?php echo JText::_('DELETE'); ?></a></td>
				</tr>
				<?php
			}
		}
		?>
			</tbody>
		</table>
	</form>
 </body>
</html>
	<?php
	}
	
	//-----------

	public function writeManagers( $app, $option, $id, $rows, $errors=array() )
	{
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	 <head>
		<title><?php echo JText::_('MEMBER_HOSTS'); ?></title>

		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

		<style type="text/css" media="screen">@import url(/templates/<?php echo $app->getTemplate(); ?>/css/main.css);</style>
		<style type="text/css" media="screen">
		body { min-width: 20px; background: #fff; margin: 0; padding: 0; }
		</style>
	 </head>
	 <body>
		<form action="index.php" method="post">
			<table>
			 <tbody>
			  <tr>
			   <td>
			    <input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="no_html" value="1" />
				<input type="hidden" name="id" value="<?php echo $id; ?>" />
				<input type="hidden" name="task" value="addmanager" />

				<input type="text" name="manager" value="" /> 
				<input type="submit" value="<?php echo JText::_('Add Manager'); ?>" />
			   </td>
			  </tr>
			 </tbody>
			</table>
			<br />
			<table class="paramlist admintable">
				<tbody>
			<?php
			if (count($rows) > 0) {
				foreach ($rows as $row)
				{
					?>
					<tr>
						<td class="paramlist_key"><?php echo $row; ?></td>
						<td class="paramlist_value"><a href="index.php?option=<?php echo $option; ?>&amp;no_html=1&amp;task=deletemanager&amp;manager=<?php echo $row; ?>&amp;id=<?php echo $id; ?>"><?php echo JText::_('DELETE'); ?></a></td>
					</tr>
					<?php
				}
			}
			?>
				</tbody>
			</table>
		</form>
	 </body>
	</html>
	<?php
	}
}
?>
