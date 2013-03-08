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

ximport('Hubzero_User_Profile_Helper');

$dateFormat  = '%d %b, %Y';
$timeFormat  = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat  = 'd M, Y';
	$timeFormat  = 'h:i a';
	$tz = true;
}

$wikiconfig = array(
	'option'   => $this->option,
	'scope'    => 'reply',
	'pagename' => $this->reply->id,
	'pageid'   => $this->reply->id,
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
$name = JText::_('PLG_RESOURCES_REVIEWS_ANONYMOUS');

$juseri = Hubzero_User_Profile::getInstance($this->reply->added_by);

if ($this->reply->anonymous != 1) 
{
	//$name = JText::_('PLG_RESOURCES_REVIEWS_UNKNOWN');
	if (is_object($juseri) && $juseri->get('name')) 
	{
		$name = '<a href="'.JRoute::_('index.php?option=com_members&id=' . $juseri->get('uidNumber')) . '">' . $this->escape(stripslashes($juseri->get('name'))) . '</a>';
	}
}
?>
<p class="comment-member-photo">
	<a name="c<?php echo $this->reply->id; ?>"></a>
	<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($juseri, $this->reply->anonymous); ?>" alt="" />
</p>
<div class="comment-content">
	<p class="comment-title">
		<strong><?php echo $name; ?></strong> 
		<a class="permalink" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->id.'&active=reviews#c'.$this->reply->id); ?>" title="<?php echo JText::_('PLG_RESOURCES_REVIEWS_PERMALINK'); ?>">
			<span class="comment-date-at">@</span> <span class="time"><time datetime="<?php echo $this->reply->added; ?>"><?php echo JHTML::_('date', $this->reply->added, $timeFormat, $tz); ?></time></span> 
			<span class="comment-date-on">on</span> <span class="date"><time datetime="<?php echo $this->reply->added; ?>"><?php echo JHTML::_('date', $this->reply->added, $dateFormat, $tz); ?></time></span>
		</a>
	</p>
<?php if ($this->abuse && $this->reply->abuse_reports > 0) { ?>
	<p class="warning"><?php echo JText::_('PLG_RESOURCES_REVIEWS_NOTICE_POSTING_REPORTED'); ?></p>
<?php } else { ?>
	<?php if ($this->reply->comment) { ?>
		<?php echo $this->parser->parse(stripslashes($this->reply->comment), $wikiconfig); ?>
	<?php } else { ?>
		<p><?php echo JText::_('PLG_RESOURCES_REVIEWS_NO_COMMENT'); ?></p>
	<?php } ?>
	
	<p class="comment-options">
<?php if ($this->abuse) { 
		if ($this->juser->get('guest')) {
			$href = JRoute::_('index.php?option=com_login&return=' . base64_encode(JRoute::_('index.php?option=com_support&task=reportabuse&category=comment&id='.$this->reply->id.'&parent='.$this->id)));
		} else {
			$href = JRoute::_('index.php?option=com_support&task=reportabuse&category=comment&id='.$this->reply->id.'&parent='.$this->id);
		}
?>
		<a class="abuse" href="<?php echo $href; ?>"><?php echo JText::_('PLG_RESOURCES_REVIEWS_REPORT_ABUSE'); ?></a>
<?php } ?>
<?php
	// Cannot reply at third level
	if ($this->level < 3) {
		if ($this->juser->get('guest')) {
			$href = JRoute::_('index.php?option=com_login&return=' . base64_encode(JRoute::_('index.php?option='.$this->option.'&id='.$this->id.'&active=reviews&action=reply&category=reviewcomment&refid='.$this->reply->id)));
		} else {
			$href = JRoute::_('index.php?option='.$this->option.'&id='.$this->id.'&active=reviews&action=reply&category=reviewcomment&refid='.$this->reply->id);
		}
		echo '<a ';
		//if (!$this->juser->get('guest')) {
		//	echo 'class="showreplyform" href="javascript:void(0);"';
		//} else {
			echo 'href="'.$href.'" ';
		//}
		echo 'class="reply" id="rep_'.$this->reply->id.'">'.JText::_('PLG_RESOURCES_REVIEWS_REPLY').'</a>';
	}
?>
	</p>
<?php 
	// Add the reply form if needed
	if ($this->level < 3 && !$this->juser->get('guest')) {
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'resources',
				'element'=>'reviews',
				'name'=>'browse',
				'layout'=>'addcomment'
			)
		);
		$view->option = $this->option;
		$view->row = $this->reply;
		$view->juser = $this->juser;
		$view->level = $this->level;
		$view->resource = $this->resource;
		$view->addcomment = $this->addcomment;
		$view->display();
	}
}
?>
</div>
