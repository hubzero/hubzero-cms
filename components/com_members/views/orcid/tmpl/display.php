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
 * @author    Ahmed Abdel-Gawad <aabdelga@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$fname = JRequest::getVar('fname', '');
$lname = JRequest::getVar('lname', '');
$email = JRequest::getVar('email', '');

$returnOrcid = JRequest::getInt('return', 0);

$juser = JFactory::getUser();

$isRegister = $returnOrcid == 1;

if ($isRegister)
{
	$callbackPrefix = "HUB.Register.";
}
else
{
	$callbackPrefix = "HUB.Members.Profile.";
}

if (!$isRegister)
{
	// Instantiate a new profile object
	$profile = \Hubzero\User\Profile::getInstance($juser->get('id'));
	if ($profile)
	{
		$fname = $fname ?: $profile->get('givenName');
		$lname = $lname ?: $profile->get('surname');
		$email = $email ?: $profile->get('email');
	}
}


$returnOrcid = JRequest::getInt('return', 0);
$isRegister = $returnOrcid == 1;

if (JRequest::getInt('return', 0) == 1)
{
	$this->js('register.js');
}
else
{
	\Hubzero\Document\Assets::addPluginScript('members', 'profile');
}

$this->css('orcid.css');

$srv = $this->config->get('orcid_service', 'members');
$tkn = $this->config->get('orcid_' . $srv . '_token');
?>
<section class="main section">
	<form name="orcid-search-form">
		<?php if ($tkn) { ?>
			<h3><?php echo JText::_('Associate your <b>ORCID</b> (Open Researcher and Contributor ID)'); ?></h3>
			<fieldset>
				<legend><?php echo JText::_('Profile Info'); ?></legend>

				<div class="grid nobreak">
					<div class="col span4">
						<label for="first-name">
							<?php echo JText::_('First name:'); ?>
							<input type="text" id="first-name" name="first-name" value="<?php echo $this->escape($fname); ?>" />
						</label>
					</div>
					<div class="col span4">
						<label for="last-name">
							<?php echo JText::_('Last name:'); ?>
							<input type="text" id="last-name" name="last-name" value="<?php echo $this->escape($lname); ?>" />
						</label>
					</div>
					<div class="col span4 omega">
						<label for="email">
							<?php echo JText::_('Email:'); ?>
							<input type="text" id="email" name="email" value="<?php echo $this->escape($email); ?>" />
						</label>
					</div>
				</div>

				<input type="hidden" name="base_uri" id="base_uri" value="<?php echo rtrim(JURI::base(true), '/'); ?>" />
			</fieldset>

			<div class="orcid-section orcid-search">
				<h4><?php echo JText::_('Search for an existing ORCID'); ?></h4>
				<div class="grid nobreak">
					<div class="col span8">
						<p><?php echo JText::_('If you have created an ORCID or your institution has generated one for you, fill in the fields above and search for your ID from the list.'); ?></p>
						<p><?php echo JText::_('Note that most ORCID records have the email address marked as private and private information will not be returned in the search results.'); ?></p>
					</div>
					<div class="col span4 omega">
						<p>
							<a id="get-orcid-results" class="btn" href="javascript:;"><?php echo JText::_('Search ORCID'); ?></a>
						</p>
					</div>
				</div>

				<div id="section-orcid-results">
					<?php
					if (isset($this->orcid_records_html))
					{
						echo $this->orcid_records_html;
					}
					?>
				</div>
			</div>

			<?php if ($this->config->get('orcid_service', 'members') != 'public') { ?>
				<div class="orcid-section orcid-create">
					<h4><?php echo JText::_('Create a new ORCID'); ?></h4>
					<div class="grid nobreak">
						<div class="col span8">
							<p><?php echo JText::_('If you can\'t find your ID or would like to create one, click the "Create new ORCID" button to generate a new ID based on the info above. You will receive an email from ORCID to claim the new ID.'); ?></p>
						</div>
						<div class="col span4 omega">
							<p><a id="create-orcid" class="btn" onclick="<?php echo $callbackPrefix . "createOrcid(document.getElementById('first-name').value, document.getElementById('last-name').value, document.getElementById('email').value)"; ?>"><?php echo JText::_('Create new ORCID'); ?></a></p>
						</div>
					</div>
				</div>
			<?php } ?>
		<?php } else { ?>
			<p class="warning"><?php echo JText::sprintf('This service is currently unavailable and/or not configured correctly. Please contact <a href="%s">support</a> for further assistance.', JRoute::_('index.php?option=com_support')); ?></p>
		<?php } ?>
	</form>
</section>