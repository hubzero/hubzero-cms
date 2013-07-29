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

//$juser = JFactory::getUser();
$database = JFactory::getDBO();

$dateFormat = '%d %b, %Y';
$timeFormat = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M, Y';
	$timeFormat = 'h:i a';
	$tz = true;
}

$this->active = (isset($this->active) ? $this->active : '');

if (!isset($this->instructors) || !is_array($this->instructors))
{
	$this->instructors = array();
	$inst = $this->course->instructors();
	if (count($inst) > 0) 
	{
		foreach ($inst as $i)
		{
			$this->instructors[] = $i->get('user_id');
		}
	}
}

$prfx = 'thread';
if (isset($this->prfx)) 
{
	$prfx = $this->prfx;
}
//$prfx .= substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',5)),0,10);

if ($this->unit)
	{
		$this->base .= '&unit=' . $this->unit;
	}
	if ($this->lecture)
	{
		$this->base .= '&b=' . $this->lecture;
	}

if (!$this->thread->thread)
{
	$this->thread->thread = $this->thread->id;
}
?>
					<li class="thread<?php if ($this->active == $this->thread->thread) { echo ' active'; } ?><?php echo ($this->thread->sticky) ? ' stuck' : '' ?>" id="<?php echo $prfx . $this->thread->thread; ?>" data-thread="<?php echo $this->thread->thread; ?>">
						<?php 
							$name = JText::_('PLG_COURSES_DISCUSSIONS_ANONYMOUS');
							$huser = '';
							if (!$this->thread->anonymous) 
							{
								ximport('Hubzero_User_Profile');
								$huser = Hubzero_User_Profile::getInstance($this->thread->created_by);
								if (is_object($huser) && $huser->get('name')) 
								{
									$name = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $this->thread->created_by) . '">' . $this->escape(stripslashes($huser->get('name'))) . '</a>';
								}
							}

							if ($this->thread->reports)
							{
								$comment = '<p class="warning">' . JText::_('This comment has been reported as abusive and/or containing inappropriate content.') . '</p>';
							}
							else
							{
								if ($this->search)
								{
									$this->thread->title = preg_replace('#' . $this->search . '#i', "<span class=\"highlight\">\\0</span>", $this->thread->title);
								}
								$comment = $this->thread->title . ' &hellip;';
							}

							$this->thread->instructor_replied = 0;
							if (count($this->instructors))
							{
								$database->setQuery("SELECT COUNT(*) FROM #__forum_posts AS c WHERE c.thread= " . $this->thread->thread . " AND c.state=1 AND c.created_by IN (" . implode(',', $this->instructors) . ")");
								$this->thread->instructor_replied = $database->loadResult();
							}
						?>
						<div class="comment-content">
							<?php //if ($this->thread->sticky) { ?>
							<p class="sticky-thread" title="<?php echo ($this->thread->sticky) ? JText::_('This thread is sticky') : JText::_('This thread is not sticky'); ?>">
								<?php echo ($this->thread->sticky) ? JText::_('sticky') :  JText::_('not sticky'); ?>
							</p>
							<?php //} ?>
							<?php if ($this->thread->instructor_replied) { ?>
							<p class="instructor-commented" title="<?php echo JText::_('Instructor commented in this discussion'); ?>">
								<?php echo JText::_('instructor'); ?>
							</p>
							<?php } ?>
							<p class="comment-title">
								<span class="date"><time datetime="<?php echo $this->thread->created; ?>"><?php echo JHTML::_('date', $this->thread->created, $dateFormat, $tz); ?></time></span>
							</p>
							<p class="comment-body">
								<a href="<?php echo JRoute::_($this->base  . '&thread=' . $this->thread->id . ($this->search ? '&action=search&search=' . $this->search : '')); ?>"><?php echo $comment; ?></a>
							</p>
							<p class="comment-author">
								<strong><?php echo $name; ?></strong>
							</p>
						</div>
					</li>