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
$dateFormat2 = '%d %b. %Y';
$timeFormat = '%I:%M %p';
$tz = 0;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M Y';
	$dateFormat2 = 'd M. Y';
	$timeFormat = 'h:i A';
	$tz = null;
}

$types = array(
	''          => JText::_('COM_FEATURES_ALL'),
	'tools'     => JText::_('COM_FEATURES_TOOLS'),
	'resources' => JText::_('COM_FEATURES_RESOURCES'),
	'answers'   => JText::_('COM_FEATURES_ANSWERS'),
	'profiles'  => JText::_('COM_FEATURES_PROFILES'),
);
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div class="main section">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" id="featureform" method="post">
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
				<p class="submit"><input type="submit" name="go" value="<?php echo JText::_('COM_FEATURES_GO'); ?>" /></p>
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			</fieldset>
<?php if ($this->config->get('access-manage-component')) { ?>
			<p><a class="add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=add'); ?>"><?php echo JText::_('COM_FEATURES_ADD'); ?></a></p>
<?php } ?>
		</div><!-- / .aside -->
		<div class="subject">
			<div class="container">
				<div class="container-block">
<?php
if (count($this->rows) > 0) 
{
		$txt_length = 300;
		$database =& JFactory::getDBO();
		switch ($this->filters['type'])
		{
			case 'profiles':
				ximport('Hubzero_User_Profile');
				include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'profile.php');
				include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'association.php');
				$mconfig =& JComponentHelper::getParams('com_members');
			break;
			case 'questions':
				$aconfig =& JComponentHelper::getParams('com_answers');
				require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'question.php');
				require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'response.php');
				require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'log.php');
				require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'questionslog.php');
			break;
			case 'tools':
				$rconfig =& JComponentHelper::getParams('com_resources');
				include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');
				include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'version.php');
			break;
			case 'resources':
				$rconfig =& JComponentHelper::getParams('com_resources');
				include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');
			break;
			case 'all':
			default:
				ximport('Hubzero_User_Profile');
				include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'profile.php');
				include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'association.php');
				require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'question.php');
				require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'response.php');
				require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'log.php');
				require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'questionslog.php');
				include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'version.php');
				include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');
				$mconfig =& JComponentHelper::getParams('com_members');
				$aconfig =& JComponentHelper::getParams('com_answers');
				$rconfig =& JComponentHelper::getParams('com_resources');
			break;
		}

		$now = date('Y-m-d H:i:s');
?>
					<!-- <ul class="features entries"> -->
