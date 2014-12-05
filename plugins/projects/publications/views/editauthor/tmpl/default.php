<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

$name = $this->author->name ? $this->author->name : $this->author->p_name;
$name = trim($name) ? $name : $this->author->invited_name;

if (trim($name)) {
	$nameParts    = explode(" ", $name);
	$lastname  	  = end($nameParts);
	$firstname    = count($nameParts) > 1 ? $nameParts[0] : '';
}
else {
	$firstname = htmlspecialchars($this->author->givenName);
	$lastname  = htmlspecialchars($this->author->surname);
	if (!$this->author->user_id)
	{
		$name = $this->author->invited_email;
	}
}
$firstname = $this->author->firstName ? htmlspecialchars($this->author->firstName) : $firstname;
$lastname = $this->author->lastName ? htmlspecialchars($this->author->lastName) : $lastname;

// Don't display email in name fields
$regex = '/^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-]+)+/';
if (preg_match($regex, $lastname))
{
	$lastname = '';
}
if (preg_match($regex, $firstname))
{
	$firstname = '';
}

$juser = JFactory::getUser();
$actor = \Hubzero\User\Profile::getInstance($juser->get('id'));

// Get profile thumb image
$profile = \Hubzero\User\Profile::getInstance($this->author->user_id);
$thumb   = $profile ? $profile->getPicture() : $actor->getPicture(true);

?>

<div id="abox-content">
<h3><?php echo $this->author->id
	? JText::_('PLG_PROJECTS_PUBLICATIONS_AUTHORS_EDIT_AUTHOR')
	: JText::_('PLG_PROJECTS_PUBLICATIONS_AUTHORS_ADD_AUTHOR'); ?></h3>
<?php
// Display error  message
if ($this->getError()) {
	echo ('<p class="error">'.$this->getError().'</p>');
} else { ?>

	<form id="hubForm-ajax" method="post" action="<?php echo $this->url; ?>">
			<fieldset >
				<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
				<input type="hidden" name="action" value="saveauthor" />
				<input type="hidden" name="active" value="publications" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="pid" value="<?php echo $this->pid; ?>" />
				<input type="hidden" name="vid" value="<?php echo $this->vid; ?>" />
				<input type="hidden" name="uid" value="<?php echo $this->uid; ?>" />
				<input type="hidden" name="owner" value="<?php echo $this->owner; ?>" />
				<input type="hidden" name="move" value="<?php echo $this->move; ?>" />
				<input type="hidden" name="selections" id="ajax-selections" value="" />
				<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $this->project->provisioned == 1 ? 1 : 0; ?>" />
				<?php if ($this->project->provisioned == 1 ) { ?>
				<input type="hidden" name="task" value="submit" />
				<?php } ?>
			</fieldset>
			<div class="author-edit">
				<?php if ($this->author->id) { ?>
				<div class="profile-info">
					<p><img width="30" height="30" src="<?php echo $thumb; ?>" alt="<?php echo $name; ?>" />
						<span>
						<span class="block faded"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_AUTHORS_TEAM_MEMBER')); ?>:</span>
						<?php echo $this->author->username ? $this->author->p_name.' ('.$this->author->username.')' : $name.' (unconfirmed)';  ?></span>
					</p>
				</div>
				<?php } ?>

				<label class="display_inline">
					<span class="leftshift faded"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_AUTHORS_AUTHOR_FIRST_NAME')); ?>*:</span>
					<input type="text" name="firstName" value="<?php echo $firstname;  ?>" maxlength="255" />
				</label>
				<label class="display_inline">
					<span class="faded"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_AUTHORS_AUTHOR_LAST_NAME')); ?>*:</span>
					<input type="text" name="lastName" value="<?php echo $lastname;  ?>" maxlength="255" />
				</label>
				<div class="clear"></div>
				<label for="organization">
					<span class="leftshift faded"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_AUTHORS_AUTHOR_ORGANIZATION')); ?>*:</span>
					<input type="text" name="organization" class="long" value="<?php echo $this->author->organization ? htmlspecialchars($this->author->organization) : htmlspecialchars($this->author->p_organization); ?>" maxlength="255" /></label>
				<div class="clear"></div>
				<?php if (!$this->author->username) { ?>
					<label for="email">
						<span class="leftshift faded"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_AUTHORS_AUTHOR_EMAIL')); ?>:</span>
						<input type="text" name="email" class="long" value="<?php echo $this->author->invited_email ? $this->author->invited_email : ''; ?>" maxlength="255" /><span class="optional"><?php echo JText::_('OPTIONAL'); ?></span>
					</label>
					<div class="clear"></div>
				<?php } ?>
				<label for="credit">
					<span class="leftshift faded"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_AUTHORS_AUTHOR_CREDIT')); ?>:</span>
					<input type="text" name="credit" class="long" value="<?php echo htmlspecialchars($this->author->credit); ?>" maxlength="255"  /><span class="optional"><?php echo JText::_('OPTIONAL'); ?></span>
				</label>
				<div class="clear"></div>
				<p class="hint"><?php echo ($this->author->username)
					? JText::_('PLG_PROJECTS_PUBLICATIONS_AUTHORS_EDIT_TIPS')
					: JText::_('PLG_PROJECTS_PUBLICATIONS_AUTHORS_EDIT_UNCONFIRMED_TIPS') ; ?></p>

				<p class="submitarea">
					<input type="submit" class="btn" value="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE'); ?>" />
					<?php if ($this->ajax) { ?>
					<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo JText::_('COM_PROJECTS_CANCEL'); ?>" />
					<?php } else {
						$rtn = JRequest::getVar('HTTP_REFERER', $this->url, 'server');
					?>
					<a href="<?php echo $rtn; ?>" class="btn btn-cancel"><?php echo JText::_('COM_PROJECTS_CANCEL'); ?></a>
					<?php } ?>
				</p>
			</div>
	</form>
	<div class="clear"></div>
<?php } ?>
</div>