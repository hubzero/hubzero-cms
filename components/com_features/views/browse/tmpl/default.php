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

$types = array(
	''=>JText::_('COM_FEATURES_ALL'),
	'tools'=>JText::_('COM_FEATURES_TOOLS'),
	'resources'=>JText::_('COM_FEATURES_RESOURCES'),
	'answers'=>JText::_('COM_FEATURES_ANSWERS'),
	'profiles'=>JText::_('COM_FEATURES_PROFILES'),
);
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div class="main section">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	<form action="<?php echo JRoute::_('index.php?option='.$this->option); ?>" id="featureform" method="post">
		<div class="aside">
			<fieldset>
				<label>
					<?php echo JText::_('COM_FEATURES_TYPE'); ?>
					<select name="type" id="type">
<?php 
					foreach ($types as $avalue => $alabel) 
					{
?>
						<option value="<?php echo $avalue; ?>"<?php echo ($avalue == $this->filters['type'] || $alabel == $this->filters['type']) ? ' selected="selected"' : ''; ?>><?php echo $alabel; ?></option>
<?php
					}
?>
					</select>
				</label>
				<input type="submit" name="go" value="<?php echo JText::_('COM_FEATURES_GO'); ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			</fieldset>
<?php if ($this->authorized) { ?>
			<p class="add"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=add'); ?>"><?php echo JText::_('COM_FEATURES_ADD'); ?></a></p>
<?php } ?>
		</div><!-- / .aside -->
		<div class="subject">
<?php
if (count($this->rows) > 0) {
		$txt_length = 300;
		$database =& JFactory::getDBO();
		switch ($this->filters['type']) 
		{
			case 'profiles':
				ximport('xprofile');
				include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_members'.DS.'members.class.php' );
				$mconfig =& JComponentHelper::getParams( 'com_members' );
			break;
			case 'questions':
				$aconfig =& JComponentHelper::getParams( 'com_answers' );
				include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'answers.class.php' );
			break;
			case 'tools':
				$rconfig =& JComponentHelper::getParams( 'com_resources' );
				include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.resource.php');
				include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.version.php' );
			break;
			case 'resources':
				$rconfig =& JComponentHelper::getParams( 'com_resources' );
				include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.resource.php');
			break;
			case 'all':
			default:
				ximport('xprofile');
				include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_members'.DS.'members.class.php' );
				include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'answers.class.php' );
				include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.version.php' );
				include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.resource.php');
				$mconfig =& JComponentHelper::getParams( 'com_members' );
				$aconfig =& JComponentHelper::getParams( 'com_answers' );
				$rconfig =& JComponentHelper::getParams( 'com_resources' );
			break;
		}
		
		$now = date( 'Y-m-d H:i:s' );
?>
			<ul class="features results">
