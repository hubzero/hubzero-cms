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

$params =& new JParameter( $this->page->params );

if ($this->sub) {
	$hid = 'sub-content-header';
	$uid = 'section-useroptions';
	$sid = 'sub-section-menu';
} else {
	$hid = 'content-header';
	$uid = 'useroptions';
	$sid = 'sub-menu';
}

if ($this->getError()) {
	echo WikiHtml::error( $this->getError() );
}
// Output other messages
if ($this->message) {
	echo WikiHtml::passed( $this->message );
}
$first = end($this->revisions);
?>
<div id="<?php echo $hid; ?>">
	<h2><?php echo $this->title; ?></h2>
	<?php echo WikiHtml::authors( $this->page, $params ); ?>
</div><!-- /#content-header -->

<?php echo WikiHtml::subMenu( $this->sub, $this->option, $this->page->pagename, $this->page->scope, $this->page->state, $this->task, $params, $this->editauthorized ); ?>

<div class="section">
	<div class="aside">
		<p>This article has <?php echo count($this->revisions); ?> versions, was created on <?php echo $first->created; ?> and last edited on <?php echo $this->revisions[0]->created; ?>.</p>
	</div><!-- / .aside -->
	<div class="subject">
		<p>Versions are listed in reverse-chronological order (newest to oldest). For any version listed below, click on its date to view it. For more help, see <a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename=Help:PageHistory'); ?>">Help:Page history</a>.</p> 
		<p>(cur) = difference from current version<br />(last) = difference from preceding version</p>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .section -->

<div class="main section">
<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&task=compare'); ?>" method="post">
	<div class="aside">
		<p><input type="submit" value="<?php echo JText::_('WIKI_HISTORY_COMPARE'); ?>" /></p>
	</div><!-- / .aside -->
	<div class="subject">
		<table id="revisionhistory" summary="<?php echo JText::_('WIKI_HISTORY_TBL_SUMMARY'); ?>">
			<thead>
				<tr>
					<th><?php echo JText::_('WIKI_HISTORY_COL_VERSION'); ?></th>
					<th colspan="2"><?php echo JText::_('WIKI_HISTORY_COL_COMPARE'); ?></th>
					<th><?php echo JText::_('WIKI_HISTORY_COL_WHEN'); ?></th>
					<th><?php echo JText::_('WIKI_HISTORY_COL_MADE_BY'); ?></th>
					<th><?php echo JText::_('WIKI_HISTORY_COL_STATUS'); ?></th>
<?php 
if (($this->page->state == 1 && $this->authorized === 'admin') 
 || ($this->page->state != 1 && $this->authorized)) { ?>
					<th>&nbsp;</th>
<?php } ?>
				</tr>
			</thead>
			<tbody>
<?php
$i = 0;
$cur = 0;
$cls = 'even';
foreach ($this->revisions as $revision) 
{
	$i++;
	$cls = ($cls == 'odd') ? 'even' : 'odd';
	$level = ($revision->minor_edit) ? 'minor' : 'major';
	
	switch ($revision->approved) 
	{
		case 1: $status = 'approved'; break;
		case 0:
		default: 
			$status = 'suggested';
			if ($this->authorized) {
				$status .= '<br /><a href="'.JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&task=approve&oldid='.$revision->id).'">'.JText::_('WIKI_ACTION_APPROVED').'</a>';
			}
			break;
	}
	
	$html  = "\t\t".'<tr class="'.$cls.'">'."\n";
	$html .= "\t\t\t".'<td>';
	if ($i == 1) {
		$lastedit = $revision->created;
		$html .= '(cur)';
		$cur = $revision->version;
	} else {
		$html .= '(<a href="'.JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&task=compare&oldid='.$revision->version.'&diff='.$cur).'">'.JText::_('WIKI_HISTORY_CURRENT').'</a>)';
	}
	$html .= '&nbsp;';
	if ($revision->version != 1) {
		$html .= '(<a href="'.JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&task=compare&oldid='.($revision->version - 1).'&diff='.$revision->version).'">'.JText::_('WIKI_HISTORY_LAST').'</a>)';
	} else {
		$html .= '(last)';
	}
	$html .= '</td>'."\n";
	if ($i == 1) {
		$html .= "\t\t\t".'<td>&nbsp;</td>'."\n";
		$html .= "\t\t\t".'<td><input type="radio" name="diff" value="'.$revision->version.'" checked="checked" /></td>'."\n";
	} else {
		$html .= "\t\t\t".'<td><input type="radio" name="oldid" value="'.$revision->version.'"';
		if ($i == 2) {
			$html .= ' checked="checked"';
		}
		$html .= ' /></td>'."\n";
		$html .= "\t\t\t".'<td>&nbsp;</td>'."\n";		
	}
	
	$xname = JText::_('WIKI_AUTHOR_UNKNOWN');
	$juser =& JUser::getInstance( $revision->created_by );
	if (is_object($juser)) {
		$xname = $juser->get('name');
	}
	
	if ($revision->summary) {
		$summary = WikiHtml::encode_html($revision->summary);
	} else {
		$summary = JText::_('WIKI_REVISION_NO_SUMMARY');
	}
	
	$html .= "\t\t\t".'<td><a href="'.JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&version='.$revision->version).'" class="tooltips" title="'.JText::_('WIKI_REVISION_SUMMARY').' :: '.$summary.'">'.$revision->created.'</a></td>'."\n";
	$html .= "\t\t\t".'<td>'.$xname.'</td>'."\n";
	$html .= "\t\t\t".'<td>'.$status.'</td>'."\n";
	if (($this->page->state == 1 && $this->authorized === 'admin') 
	 || ($this->page->state != 1 && $this->authorized)) {
		$html .= "\t\t\t".'<td><a href="'.JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&task=deleterevision&oldid='.$revision->id).'" title="'.JText::_('WIKI_REVISION_DELETE').'">'."\n";
		if($this->option == 'com_groups') {
			$group_config = JComponentHelper::getParams( $this->option );
			$html .= "\t\t\t\t".'<img src="'.$group_config->get('iconpath').'/trash.gif" alt="'.JText::_('DELETE').'" />'."\n";
		} else { 
			$html .= "\t\t\t\t".'<img src="/components/'.$this->option.'/images/icons/trash.gif" alt="'.JText::_('DELETE').'" />'."\n";
		}
		$html .= "\t\t\t".'</a></td>'."\n";
	}
	$html .= "\t\t".'</tr>'."\n";
	echo $html;
}
?>
			</tbody>
		</table>
	</div><!-- / .subject -->
	<div class="clear"></div>
	<input type="hidden" name="pagename" value="<?php echo $this->page->pagename; ?>" />
	<input type="hidden" name="scope" value="<?php echo $this->page->scope; ?>" />
	<input type="hidden" name="pageid" value="<?php echo $this->page->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<?php if ($this->sub) { ?>
	<input type="hidden" name="active" value="<?php echo $this->sub; ?>" />
<?php } ?>
	<input type="hidden" name="task" value="compare" />
</form>
</div><!-- / .main section -->