<?php
		$html = '';
		$prevDate = '';
		$i = 0;
		foreach ($this->rows as $fh)
		{
			$i++;
			if ($fh->note == 'tools') 
			{
				$fh->tbl = 'tools';
			}
			$curDate = JHTML::_('date', $fh->featured, $dateFormat, $tz);
			if ($curDate != $prevDate)
			{
				$prevDate = $curDate;
				if ($i > 1) 
				{
					$html .= '</ul>';
				}
				$html .= '<h3>' . $prevDate . '</h3>';
				$html .= '<ul class="features entries">';
			}
			switch ($fh->tbl)
			{
				case 'tools':
					$row = new ResourcesResource($database);
					$row->load($fh->objectid);

					$path = DS . trim($rconfig->get('uploadpath'), DS);
					$path = FeaturesHtml::build_path($row->created, $row->id, $path);

					$tv = new ToolVersion($database);

					$versionid = $tv->getVersionIdFromResource($row->id, 'current');

					$picture = FeaturesHtml::getToolImage($path, $versionid);

					$thumb = $path . DS . $picture;
					if (!is_file(JPATH_ROOT . $thumb)) 
					{
						$thumb = FeaturesHtml::getContributorImage($row->id, $database);

						if (!is_file(JPATH_ROOT . $thumb)) 
						{
							$thumb = DS . trim($rconfig->get('defaultpic'), DS);
						}
					}

					$href  = 'index.php?option=com_resources&id=' . $row->id;

					$html .= "\t\t\t\t".'<li';
					if ($fh->featured > $now) 
					{
						$html .= ' class="upcoming"';
					}
					$html .= '>' . "\n";
					if (is_file(JPATH_ROOT . $thumb)) 
					{
						$html .= '<p class="featured-img"><img width="50" height="50" src="' . $thumb . '" alt="" /></p>' . "\n";
					}
					$html .= '<p class="title"><a href="' . JRoute::_($href) . '">' . $this->escape(stripslashes($row->title)) . '</a></p>' . "\n";
					$html .= '<p class="details">' . JText::_('COM_FEATURES_FEATURED') . ' ' . JHTML::_('date', $fh->featured, $dateFormat, $tz) . ' ' . JText::_('COM_FEATURES_IN') . ' ' . JText::_(strtoupper($this->option) . '_' . strtoupper($fh->tbl));
					if ($this->config->get('access-manage-component')) {
						$html .= ' <span>|</span> <a class="delete" href="' . JRoute::_('index.php?option=' . $this->option . '&task=delete&id=' . $fh->id) . '">' . JText::_('COM_FEATURES_DELETE') . '</a>' . "\n";
						$html .= ' <span>|</span> <a class="edit" href="' . JRoute::_('index.php?option=' . $this->option . '&task=edit&id=' . $fh->id) . '">' . JText::_('COM_FEATURES_EDIT') . '</a>' . "\n";
					}
					$html .= '</p>' . "\n";
					$html .= Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::xhtml(strip_tags($row->introtext)), $txt_length, 1) . "\n";
					$html .= '</li>' . "\n";
				break;

				case 'nontools':
				case 'resources':
					$row = new ResourcesResource($database);
					$row->load($fh->objectid);

					$path = DS . trim($rconfig->get('uploadpath'), DS);
					$path = FeaturesHtml::build_path($row->created, $row->id, $path);

					$picture = FeaturesHtml::getImage($path);

					$thumb = $path . DS . $picture;
					if (!is_file(JPATH_ROOT . $thumb)) 
					{
						$thumb = FeaturesHtml::getContributorImage($row->id, $database);

						if (!is_file(JPATH_ROOT . $thumb)) 
						{
							$thumb = DS . trim($rconfig->get('defaultpic'), DS);
						}
					}

					$href  = 'index.php?option=com_resources&id=' . $row->id;

					$html .= "\t\t\t\t".'<li';
					if ($fh->featured > $now) 
					{
						$html .= ' class="upcoming"';
					}
					$html .= '>' . "\n";
					if (is_file(JPATH_ROOT . $thumb)) 
					{
						$html .= '<p class="featured-img"><img width="50" height="50" src="' . $thumb . '" alt="" /></p>' . "\n";
					}
					$html .= '<p class="title"><a href="' . JRoute::_($href) . '">' . $this->escape(stripslashes($row->title)) . '</a></p>' . "\n";
					$html .= '<p class="details">' . JText::_('COM_FEATURES_FEATURED') . ' ' . JHTML::_('date', $fh->featured, $dateFormat, $tz) . ' ' . JText::_('COM_FEATURES_IN') . ' ' . JText::_(strtoupper($this->option) . '_' . strtoupper($fh->tbl));
					if ($this->config->get('access-manage-component')) 
					{
						$html .= ' <span>|</span> <a class="delete" href="' . JRoute::_('index.php?option=' . $this->option . '&task=delete&id=' . $fh->id) . '">' . JText::_('COM_FEATURES_DELETE') . '</a>' . "\n";
						$html .= ' <span>|</span> <a class="edit" href="' . JRoute::_('index.php?option=' . $this->option . '&task=edit&id=' . $fh->id) . '">' . JText::_('COM_FEATURES_EDIT') . '</a>' . "\n";
					}
					$html .= '</p>' . "\n";
					$html .= Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::xhtml(strip_tags($row->introtext)), $txt_length, 1) . "\n";
					$html .= '</li>' . "\n";
				break;

				case 'questions':
				case 'answers':
					$row = new AnswersTableQuestion($database);
					$row->load($fh->objectid);

					$ar = new AnswersTableResponse($database);
					$row->rcount = count($ar->getIds($row->id));

					$thumb = '/modules/mod_featuredquestion/question_thumb.gif'; //trim($params->get('defaultpic'));

					$name = JText::_('COM_FEATURES_ANONYMOUS');
					if ($row->anonymous == 0) 
					{
						$juser =& JUser::getInstance($row->created_by);
						if (is_object($juser)) 
						{
							$name = $juser->get('name');
						}
					}

					$row->created = FeaturesHtml::mkt($row->created);
					$when = FeaturesHtml::timeAgo($row->created);

					$html .= "\t\t\t\t".'<li';
					if ($fh->featured > $now) 
					{
						$html .= ' class="upcoming"';
					}
					$html .= '>' . "\n";
					if (is_file(JPATH_ROOT . $thumb)) 
					{
						$html .= '<p class="featured-img"><img width="50" height="50" src="' . $thumb . '" alt="" /></p>' . "\n";
					}
					$html .= '<p class="title"><a href="' . JRoute::_('index.php?option=com_answers&task=question&id='.$row->id) . '">' . $this->escape(stripslashes($row->subject)) . '</a></p>' . "\n";
					$html .= '<p class="details">' . JText::_('COM_FEATURES_FEATURED') . ' ' . JHTML::_('date', $fh->featured, $dateFormat, $tz) . ' ' . JText::_('COM_FEATURES_IN') . ' ' . JText::_(strtoupper($this->option) . '_' . strtoupper($fh->tbl));
					if ($this->config->get('access-manage-component')) 
					{
						$html .= ' <span>|</span> <a class="delete" href="' . JRoute::_('index.php?option=' . $this->option . '&task=delete&id=' . $fh->id) . '">' . JText::_('COM_FEATURES_DELETE') . '</a>' . "\n";
						$html .= ' <span>|</span> <a class="edit" href="' . JRoute::_('index.php?option=' . $this->option . '&task=edit&id=' . $fh->id) . '">' . JText::_('COM_FEATURES_EDIT') . '</a>' . "\n";
					}
					$html .= '</p>' . "\n";
					$html .= '<p><span>' . JText::sprintf('COM_FEATURES_ASKED_BY', $name) . '</span> - <span>'.$when.' ago</span> - <span>';
					$html .= ($row->rcount == 1) ? JText::sprintf('COM_FEATURES_RESPONSE', $row->rcount) : JText::sprintf('COM_FEATURES_RESPONSES', $row->rcount);
					$html .= '</span></p>' . "\n";
					$html .= Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::xhtml(strip_tags($row->question)), $txt_length, 1) . "\n";
					$html .= '</li>' . "\n";
				break;

				case 'xprofiles':
				case 'profiles':
					$row = new MembersProfile($database);
					$row->load($fh->objectid);

					// Member profile
					$title = $row->name;
					if (!trim($title)) 
					{
						$title = $row->givenName . ' ' . $row->surname;
					}
					$id = $row->uidNumber;

					// Load their bio
					$profile = new Hubzero_User_Profile();
					$profile->load($row->uidNumber);
					$txt = $profile->get('bio');

					// Do we have a picture?
					$thumb = '';
					if (isset($row->picture) && $row->picture != '') 
					{
						// Yes - so build the path to it
						$thumb  = DS . trim($mconfig->get('webpath'), DS) . DS . FeaturesHtml::niceidformat($row->uidNumber) . DS . $row->picture;

						// No - use default picture
						if (is_file(JPATH_ROOT . $thumb)) 
						{
							// Build a thumbnail filename based off the picture name
							$thumb = FeaturesHtml::thumb($thumb);
						}
					}

					// No - use default picture
					if (!is_file(JPATH_ROOT . $thumb)) 
					{
						$thumb = DS . trim($mconfig->get('defaultpic'), DS);
						// Build a thumbnail filename based off the picture name
						$thumb = FeaturesHtml::thumb($thumb);
					}

					$html .= "\t\t\t\t".'<li';
					if ($fh->featured > $now) 
					{
						$html .= ' class="upcoming"';
					}
					$html .= '>' . "\n";
					if (is_file(JPATH_ROOT . $thumb)) 
					{
						$html .= '<p class="featured-img"><img width="50" height="50" src="' . $thumb . '" alt="" /></p>' . "\n";
					}
					$html .= '<p class="title"><a href="' . JRoute::_('index.php?option=com_members&id='.$id) . '">' . stripslashes($title) . '</a></p>' . "\n";
					$html .= '<p class="details">' . JText::_('COM_FEATURES_FEATURED') . ' ' . JHTML::_('date', $fh->featured, $dateFormat, $tz) . ' ' . JText::_('COM_FEATURES_IN') . ' ' . JText::_(strtoupper($this->option) . '_' . strtoupper($fh->tbl));
					if ($this->config->get('access-manage-component')) 
					{
						$html .= ' <span>|</span> <a class="delete" href="' . JRoute::_('index.php?option=' . $this->option . '&task=delete&id=' . $fh->id) . '">' . JText::_('COM_FEATURES_DELETE') . '</a>' . "\n";
						$html .= ' <span>|</span> <a class="edit" href="' . JRoute::_('index.php?option=' . $this->option . '&task=edit&id=' . $fh->id) . '">' . JText::_('COM_FEATURES_EDIT') . '</a>' . "\n";
					}
					$html .= '</p>' . "\n";
					$html .= Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::xhtml(strip_tags($txt)), $txt_length, 1) . "\n";
					$html .= '</li>' . "\n";
				break;
			}
		}
		echo $html;
?>
					</ul>
				</div><!-- / .container-block -->

<?php
				$this->pageNav->setAdditionalUrlParam('type', $this->filters['type']);
				echo $this->pageNav->getListFooter();
} else { ?>
				<p class="warning"><?php echo JText::_('COM_FEATURES_NONE_FOUND'); ?></p>
<?php } ?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</div><!-- / .subject -->
		<div class="clear"></div>
	</form>
</div><!-- / .main section -->

