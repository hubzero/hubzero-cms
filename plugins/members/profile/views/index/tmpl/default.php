<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
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

//get user object
$juser =& JFactory::getUser();

//flags for not logged in and not user
$loggedin = false;
$isUser = false;

//if we are logged in set logged in flag
if (!$juser->get('guest')) 
{
	$loggedin = true;
}

//if we are this user set user flag
if($juser->get("id") == $this->profile->get("uidNumber")) 
{
	$isUser = true;
}

//array of org types and proper display text
$org_types = array(
	'' => JText::_('n/a'),
	'universityundergraduate' => JText::_('University / College Undergraduate'),
	'universitygraduate' => JText::_('University / College Graduate'),
	'universitystudent' => JText::_('University / College Student'),
	'university' => JText::_('University / College Faculty'),
	'universityfaculty' => JText::_('University / College Faculty'),
	'universitystaff' => JText::_('University / College Staff'),
	'precollege' => JText::_('K-12 (Pre-College) Faculty or Staff'),
	'precollegefacultystaff' => JText::_('K-12 (Pre-College) Faculty or Staff'),
	'precollegestudent' => JText::_('K-12 (Pre-College) Student'),
	'nationallab' => JText::_('National Laboratory'),
	'industry' => JText::_('Industry / Private Company'),
	'government' => JText::_('Government Agency'),
	'military' => JText::_('Military'),
	'unemployed' => JText::_('Retired / Unemployed')
);
?>


<h3 class="section-header"><?php echo JText::_('PROFILE'); ?></h3>

<?php if($isUser) : ?>
	<form action="/members/<?php echo $this->profile->get('uidNumber'); ?>/profile" method="POST" id="member-profile">
