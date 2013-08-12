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

// Set the name of the reviewer
$name = JText::_('ANONYMOUS');
$ruser = Hubzero_User_Profile::getInstance($this->reply->added_by);
if ($this->reply->anonymous != 1) 
{
	$name = $this->reply->authorname;
}

$dateformat = '%d %b %Y';
$timeformat = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateformat = 'd M Y';
	$timeformat = 'h:i A';
	$tz = null;
}

ximport('Hubzero_Wiki_Parser');

$wikiconfig = array(
	'option'   => $this->option,
	'scope'    => 'wishlist',
	'pagename' => 'wishlist',
	'pageid'   => $this->reply->id,
	'filepath' => '',
	'domain'   => $this->reply->id
);

$p =& Hubzero_Wiki_Parser::getInstance();

if ($this->reply->comment)
{
	/*$this->reply->comment = stripslashes($this->reply->comment);
	$this->reply->comment = preg_replace('/<br(\s+)\/?>/', '', $this->reply->comment);
	$this->reply->comment = preg_replace('/\[(attachment #[0-9]{1,} not found)\]/', "$1", $this->reply->comment);*/

	$this->reply->comment = $p->parse($this->reply->comment, $wikiconfig, false);
}

//$commenttype = $this->reply->added_by == $this->wishauthor && $this->reply->anonymous != 1 ?  'submittercomment' : 'plaincomment';
//$commenttype = $this->reply->admin && $this->reply->anonymous != 1 ? 'admincomment' : $commenttype;

?>
<p class="comment-member-photo">
	<span class="comment-anchor"><a name="#c<?php echo $this->reply->id; ?>"></a></span>
	<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($ruser, $this->reply->anonymous); ?>" alt="" />
</p>
<div class="comment-content">
	<p class="comment-title">
		<strong><?php echo $name; ?></strong> 
		<a class="permalink" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=wish&id=' . $this->listid . '#c' . $this->reply->id); ?>" title="<?php echo JText::_('COM_WISHLIST_PERMALINK'); ?>">
			<span class="comment-date-at">@</span> <span class="time"><time datetime="<?php echo $this->reply->added; ?>"><?php echo JHTML::_('date', $this->reply->added, $timeformat, $tz); ?></time></span> 
			<span class="comment-date-on"><?php echo JText::_('COM_WISHLIST_ON'); ?></span> <span class="date"><time datetime="<?php echo $this->reply->added; ?>"><?php echo JHTML::_('date', $this->reply->added, $dateformat, $tz); ?></time></span>
		</a>
	</p>
<?php if ($this->abuse && $this->reply->reports > 0) { ?>
	<p class="warning"><?php echo JText::_('COM_WISHLIST_NOTICE_POSTING_REPORTED'); ?></p>
<?php } else { ?>
	<?php if ($this->reply->comment) { ?>
		<?php echo $this->reply->comment; ?>
	<?php } else { ?>
		<p><?php echo JText::_('COM_WISHLIST_NO_COMMENT'); ?></p>
	<?php } ?>
	<?php if ($this->reply->attachment) { ?>
		<p class="attachment"><?php echo $this->reply->attachment; ?></p>
	<?php } ?>

	<p class="comment-options">
<?php if ($this->level < 3) { // Cannot reply at third level ?>
		<a class="icon-reply reply" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=reply&cat=wishcomment&id=' . $this->listid . '&refid=' . $this->reply->id . '&wishid=' . $this->wishid); ?>" id="rep_<?php echo $this->reply->id; ?>"><?php echo JText::_('COM_WISHLIST_REPLY'); ?></a>
<?php } ?>
<?php if ($this->abuse) { ?>
		<a class="icon-abuse abuse" href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=comment&id=' . $this->reply->id . '&parent=' . $this->wishid); ?>"><?php echo JText::_('COM_WISHLIST_REPORT_ABUSE'); ?></a>
<?php } ?>
<?php if ($this->juser->get('id') == $this->reply->added_by) { ?>
		<a class="icon-delete delete" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=deletereply&replyid=' . $this->reply->id); ?>"><?php echo JText::_('COM_WISHLIST_DELETE_COMMENT'); ?></a>
<?php } ?>
	</p>
	
<?php 
	// Add the reply form if needed
	if ($this->level < 3 && !$this->juser->get('guest')) 
	{
		$view = new JView(array(
			'name'   => 'wish', 
			'layout' => 'addcomment'
		));
		$view->option     = $this->option;
		$view->listid     = $this->listid;
		$view->level      = $this->level;
		$view->row        = $this->reply;
		$view->juser      = $this->juser;
		$view->wishid     = $this->wishid;
		$view->refid      = $this->reply->id;
		$view->addcomment = $this->addcomment;
		$view->display();
	}
}
?>
</div><!-- / .comment-content -->