<?php
		$html = '';
		foreach ($this->rows as $fh) 
		{
			if ($fh->note == 'tools') {
				$fh->tbl = 'tools';
			}
			$html .= "\t\t\t\t".'<li';
			if ($fh->featured > $now) {
				$html .= ' class="upcoming"';
			}
			$html .= '>'."\n";
			switch ($fh->tbl)
			{
				case 'tools':
					$row = new ResourcesResource( $database );
					$row->load( $fh->objectid );

					$path = $rconfig->get('uploadpath');
					if (substr($path, 0, 1) != DS) {
						$path = DS.$path;
					}
					if (substr($path, -1, 1) == DS) {
						$path = substr($path, 0, (strlen($path) - 1));
					}
					$path = FeaturesHtml::build_path( $row->created, $row->id, $path );

					$tv = new ToolVersion( $database );

					$versionid = $tv->getVersionIdFromResource( $row->id, 'current' );

					$picture = FeaturesHtml::getToolImage( $path, $versionid );

					$thumb = $path.DS.$picture;
					if (!is_file(JPATH_ROOT.$thumb)) {
						$thumb = FeaturesHtml::getContributorImage( $row->id, $database );

						if (!is_file(JPATH_ROOT.$thumb)) {
							$thumb = $rconfig->get('defaultpic');
							if (substr($thumb, 0, 1) != DS) {
								$thumb = DS.$thumb;
							}
						}
					}

					$href  = 'index.php?option=com_resources&id='.$row->id;
					
					if (is_file(JPATH_ROOT.$thumb)) {
						$html .= '<p class="featured-img"><img width="50" height="50" src="'.$thumb.'" alt="" /></p>'."\n";
					}
					$html .= '<p class="title"><a href="'.JRoute::_($href).'">'.stripslashes($row->title).'</a></p>'."\n";
					$html .= '<p class="details">'.JText::_('COM_FEATURES_FEATURED').' '.JHTML::_('date', $fh->featured, '%d %b. %Y').' '.JText::_('COM_FEATURES_IN').' '.JText::_(strtoupper($this->option).'_'.strtoupper($fh->tbl));
					if ($this->authorized) {
						$html .= ' <span>|</span> <a class="delete" href="'.JRoute::_('index.php?option='.$this->option.'&task=delete&id='.$fh->id).'">'.JText::_('COM_FEATURES_DELETE').'</a>'."\n";
						$html .= ' <span>|</span> <a class="edit" href="'.JRoute::_('index.php?option='.$this->option.'&task=edit&id='.$fh->id).'">'.JText::_('COM_FEATURES_EDIT').'</a>'."\n";
					}
					$html .= '</p>'."\n";
					$html .= Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::xhtml(strip_tags($row->introtext)), $txt_length, 1)."\n";
				break;
				
				case 'nontools':
				case 'resources':
					$row = new ResourcesResource( $database );
					$row->load( $fh->objectid );

					$path = $rconfig->get('uploadpath');
					if (substr($path, 0, 1) != DS) {
						$path = DS.$path;
					}
					if (substr($path, -1, 1) == DS) {
						$path = substr($path, 0, (strlen($path) - 1));
					}
					$path = FeaturesHtml::build_path( $row->created, $row->id, $path );

					$picture = FeaturesHtml::getImage( $path );

					$thumb = $path.DS.$picture;
					if (!is_file(JPATH_ROOT.$thumb)) {
						$thumb = FeaturesHtml::getContributorImage( $row->id, $database );

						if (!is_file(JPATH_ROOT.$thumb)) {
							$thumb = $rconfig->get('defaultpic');
							if (substr($thumb, 0, 1) != DS) {
								$thumb = DS.$thumb;
							}
						}
					}

					$href  = 'index.php?option=com_resources&id='.$row->id;
					
					if (is_file(JPATH_ROOT.$thumb)) {
						$html .= '<p class="featured-img"><img width="50" height="50" src="'.$thumb.'" alt="" /></p>'."\n";
					}
					$html .= '<p class="title"><a href="'.JRoute::_($href).'">'.stripslashes($row->title).'</a></p>'."\n";
					$html .= '<p class="details">'.JText::_('COM_FEATURES_FEATURED').' '.JHTML::_('date', $fh->featured, '%d %b. %Y').' '.JText::_('COM_FEATURES_IN').' '.JText::_(strtoupper($this->option).'_'.strtoupper($fh->tbl));
					if ($this->authorized) {
						$html .= ' <span>|</span> <a class="delete" href="'.JRoute::_('index.php?option='.$this->option.'&task=delete&id='.$fh->id).'">'.JText::_('COM_FEATURES_DELETE').'</a>'."\n";
						$html .= ' <span>|</span> <a class="edit" href="'.JRoute::_('index.php?option='.$this->option.'&task=edit&id='.$fh->id).'">'.JText::_('COM_FEATURES_EDIT').'</a>'."\n";
					}
					$html .= '</p>'."\n";
					$html .= Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::xhtml(strip_tags($row->introtext)), $txt_length, 1)."\n";
				break;
				
				case 'questions':
				case 'answers':
					$row = new AnswersQuestion( $database );
					$row->load( $fh->objectid );
				
					$ar = new AnswersResponse( $database );
					$row->rcount = count($ar->getIds( $row->id ));
				
					$thumb = '/modules/mod_featuredquestion/question_thumb.gif'; //trim($params->get( 'defaultpic' ));

					$name = JText::_('COM_FEATURES_ANONYMOUS');
					if ($row->anonymous == 0) {
						$juser =& JUser::getInstance( $row->created_by );
						if (is_object($juser)) {
							$name = $juser->get('name');
						}
					}

					$row->created = FeaturesHtml::mkt($row->created);
					$when = FeaturesHtml::timeAgo($row->created);
					
					if (is_file(JPATH_ROOT.$thumb)) {
						$html .= '<p class="featured-img"><img width="50" height="50" src="'.$thumb.'" alt="" /></p>'."\n";
					}
					$html .= '<p class="title"><a href="'.JRoute::_('index.php?option=com_answers&task=question&id='.$row->id).'">'.stripslashes($row->subject).'</a></p>'."\n";
					$html .= '<p class="details">'.JText::_('COM_FEATURES_FEATURED').' '.JHTML::_('date', $fh->featured, '%d %b. %Y').' '.JText::_('COM_FEATURES_IN').' '.JText::_(strtoupper($this->option).'_'.strtoupper($fh->tbl));
					if ($this->authorized) {
						$html .= ' <span>|</span> <a class="delete" href="'.JRoute::_('index.php?option='.$this->option.'&task=delete&id='.$fh->id).'">'.JText::_('COM_FEATURES_DELETE').'</a>'."\n";
						$html .= ' <span>|</span> <a class="edit" href="'.JRoute::_('index.php?option='.$this->option.'&task=edit&id='.$fh->id).'">'.JText::_('COM_FEATURES_EDIT').'</a>'."\n";
					}
					$html .= '</p>'."\n";
					$html .= '<p><span>'.JText::sprintf('COM_FEATURES_ASKED_BY', $name).'</span> - <span>'.$when.' ago</span> - <span>';
					$html .= ($row->rcount == 1) ? JText::sprintf('COM_FEATURES_RESPONSE', $row->rcount) : JText::sprintf('COM_FEATURES_RESPONSES', $row->rcount);
					$html .= '</span></p>'."\n";
					$html .= Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::xhtml(strip_tags($row->question)), $txt_length, 1)."\n";
				break;
				
				case 'xprofiles':
				case 'profiles':
					$row = new MembersProfile( $database );
					$row->load( $fh->objectid );

					// Member profile
					$title = $row->name;
					if (!trim($title)) {
						$title = $row->givenName.' '.$row->surname;
					}
					$id = $row->uidNumber;

					// Load their bio
					$profile = new XProfile();
					$profile->load( $row->uidNumber );
					$txt = $profile->get('bio');

					// Do we have a picture?
					$thumb = '';
					if (isset($row->picture) && $row->picture != '') {
						// Yes - so build the path to it
						$thumb  = $mconfig->get('webpath');
						if (substr($thumb, 0, 1) != DS) {
							$thumb = DS.$thumb;
						}
						if (substr($thumb, -1, 1) == DS) {
							$thumb = substr($thumb, 0, (strlen($thumb) - 1));
						}
						$thumb .= DS.FeaturesHtml::niceidformat($row->uidNumber).DS.$row->picture;

						// No - use default picture
						if (is_file(JPATH_ROOT.$thumb)) {
							// Build a thumbnail filename based off the picture name
							$thumb = FeaturesHtml::thumb( $thumb );
						}
					}

					// No - use default picture
					if (!is_file(JPATH_ROOT.$thumb)) {
						$thumb = $mconfig->get('defaultpic');
						if (substr($thumb, 0, 1) != DS) {
							$thumb = DS.$thumb;
						}
						// Build a thumbnail filename based off the picture name
						$thumb = FeaturesHtml::thumb( $thumb );
					}

					if (is_file(JPATH_ROOT.$thumb)) {
						$html .= '<p class="featured-img"><img width="50" height="50" src="'.$thumb.'" alt="" /></p>'."\n";
					}
					$html .= '<p class="title"><a href="'.JRoute::_('index.php?option=com_members&id='.$id).'">'.stripslashes($title).'</a></p>'."\n";
					$html .= '<p class="details">'.JText::_('COM_FEATURES_FEATURED').' '.JHTML::_('date', $fh->featured, '%d %b. %Y').' '.JText::_('COM_FEATURES_IN').' '.JText::_(strtoupper($this->option).'_'.strtoupper($fh->tbl));
					if ($this->authorized) {
						$html .= ' <span>|</span> <a class="delete" href="'.JRoute::_('index.php?option='.$this->option.'&task=delete&id='.$fh->id).'">'.JText::_('COM_FEATURES_DELETE').'</a>'."\n";
						$html .= ' <span>|</span> <a class="edit" href="'.JRoute::_('index.php?option='.$this->option.'&task=edit&id='.$fh->id).'">'.JText::_('COM_FEATURES_EDIT').'</a>'."\n";
					}
					$html .= '</p>'."\n";
					$html .= Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::xhtml(strip_tags($txt)), $txt_length, 1)."\n";
				break;
			}
			$html .= '</li>'."\n";
		}
		$html .= '</ul>'."\n";
		echo $html;
		
		$qs = '';
		foreach ($this->filters as $key=>$value) 
		{
			$qs .= ($key != 'limit' && $key != 'start') ? $key.'='.$value.'&' : '';
		}
		$paging = $this->pageNav->getListFooter();
		$paging = str_replace('features/?','features/?'.$qs,$paging);
		echo $paging;
} else { ?>
			<p class="warning"><?php echo JText::_('COM_FEATURES_NONE_FOUND'); ?></p>
<?php } ?>
		</div><!-- / .subject -->
		<div class="clear"></div>
	</form>
</div><!-- / .main section -->