<?php endif; ?>
	
		<table id="profile_info">
			<?php if($isUser) : ?>
				<noscript>
					<tfoot>
						<tr>
							<td colspan="3">
								<input type="submit" name="submit" value="Save Changes" />
							</td>
						</tr>
					</tfoot>
				</noscript>
			<?php endif; ?>
			
			<tbody>
				<?php if ($this->registration->Organization != REG_HIDE) : ?>
					<?php if ($this->params->get('access_org') == 0 
							|| ($this->params->get('access_org') == 1 && $loggedin) 
							|| ($this->params->get('access_org') == 2 && $isUser)
							) : ?>
						<tr <?php if($this->params->get('access_org') == 2) { echo "class=\"private\""; } ?>>
							<td class="key"><?php echo JText::_('COL_ORGANIZATION'); ?></td>
							<td><?php echo Hubzero_View_Helper_Html::xhtml(stripslashes($this->profile->get('organization'))); ?></td>
							<td class="privacy">
								<?php 
									if($isUser) {
										echo MembersHtml::selectAccess('access[org]',$this->params->get('access_org'));
									}
								?>
							</td>
						</tr>
					<?php endif; ?>
				<?php endif; ?>
				
				<?php if ($this->registration->Employment != REG_HIDE) : ?>
					<?php if ($this->params->get('access_orgtype') == 0 
					 		|| ($this->params->get('access_orgtype') == 1 && $loggedin) 
					 		|| ($this->params->get('access_orgtype') == 2 && $isUser)
							) : ?>
						<tr <?php if($this->params->get('access_orgtype') == 2) { echo "class=\"private\""; } ?>>
							<td class="key"><?php echo JText::_('Employment Status'); ?></td>
							<td>
								<?php
									if(array_key_exists($this->profile->get("orgtype"), $org_types)) {
										echo $org_types[$this->profile->get("orgtype")];
									} else {
										echo htmlentities($this->profile->get('orgtype'),ENT_COMPAT,'UTF-8');
									}
								?>
							</td>
							<td class="privacy">
								<?php 
									if($isUser) {
										echo MembersHtml::selectAccess('access[orgtype]',$this->params->get('access_orgtype'));
									}
								?>
							</td>
						</tr>
					<?php endif; ?>
				<?php endif; ?>
				
				<?php if ($this->profile->get('email')) : ?>
					<?php if ($this->params->get('access_email') == 0 
					 		|| ($this->params->get('access_email') == 1 && $loggedin) 
					 		|| ($this->params->get('access_email') == 2 && $isUser)
							) : ?>
						<tr <?php if($this->params->get('access_email') == 2) { echo "class=\"private\""; } ?>>
							<td class="key">E-mail</td>
							<td><a class="email" href="mailto:<?php echo MembersHtml::obfuscate($this->profile->get('email')); ?>" rel="nofollow"><?php echo MembersHtml::obfuscate($this->profile->get('email')); ?></a></td>
							<td class="privacy">
								<?php
									if($isUser) {
										echo MembersHtml::selectAccess('access[email]',$this->params->get('access_email'));
									}
								?>
							</td>
						</tr>
					<?php endif; ?>
				<?php endif; ?>
				
				
				<?php if ($this->registration->URL != REG_HIDE) : ?>
					<?php if ($this->params->get('access_url') == 0 
					 		|| ($this->params->get('access_url') == 1 && $loggedin) 
					 		|| ($this->params->get('access_url') == 2 && $isUser)
						) : ?>
						<tr <?php if($this->params->get('access_url') == 2) { echo "class=\"private\""; } ?>>
							<td class="key"><?php echo JText::_('COL_WEBSITE'); ?></td>
							<td>
								<?php
									$url = stripslashes($this->profile->get('url'));
									$title = $this->profile->get("name") . "'s Website";
									echo ($url) ? "<a class=\"url\" rel=\"external\" title=\"{$title}\" href=\"{$url}\">{$url}</a>" : JText::_('None');
								?>
							</td>
							<td class="privacy">
								<?php
									if($isUser) {
										echo MembersHtml::selectAccess('access[url]',$this->params->get('access_url'));
									}
								?>
							</td>
						</tr>
					<?php endif; ?>
				<?php endif; ?>
				
				<?php if ($this->registration->Phone != REG_HIDE) : ?>
					<?php if ($this->params->get('access_phone') == 0 
					 		|| ($this->params->get('access_phone') == 1 && $loggedin) 
					 		|| ($this->params->get('access_phone') == 2 && $isUser)
						) : ?>
						<tr <?php if($this->params->get('access_phone') == 2) { echo "class=\"private\""; } ?>>
							<td class="key"><?php echo JText::_('Telephone'); ?></td>
							<td>
								<?php
									$tel = htmlentities($this->profile->get('phone'),ENT_COMPAT,'UTF-8');
									$tel = str_replace(".","-",$tel);
									$tel = str_replace(" ","-",$tel);
									echo ($tel) ? "<a class=\"phone\" href=\"{$tel}\">{$tel}</a>" : JText::_('None');
								?>
							</td>
							<td class="privacy">
								<?php
									if($isUser) {
										echo MembersHtml::selectAccess('access[phone]',$this->params->get('access_phone'));
									}
								?>
							</td>
						</tr>
					<?php endif; ?>
				<?php endif; ?>
				
				<?php if ($this->params->get('access_bio') == 0 
				 		|| ($this->params->get('access_bio') == 1 && $loggedin) 
				 		|| ($this->params->get('access_bio') == 2 && $isUser)
					) : ?>
					<tr class="bio <?php if($this->params->get('access_bio') == 2) { echo " private"; } ?>">
						<td class="key"><?php echo JText::_('COL_BIOGRAPHY'); ?></td>
						<td>
							<?php
								if ($this->profile->get('bio')) {
									$wikiconfig = array(
										'option'   => $this->option,
										'scope'    => 'members'.DS.'profile',
										'pagename' => 'member',
										'pageid'   => 0,
										'filepath' => '',
										'domain'   => '' 
									);
									ximport('Hubzero_Wiki_Parser');
									$p =& Hubzero_Wiki_Parser::getInstance();
									$bio = $p->parse(stripslashes($this->profile->get('bio')), $wikiconfig);
								} else {
									$bio = JText::_('NO_BIOGRAPHY');
								}
								
								echo $bio;
							?>
						</td>
						<td class="privacy">
							<?php
								if($isUser) {
									echo MembersHtml::selectAccess('access[bio]',$this->params->get('access_bio'));
								}
							?>
						</td>
					</tr>
				<?php endif; ?>
				
				<?php if ($this->registration->Interests != REG_HIDE) : ?>
					<?php if ($this->params->get('access_tags') == 0 
					 		|| ($this->params->get('access_tags') == 1 && $loggedin) 
					 		|| ($this->params->get('access_tags') == 2 && $isUser)
						) : ?>
						<tr <?php if($this->params->get('access_tags') == 2) { echo "class=\"private\""; } ?>>
							<td class="key"><?php echo JText::_('COL_INTERESTS'); ?></td>
							<td>
								<?php
									$database =& JFactory::getDBO();
									$mt = new MembersTags( $database );
									$tags = $mt->get_tag_cloud(0,0,$this->profile->get('uidNumber'));
									echo ($tags) ? $tags : JText::_('None');
								?>
							</td>
							<td class="privacy">
								<?php
									if($isUser) {
										echo MembersHtml::selectAccess('access[tags]',$this->params->get('access_tags'));
									}
								?>
							</td>
						</tr>
					<?php endif; ?>
				<?php endif; ?>
				
				<?php if ($this->registration->Citizenship != REG_HIDE) : ?>
					<?php if ($this->params->get('access_countryorigin') == 0 
					 		|| ($this->params->get('access_countryorigin') == 1 && $loggedin) 
					 		|| ($this->params->get('access_countryorigin') == 2 && $isUser)
						) : ?>
						<tr <?php if($this->params->get('access_countryorigin') == 2) { echo "class=\"private\""; } ?>>
							<td class="key"><?php echo JText::_('Citizenship'); ?></td>
							<td>
								<?php
									$img = '';
									if (is_file(JPATH_ROOT.DS.'components'.DS.$this->option.DS.'images'.DS.'flags'.DS.strtolower($this->profile->get('countryorigin')).'.gif')) {
										$img = '<img src="/components/'.$this->option.'/images/flags/'.strtolower($this->profile->get('countryorigin')).'.gif" alt="'.$this->profile->get('countryorigin').' '.JText::_('flag').'" /> ';
									}
									echo $img . strtoupper(htmlentities($this->profile->get('countryorigin'),ENT_COMPAT,'UTF-8'));
								?>
							</td>
							<td class="privacy">
								<?php
									if($isUser) {
										echo MembersHtml::selectAccess('access[countryorigin]',$this->params->get('access_countryorigin'));
									}
								?>
							</td>
						</tr>
					<?php endif; ?>
				<?php endif; ?>
				
				<?php if ($this->registration->Residency != REG_HIDE) : ?>
					<?php if ($this->params->get('access_countryresident') == 0 
					 		|| ($this->params->get('access_countryresident') == 1 && $loggedin) 
					 		|| ($this->params->get('access_countryresident') == 2 && $isUser)
						) : ?>
						<tr <?php if($this->params->get('access_countryresident') == 2) { echo "class=\"private\""; } ?>>
							<td class="key"><?php echo JText::_('Residence'); ?></td>
							<td>
								<?php
									$img = '';
									if (is_file(JPATH_ROOT.DS.'components'.DS.$this->option.DS.'images'.DS.'flags'.DS.strtolower($this->profile->get('countryresident')).'.gif')) {
										$img = '<img src="/components/'.$this->option.'/images/flags/'.strtolower($this->profile->get('countryresident')).'.gif" alt="'.$this->profile->get('countryresident').' '.JText::_('flag').'" /> ';
									}
									echo $img . strtoupper(htmlentities($this->profile->get('countryresident'),ENT_COMPAT,'UTF-8'));
								?>
							</td>
							<td class="privacy">
								<?php
									if($isUser) {
										echo MembersHtml::selectAccess('access[countryresident]',$this->params->get('access_countryresident'));
									}
								?>
							</td>
						</tr>
					<?php endif; ?>
				<?php endif; ?>
				
				<?php if ($this->registration->Sex != REG_HIDE) : ?>
					<?php if ($this->params->get('access_gender') == 0 
					 		|| ($this->params->get('access_gender') == 1 && $loggedin) 
					 		|| ($this->params->get('access_gender') == 2 && $isUser)
						) : ?>
						<tr <?php if($this->params->get('access_gender') == 2) { echo "class=\"private\""; } ?>>
							<td class="key"><?php echo JText::_('Sex'); ?></td>
							<td>
								<?php
									echo MembersHtml::propercase_singleresponse($this->profile->get('gender'));
								?>
							</td>
							<td class="privacy">
								<?php
									if($isUser) {
										echo MembersHtml::selectAccess('access[gender]',$this->params->get('access_gender'));
									}
								?>
							</td>
						</tr>
					<?php endif; ?>
				<?php endif; ?>
				
				<?php if ($this->registration->Disability != REG_HIDE) : ?>
					<?php if ($this->params->get('access_disability') == 0 
					 		|| ($this->params->get('access_disability') == 1 && $loggedin) 
					 		|| ($this->params->get('access_disability') == 2 && $isUser)
						) : ?>
						<tr <?php if($this->params->get('access_disability') == 2) { echo "class=\"private\""; } ?>>
							<td class="key"><?php echo JText::_('Disability'); ?></td>
							<td>
								<?php
									echo MembersHtml::propercase_multiresponse($this->profile->get('disability'));
								?>
							</td>
							<td class="privacy">
								<?php
									if($isUser) {
										echo MembersHtml::selectAccess('access[disability]',$this->params->get('access_disability'));
									}
								?>
							</td>
						</tr>
					<?php endif; ?>
				<?php endif; ?>
				
				<?php if ($this->registration->Hispanic != REG_HIDE) : ?>
					<?php if ($this->params->get('access_hispanic') == 0 
					 		|| ($this->params->get('access_hispanic') == 1 && $loggedin) 
					 		|| ($this->params->get('access_hispanic') == 2 && $isUser)
						) : ?>
						<tr <?php if($this->params->get('access_hispanic') == 2) { echo "class=\"private\""; } ?>>
							<td class="key"><?php echo JText::_('Hispanic Heritage'); ?></td>
							<td>
								<?php
									echo MembersHtml::propercase_multiresponse($this->profile->get('hispanic'));
								?>
							</td>
							<td class="privacy">
								<?php
									if($isUser) {
										echo MembersHtml::selectAccess('access[hispanic]',$this->params->get('access_hispanic'));
									}
								?>
							</td>
						</tr>
					<?php endif; ?>
				<?php endif; ?>
				
				<?php if ($this->registration->Race != REG_HIDE) : ?>
					<?php if ($this->params->get('access_race') == 0 
					 		|| ($this->params->get('access_race') == 1 && $loggedin) 
					 		|| ($this->params->get('access_race') == 2 && $isUser)
						) : ?>
						<tr <?php if($this->params->get('access_race') == 2) { echo "class=\"private\""; } ?>>
							<td class="key"><?php echo JText::_('Racial Background'); ?></td>
							<td>
								<?php
									echo MembersHtml::propercase_multiresponse($this->profile->get('race'));
								?>
							</td>
							<td class="privacy">
								<?php
									if($isUser) {
										echo MembersHtml::selectAccess('access[race]',$this->params->get('access_race'));
									}
								?>
							</td>
						</tr>
					<?php endif; ?>
				<?php endif; ?>
				
				<?php if ($this->registration->OptIn != REG_HIDE) : ?>
					<?php if ($this->params->get('access_optin') == 0 
					 		|| ($this->params->get('access_optin') == 1 && $loggedin) 
					 		|| ($this->params->get('access_optin') == 2 && $isUser)
						) : ?>
						<tr <?php if($this->params->get('access_optin') == 2) { echo "class=\"private\""; } ?>>
							<td class="key"><?php echo JText::_('E-mail Updates'); ?></td>
							<td>
								<?php
									echo ($this->profile->get('mailPreferenceOption')) ? JText::_('Yes') : JText::_('No');
								?>
							</td>
							<td class="privacy">
								<?php
									if($isUser) {
										echo MembersHtml::selectAccess('access[optin]',$this->params->get('access_optin'));
									}
								?>
							</td>
						</tr>
					<?php endif; ?>
				<?php endif; ?>
			</tbody>
		</table>	

<?php if($isUser) : ?>
		<input type="hidden" name="option" value="com_members" /> 
		<input type="hidden" name="id" value="<?php echo $this->profile->get('uidNumber'); ?>" /> 
		<input type="hidden" name="task" value="saveaccess" /> 
	</form>
<?php endif; ?>

<?php
	$thumb = "/site/stats/contributor_impact/impact_".$this->profile->get("uidNumber")."_th.gif";
	$full = "/site/stats/contributor_impact/impact_".$this->profile->get("uidNumber").".gif"
?>
<?php if(file_exists(JPATH_ROOT . $thumb)) : ?>
	<a id="member-stats-graph" title="<?php echo $this->profile->get("name")."'s Impact Graph"; ?>" href="<?php echo $full; ?>" rel="lightbox">
		<img src="<?php echo $thumb; ?>" alt="<?php echo $this->profile->get("name")."'s Impact Graph"; ?>" />
	</a>
<?php endif; ?>