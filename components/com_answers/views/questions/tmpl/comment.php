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

$dateFormat = '%d %b %Y';
$timeFormat = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M Y';
	$timeFormat = 'H:i p';
	$tz = true;
}

$wikiconfig = array(
	'option'   => $this->option,
	'scope'    => 'answer',
	'pagename' => $this->question->id,
	'pageid'   => $this->question->id,
	'filepath' => '',
	'domain'   => ''
);
if (!$this->parser) 
{
	ximport('Hubzero_Wiki_Parser');
	$parser =& Hubzero_Wiki_Parser::getInstance();
	$this->parser = $parser;
}

// Set the name of the reviewer
$name = JText::_('COM_ANSWERS_ANONYMOUS');
$ruser = new Hubzero_User_Profile();
$ruser->load($this->reply->added_by);
if ($this->reply->anonymous != 1) 
{
	$name = JText::_('COM_ANSWERS_UNKNOWN');
	//$ruser =& JUser::getInstance($this->reply->added_by);
	if (is_object($ruser)) 
	{
		$name = $this->escape(stripslashes($ruser->get('name')));
	}
}
?>
<p class="comment-member-photo">
	<span class="comment-anchor"><a name="c<?php echo $this->reply->id; ?>"></a></span>
	<img src="<?php echo AnswersHelperMember::getMemberPhoto($ruser, $this->reply->anonymous); ?>" alt="" />
</p>
<div class="comment-content">
	<p class="comment-title">
		<strong><?php echo $name; ?></strong> 
		<a class="permalink" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=question&id=' . $this->question->id . '#c' . $this->reply->id); ?>" title="<?php echo JText::_('COM_ANSWERS_PERMALINK'); ?>">
			<span class="comment-date-at">@</span> <span class="time"><time datetime="<?php echo $this->reply->added; ?>"><?php echo JHTML::_('date', $this->reply->added, $timeFormat, $tz); ?></time></span> 
			<span class="comment-date-on">on</span> <span class="date"><time datetime="<?php echo $this->reply->added; ?>"><?php echo JHTML::_('date', $this->reply->added, $dateFormat, $tz); ?></time></span>
		</a>
	</p>
<?php if ($this->abuse && $this->reply->reports > 0) { ?>
	<p class="warning"><?php echo JText::_('COM_ANSWERS_NOTICE_POSTING_REPORTED'); ?></p>
<?php } else { ?>
	<?php if ($this->reply->comment) { ?>
		<p><?php echo $this->parser->parse(stripslashes($this->reply->comment), $wikiconfig); ?></p>
	<?php } else { ?>
		<p><?php echo JText::_('COM_ANSWERS_NO_COMMENT'); ?></p>
	<?php } ?>

	<p class="comment-options">
<?php if ($this->abuse) { ?>
		<a class="abuse" href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=comment&id=' . $this->reply->id . '&parent=' . $this->id); ?>">
			<?php echo JText::_('COM_ANSWERS_REPORT_ABUSE'); ?>
		</a>
<?php } ?>
<?php if ($this->level < 3) { ?>
		<a class="reply" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=reply&category=answercomment&id=' . $this->id . '&refid=' . $this->reply->id . '#c' . $this->reply->id); ?>" rel="commentform_<?php echo $this->reply->id; ?>">
			<?php echo JText::_('COM_ANSWERS_REPLY'); ?>
		</a>
<?php } ?>
	</p>
<?php 
	// Add the reply form if needed
	if ($this->level < 3 && !$this->juser->get('guest')) 
	{
		$view = new JView(array(
			'name'   => 'questions', 
			'layout' => 'addcomment'
		));
		$view->option     = $this->option;
		$view->row        = $this->reply;
		$view->juser      = $this->juser;
		$view->level      = $this->level;
		$view->question   = $this->question;
		$view->addcomment = $this->addcomment;
		$view->display();
	}
}
?>
</div>