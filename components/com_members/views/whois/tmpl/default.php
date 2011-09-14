<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     GNU General Public License, version 2 (GPLv2) 
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<div class="main section">
	
	<form name="whoFrom" id="hubForm" action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=whois'); ?>" method="post" enctype="multipart/form-data">
		<div class="explaination">
			<div class="info">
				<h4><?php echo JText::_('Administrative Options'); ?></h4>
				<p><a href="<?php echo JRoute::_('index.php?option=com_register&task=proxycreate')?>"><?php echo JText::_('Proxy Create New User'); ?></a></p>
			</div>
		</div>
		
		<fieldset>
			<h3>Lookup</h3>
			<p>
				Enter the search parameters for the user(s) you wish to lookup.
				Searches are made against Name (name), E-mail (email), Login (username), 
				User ID (uidNumber), Email Confirmed (emailConfirmed), and Proxy UID Number 
				(proxyUidNumber) fields, as well as the logical search Proxy Confirmed 
				(proxyConfirmed) in the members directory.  You may specify the field to search 
				or allow the field to be chosen based on your input.  Wild-card characters 
				'*' and '?' may be used.  The operators '&lt=' and '&gt=' are 
				replaced by '-=' and '+=', respectively, to avoid HTML problems.
			</p>
			<p>
				Search is limited to first 100 results.
			</p>
	
			<label>
				<input type="text" name="query" size="30" value="<?php echo $this->query; ?>" />
				<span class="hint">[ (name|email|username|uidNumber|emailConfirmed|...) (=|-=|+=|!=) ] value [,...]</span>
			</label>
			<input type="hidden" name="task" value="whois" />
			<input type="hidden" name="option" value="com_members" />
		</fieldset>
		<div class="clear"></div>
		
		<p class="submit"><input type="submit" value="Submit" /></p>
	</form>
	<?php
	if ($this->getError()) {
		echo '<p class="error">'.$this->getError().'</p>';
	}
	?>
	<?php
	if ($this->summaries) {
	?>
	<table summary="<?php echo JText::_('Matches to the query performed'); ?>">
		<thead>
			<tr>
				<th colspan="3"><?php echo JText::_('Matches'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		$cls = 'even';
		for ($i = 0; $i < count($this->summaries); $i++)
		{
			$cls = ($cls == 'odd') ? 'even' : 'odd';

			$html  = t.t.'<tr class="'. $cls .'">'.n;
			$html .= t.t.t.'<td><a href="'.JRoute::_('index.php?option='.$this->option.a.'id='. $this->summaries[$i]->uidNumber).'">';
			$html .= $this->summaries[$i]->username .'</a> (' . $this->summaries[$i]->uidNumber .') </td>'.n;
			$html .= t.t.t.'<td>'. stripslashes($this->summaries[$i]->name) .'</td>'.n;
			$html .= t.t.t.'<td>'. $this->summaries[$i]->email .'</td>'.n;
			$html .= t.t.'</tr>'.n;
			echo $html;
		}
		?>
		</tbody>
	</table>
	<?php
	}
	?>
	<?php
	$xprofile = null;
	if ($this->user) {
		$xprofile = new Hubzero_User_Profile();
		$xprofile->load( $this->user );
	}
	if (is_object($xprofile)) {
		if (($xprofile->get('emailConfirmed') != 1) && ($xprofile->get('emailConfirmed') != 3)) {
			echo '<p class="warning">This account will not be activated until they confirm receipt of email at the address listed below.</p>';
		}
	?>
	<table summary="Account Details">
		<caption>Account Details</caption>
		<tbody>
			<tr class="odd">
				<th>Full Name:</th>
				<td><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$xprofile->get('uidNumber')); ?>"><?php echo $xprofile->get('name'); ?></a></td>
			</tr>
			<tr class="even">
				<th>Username:</th>
				<td><?php echo $xprofile->get('username') .' ('. $xprofile->get('uidNumber').')'; ?></td>
			</tr>
			<tr class="odd">
				<th>Organization or School:</th>
				<td><?php echo $xprofile->get('organization'); ?></td>
			</tr>
			<tr class="even">
				<th>Employment Status:</th>
				<td><?php
				switch ($xprofile->get('orgtype'))
				{
					case '':
						echo 'n/a';
						break;
					case 'universitystudent':
						echo JText::_('University / College Student');
						break;
					case 'university':
					case 'universityfaculty':
						echo JText::_('University / College Faculty');
						break;
					case 'universitystaff':
						echo JText::_('University / College Staff');
						break;
					case 'precollege':
					case 'precollegefacultystaff':
						echo JText::_('K-12 (Pre-College) Faculty or Staff');
						break;
					case 'precollegestudent':
						echo JText::_('K-12 (Pre-College) Student');
						break;
					case 'industry':
						echo JText::_('Industry / Private Company');
						break;
					case 'government':
						echo JText::_('Government Agency');
						break;
					case 'military':
						echo JText::_('Military');
						break;
					case 'unemployed':
						echo JText::_('Retired / Unemployed');
						break;
					default:
						echo $xprofile->get('orgtype');
						break;
				}
				?></td>
			</tr>
			<tr class="odd">
				<th>E-mail:</th>
				<td><?php echo $xprofile->get('email');
				if ($xprofile->get('emailConfirmed') == 1) {
					echo '<br />(confirmed)';
				} elseif ($xprofile->get('emailConfirmed') == 2) {
					echo '<br />(grandfathered account)';
				} elseif ($xprofile->get('emailConfirmed') == 3) {
					echo '<br />(domain supplied email)';
				} elseif ($xprofile->get('emailConfirmed') < 0) {
					if ($xprofile->get('email')) {
						echo '<br /><span style="color: red;">(awaiting confirmation)</span>';
						echo '<br />[code: ' . -$xprofile->get('emailConfirmed') . ']';
					} else {
						echo '<br /><span style="color: red;">(no email address on file)</span>';
					}
				} else {
					echo '<br /><span style="color: red;">(unknown confirmation status)</span>';
				}
				?></td>
			</tr>
			<tr class="even">
				<th>Contact Me:</th>
				<td><?php
				if ($xprofile->get('mailPreferenceOption') != 0) {
					echo 'Yes, I wish to receive newsletters and other updates by e-mail.';
				} else {
					echo 'No, I do not wish to receive newsletters and other updates by e-mail.';
				}
				?></td>
			</tr>
			<tr class="odd">
				<th>Host Access:</th>
				<td><?php
				$hosts = $xprofile->get('host');
				$count = (!empty($hosts)) ? count($hosts) : 0;
				for ($i = 0;$i < $count; $i++)
				{
					echo htmlentities($hosts[$i]) .' ';
				}
				?></td>
			</tr>
			<tr class="even">
				<th>Jobs Allowed:</th>
				<td><?php echo $xprofile->get('jobsAllowed'); ?></td>
			</tr>
			<tr class="odd">
				<th>Citizenship:</th>
				<td><?php echo $xprofile->get('countryorigin'); ?></td>
			</tr>
			<tr class="even">
				<th>Residence:</th>
				<td><?php echo $xprofile->get('countryresident'); ?></td>
			</tr>
			<tr class="odd">
				<th>Sex:</th>
				<td><?php echo MembersHtml::propercase_singleresponse($xprofile->get('gender')); ?></td>
			</tr>
			<tr class="even">
				<th>Racial Background:</th>
				<td><?php echo MembersHtml::propercase_multiresponse($xprofile->get('race')); ?></td>
			</tr>
			<tr class="odd">
				<th>Hispanic Heritage:</th>
				<td><?php echo MembersHtml::propercase_multiresponse($xprofile->get('hispanic')); ?></td>
			</tr>
			<tr class="even">
				<th>Disability:</th>
				<td><?php echo MembersHtml::propercase_multiresponse($xprofile->get('disability')); ?></td>
			</tr>
			<tr class="odd">
				<th>Telephone:</th>
				<td><?php echo $xprofile->get('phone'); ?></td>
			</tr>
			<tr class="even">
				<th>Home Directory:</th>
				<td><?php echo $xprofile->get('homeDirectory'); ?></td>
			</tr>
			<tr class="odd">
				<th>Reason for Account:</th>
				<td><?php echo $xprofile->get('reason'); ?></td>
			</tr>
			<tr class="even">
				<th>Created from Host:<br />(IP Address)</th>
				<td><?php 
				$reg_host = $xprofile->get('regHost');
				$reg_ip   = $xprofile->get('regIP');
				$reg_host = (empty($reg_host)) ? "n/a" : $reg_host;
				$reg_ip   = (empty($reg_ip)) ? "n/a" : $reg_ip;

				echo $reg_host .'<br />('. $reg_ip .')';
				?></td>
			</tr>
			<tr class="odd">
				<th>Proxy Created By:</th>
				<td><?php
				$proxy = $xprofile->get('proxyUidNumber');
				if ($proxy) {
					$proxyuser = new Hubzero_User_Profile();
					$proxyuser->load( $proxy );
					if (!empty($proxyuser)) {
						echo $proxyuser->get('name') . ' (<a href="'.JRoute::_('index.php?option=com_members&task=view&id=' . $proxyuser->get('uidNumber')) . '">' . $proxyuser->get('username') . '</a>)';
					} else {
						echo 'Unknown (' . $proxy . ')';
					}
				} else {
					echo 'n/a';
				}
				?></td>
			</tr>
			<tr class="even">
				<th>Created On:</th>
				<td><?php
				if ($xprofile->get('registerDate')) {
					echo str_replace('  ', '&nbsp;&nbsp;', date("F j, Y  g:ia", MembersHtml::date2epoch($xprofile->get('registerDate'))));
				} else {
					echo 'n/a';
				}
				?></td>
			</tr>
			<tr class="odd">
				<th>Last Modified On:</th>
				<td><?php
				if ($xprofile->get('modifiedDate')) {
					echo str_replace('  ', '&nbsp;&nbsp;', date("F j, Y  g:ia", MembersHtml::date2epoch($xprofile->get('modifiedDate'))));
				} else {
					echo 'n/a';
				}
				?></td>
			</tr>
		</tbody>
	</table>
	<?php
	}
	?>
</div><!-- / .section -->

