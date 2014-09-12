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

$dateFormat = '%d %b, %Y';
$timeFormat = '%I:%M %p';
$tz = 0;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M Y';
	$timeFormat = 'h:i A';
	$tz = null;
}


$wikiconfig = array(
	'option'   => $this->option,
	'scope'    => 'reply',
	'pagename' => $this->reply->id,
	'pageid'   => $this->reply->id,
	'filepath' => '',
	'domain'   => ''
);
if (!$this->parser) {
	ximport('Hubzero_Wiki_Parser');
	$parser =& Hubzero_Wiki_Parser::getInstance();
	$this->parser = $parser;
}

// Set the name of the reviewer
$name = JText::_('PLG_PUBLICATION_REVIEWS_ANONYMOUS');
$juseri = new Hubzero_User_Profile();
if ($this->reply->anonymous != 1) {
	$name = JText::_('PLG_PUBLICATION_REVIEWS_UNKNOWN');
	/*$ruser =& JUser::getInstance($this->reply->added_by);
	if (is_object($ruser)) {
		$name = $ruser->get('name');
	}*/
	$juseri->load( $this->reply->added_by );
	if (is_object($juseri) && $juseri->get('name')) {
		$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$juseri->get('uidNumber')).'">'.stripslashes($juseri->get('name')).'</a>';
	}
}
?>
<a name="c<?php echo $this->reply->id; ?>"></a>
<p class="comment-member-photo">
	<img src="<?php echo plgPublicationsReviews::getMemberPhoto($juseri, $this->reply->anonymous); ?>" alt="" />
</p>
<div class="comment-content">
	<p class="comment-title">
		<strong><?php echo $name; ?></strong> 
		<a class="permalink" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->id.'&active=reviews#c'.$this->reply->id); ?>" title="<?php echo JText::_('PLG_PUBLICATION_REVIEWS_PERMALINK'); ?>">@ <span class="time"><?php echo JHTML::_('date',$this->reply->added, $timeFormat, $tz); ?></span> on <span class="date"><?php echo JHTML::_('date',$this->reply->added, $dateFormat, $tz); ?></span></a>
	</p>
<?php if ($this->abuse && $this->reply->abuse_reports > 0) { ?>
	<p class="warning"><?php echo JText::_('PLG_PUBLICATION_REVIEWS_NOTICE_POSTING_REPORTED'); ?></p>
<?php } else { ?>
	<?php if ($this->reply->comment) { ?>
		<?php echo $this->parser->parse(stripslashes($this->reply->comment), $wikiconfig); ?>
	<?php } else { ?>
		<p><?php echo JText::_('PLG_PUBLICATION_REVIEWS_NO_COMMENT'); ?></p>
	<?php } ?>
	
	<p class="comment-options">
<?php if ($this->abuse) { ?>
		<a class="abuse" href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=comment&id='.$this->reply->id.'&parent='.$this->id); ?>"><?php echo JText::_('PLG_PUBLICATION_REVIEWS_REPORT_ABUSE'); ?></a>
<?php } ?>
<?php
	// Cannot reply at third level
	if ($this->level < 3) {
		echo '<a ';
		//if (!$this->juser->get('guest')) {
		//	echo 'class="showreplyform" href="javascript:void(0);"';
		//} else {
			echo 'href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->id.'&active=reviews&action=reply&category=reviewcomment&refid='.$this->reply->id).'" ';
		//}
		echo 'class="reply" id="rep_'.$this->reply->id.'">'.JText::_('PLG_PUBLICATION_REVIEWS_REPLY').'</a>';
	}
?>
	</p>
<?php 
	// Add the reply form if needed
	if ($this->level < 3 && !$this->juser->get('guest')) {
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'publications',
				'element'=>'reviews',
				'name'=>'browse',
				'layout'=>'addcomment'
			)
		);
		$view->option = $this->option;
		$view->row = $this->reply;
		$view->juser = $this->juser;
		$view->level = $this->level;
		$view->publication = $this->publication;
		$view->addcomment = $this->addcomment;
		$view->display();
	}
}
?>
</div>
