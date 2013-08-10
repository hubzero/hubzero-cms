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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$dateformat = '%d %b %Y';
$timeformat = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateformat = 'd M Y';
	$timeformat = 'H:i p';
	$tz = true;
}

	$juser = JFactory::getUser();
	$database =& JFactory::getDBO();

	$jt = new JobType($database);
	$jc = new JobCategory($database);

	$profile = Hubzero_User_Profile::getInstance($this->seeker->uid);

	$jobtype = $jt->getType($this->seeker->sought_type, strtolower(JText::_('PLG_RESUME_TYPE_ANY')));
	$jobcat  = $jc->getCat($this->seeker->sought_cid, strtolower(JText::_('PLG_RESUME_CATEGORY_ANY')));

	$title = JText::_('ACTION_DOWNLOAD') . ' ' . $this->seeker->name . ' ' . ucfirst(JText::_('PLG_RESUME_RESUME'));

	// Get the configured upload path
		$base_path = DS . trim($this->params->get('webpath', '/site/members'), DS);

		ximport('Hubzero_View_Helper_Html');
		$path = $base_path . DS . Hubzero_View_Helper_Html::niceidformat($this->seeker->uid);

		if (!is_dir(JPATH_ROOT . $path)) 
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create(JPATH_ROOT . $path, 0777)) 
			{
				$path = '';
			}
		}

	$resume = is_file(JPATH_ROOT . $path . DS . $this->seeker->filename) ? $path . DS . $this->seeker->filename : '';
?>
	<div class="aboutme<?php echo $this->seeker->mine && $list ? ' mine' : ''; echo isset($this->seeker->shortlisted) && $this->seeker->shortlisted ? ' shortlisted' : ''; ?>">
		<div class="thumb">
			<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($profile); ?>" alt="<?php echo $this->seeker->name; ?>" />
		</div>

		<div class="aboutlb">
			<?php echo $list ? '<a href="' . JRoute::_('index.php?option=' . $this->option . '&id=' . $this->seeker->uid . '&active=resume') . '" class="profilelink">' : ''; ?>
			<?php echo $this->seeker->name; ?>
			<?php echo $list ? '</a>' : ''; ?>
			<?php if ($this->seeker->countryresident) { ?>
				, <span class="wherefrom"><?php echo $this->escape($this->seeker->countryresident); ?></span>
			<?php } ?>
			<?php if ($this->seeker->tagline) { ?>
				<blockquote>
					<p><?php echo stripslashes($this->seeker->tagline); ?></p>
				</blockquote>
			<?php } ?>
		</div>

		<div class="lookingforlb">
			<?php echo JText::_('PLG_RESUME_LOOKING_FOR'); ?>
			<span class="jobprefs">';
				<?php echo $jobtype ? $jobtype : ' '; ?>
				<?php echo $jobcat ? ' &bull; ' . $jobcat : ''; ?>
			</span>
			<span class="abouttext">
				<?php echo stripslashes($this->seeker->lookingfor); ?>
			</span>
		</div>

	<?php if ($this->seeker->mine) { ?>
		<span class="editbt">
			<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->seeker->uid . '&active=resume&action=editprefs'); ?>" title="<?php echo JText::_('PLG_RESUME_ACTION_EDIT_MY_PROFILE'); ?>">&nbsp;</a>
		</span>
	<?php } else if ($this->emp or $this->admin) { ?>
		<span id ="o<?php echo $this->seeker->uid; ?>">
			<a href="<?php echo JRoute::_('index.php?option=com_jobs&oid=' . $this->seeker->uid . '&task=shortlist'); ?>" class="favvit" title="<?php echo isset($this->seeker->shortlisted) && $this->seeker->shortlisted ? JText::_('PLG_RESUME_ACTION_REMOVE_FROM_SHORTLIST') : JText::_('PLG_RESUME_ACTION_ADD_TO_SHORTLIST'); ?>">
				<?php echo isset($this->seeker->shortlisted) && $this->seeker->shortlisted ? JText::_('PLG_RESUME_ACTION_REMOVE_FROM_SHORTLIST') : JText::_('PLG_RESUME_ACTION_ADD_TO_SHORTLIST'); ?>
			</a>
		</span>
	<?php } ?>
		<div class="clear leftclear"></div>
		<span class="indented">
	<?php if ($resume) { ?>
			<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->seeker->uid . '&active=resume&action=download'); ?>" class="resume getit" title="<?php echo $title; ?>">
				<?php echo ucfirst(JText::_('PLG_RESUME_RESUME')); ?>
			</a> 
			<span class="mini"><?php echo JText::_('PLG_RESUME_LAST_UPDATE'); ?>: <?php echo $this->nicetime($this->seeker->created); ?></span>
			<?php if ($this->seeker->url) { ?>
				<span class="mini"> | </span> 
				<span class="mini">
					<a href="<?php echo $this->seeker->url; ?>" class="web" rel="external" title="<?php echo JText::_('PLG_RESUME_MEMBER_WEBSITE') . ': ' . $this->seeker->url; ?>"><?php echo JText::_('PLG_RESUME_WEBSITE'); ?></a>
				</span>
			<?php } ?>
			<?php if ($this->seeker->linkedin) { ?>
				<span class="mini"> | </span> 
				<span class="mini">
					<a href="<?php echo $this->seeker->linkedin; ?>" class="linkedin" rel="external" title="<?php echo JText::_('PLG_RESUME_MEMBER_LINKEDIN'); ?>"><?php echo JText::_('PLG_RESUME_LINKEDIN'); ?></a>
				</span>
			<?php } ?>
	<?php } else { ?>
			<span class="unavail"><?php echo JText::_('PLG_RESUME_ACTION_DOWNLOAD'); ?></span>
	<?php } ?>
		</span>
	</div>
