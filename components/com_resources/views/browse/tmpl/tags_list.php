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
defined('_JEXEC') or die( 'Restricted access' );

$html = '';
switch ($this->level)
{
	case 1:
		$tags = $this->bits['tags'];
		$tg = $this->bits['tg'];
		$tg2 = $this->bits['tg2'];
		$type = $this->bits['type'];
		$id = $this->bits['id'];
		$d = 0;

		if ($tg2) {
			$html .= '<h3>'.JText::_('COM_RESOURCES_TAG').' + '.$tg2.'</h3>';
		} else {
			$html .= '<h3>'.JText::_('COM_RESOURCES_TAG').'</h3>';
		}
		$html .= '<ul id="ultags">';
		if (!$tg2) {
			$html .= '<li><a id="col1_all" class="';
			if ($tg == '') {
				$html .= 'open';
			}
			$html .= '" href="javascript:HUB.TagBrowser.nextLevel(\''.$type.'\',\'\',\'\',2,\'col1_all\',\''.$id.'\');">[ All ]</a></li>';
		}
		$lis = '';
		$i = 0;
		foreach ($tags as $tag)
		{
			$i++;
			$li  = '<li';
			if ($this->bits['supportedtag'] && $tag->tag == $this->bits['supportedtag']) {
				$li .= ' class="supported"';
				$i = 0;
			}
			$li .= '><a id="col1_'.$tag->tag.'" class="';
			if ($tg == $tag->tag) {
				$li .= 'open';
				$d = $i;
			}

			$li .= '" href="javascript:HUB.TagBrowser.nextLevel(\''.$type.'\',\''.$tag->tag.'\',\''.$tg2.'\',2,\'col1_'.$tag->tag.'\',\''.$id.'\');">'.stripslashes($tag->raw_tag).' ('.$tag->ucount.')</a></li>';

			if ($this->bits['supportedtag'] && $tag->tag == $this->bits['supportedtag']) {
				$html .= $li;
			} else {
				$lis .= $li;
			}
		}
		if ($tg == '') {
			$tg = 'all';
		}
		$html .= $lis;
		$html .= '</ul><input type="hidden" name="atg" id="atg" value="'. htmlentities($tg) .'" /><input type="hidden" name="d" id="d" value="'.$d.'" />';
	break;

	case 2:
		$tools = $this->bits['tools'];
		$typetitle = $this->bits['typetitle'];
		$type = $this->bits['type'];
		$rt = $this->bits['rt'];
		$params = $this->bits['params'];
		$filters = $this->bits['filters'];

		$sortbys = array(
			'date'=>JText::_('COM_RESOURCES_SORT_BY').' '.JText::_('COM_RESOURCES_DATE'),
			'title'=>JText::_('COM_RESOURCES_SORT_BY').' '.JText::_('COM_RESOURCES_TITLE'),
			'ranking'=>JText::_('COM_RESOURCES_SORT_BY').' '.JText::_('COM_RESOURCES_RANKING')
		);
		if ($type == 7) {
			$sortbys['users'] = JText::_('COM_RESOURCES_SORT_BY').' '.JText::_('COM_RESOURCES_USERS');
			$sortbys['jobs'] = JText::_('COM_RESOURCES_SORT_BY').' '.JText::_('COM_RESOURCES_JOBS');
		}

		$html .= '<h3>'.JText::_('COM_RESOURCES').' '.ResourcesHtml::formSelect('sortby', $sortbys, $this->bits['sortby'], '" onchange="javascript:HUB.TagBrowser.changeSort();"').'</h3>';
		$html .= '<ul id="ulitems">';
		if ($tools && count($tools) > 0) {
			//$database =& JFactory::getDBO();
			foreach ($tools as $tool)
			{
				$tool->title = Hubzero_View_Helper_Html::shortenText($tool->title, 40, 0);

				$supported = null;
				if ($this->bits['supportedtag']) {
					if (in_array($tool->id, $this->bits['supportedtagusage'])) {
						$supported = true;
					}
					//$supported = $rt->checkTagUsage( $this->bits['supportedtag'], $tool->id );
				}

				$html .= '<li ';
				if ($this->bits['supportedtag'] && ($this->bits['tag'] == $this->bits['supportedtag'] || $supported)) {
					$html .= 'class="supported" ';
				}
				$html .= '><a id="col2_'.$tool->id.'" href="javascript:HUB.TagBrowser.nextLevel(\''.$type.'\',\''.$tool->id.'\',\'\',3,\'col2_'.$tool->id.'\',\'\');">'.stripslashes($tool->title).'</a></li>';
			}
		} else {
			$html .= '<li><span>'.JText::_('COM_RESOURCES_NO_RESULTS').'</span></li>';
		}
		$html .= '</ul>';
		if ($type == 7 && $params->get('show_ranking')) {
			$this->bits['filter'] = is_array ($this->bits['filter']) ? $this->bits['filter'] : array();
			if(!empty($filters)) {
				$html .= '<div id="filteroptions">';
				$html .= ' <div>'.JText::_('Show:');
				foreach ($filters as $avalue => $alabel)
				{
					$html .= ' <label class="skill_'.$avalue.'"><input type="checkbox" class="option" name="filter" value="'.$avalue.'" onchange="javascript:HUB.TagBrowser.changeSort();" ';
					$html .= in_array($avalue, $this->bits['filter']) ? 'checked="checked"' : '';
					$html .= ' /> '.$alabel.'</label>';
				}
				if($params->get('audiencelink')) {
					$html .= ' <span>'.JText::_('COM_RESOURCES_WHATS_THIS').' <a href="'.$params->get('audiencelink').'">'.JText::_('About audience levels').' &rsaquo;</a></span>';
				}
				$html .= ' </div>';
				$html .= '</div>';
			}
		}
	break;

	case 3:
		$resource = $this->bits['resource'];
		$helper = $this->bits['helper'];
		$sef = $this->bits['sef'];
		$sections = $this->bits['sections'];
		$primary_child = (isset($this->bits['primary_child'])) ? $this->bits['primary_child'] : '';
		$params = $this->bits['params'];
		$rt = $this->bits['rt'];
		$config = $this->bits['config'];
		$authorized = $this->bits['authorized'];

		$database =& JFactory::getDBO();

		$statshtml = '';
		if ($params->get('show_ranking')) {
			$helper->getLastCitationDate();

			if ($resource->type == 7) {
				$stats = new ToolStats($database, $resource->id, $resource->type, $resource->rating, $helper->citationsCount, $helper->lastCitationDate);
			} else {
				$stats = new AndmoreStats($database, $resource->id, $resource->type, $resource->rating, $helper->citationsCount, $helper->lastCitationDate);
			}

			$statshtml = $stats->display();
		}

		$html .= '<h3>'.JText::_('COM_RESOURCES_INFO').'</h3>';
		$html .= '<ul id="ulinfo">';
		$html .= '<li>';
		$html .= '<h4';
		switch ($resource->access)
		{
			case 1: $html .= ' class="registered"'; break;
			case 2: $html .= ' class="special"'; break;
			case 3: $html .= ' class="protected"'; break;
			case 4: $html .= ' class="private"'; break;
			case 0:
			default: $html .= ' class="public"'; break;
		}
		$html .= '><a href="'.$sef.'">'.Hubzero_View_Helper_Html::xhtml(stripslashes($resource->title)).'</a></h4>';
		$html .= '<p>'.Hubzero_View_Helper_Html::shortenText(stripslashes($resource->introtext), 400, 0).' &nbsp; <a href="'.$sef.'">'.JText::_('COM_RESOURCES_LEARN_MORE').'</a></p>';

		$juser =& JFactory::getUser();
		if (!$juser->get('guest')) {
			ximport('Hubzero_User_Helper');
			$xgroups = Hubzero_User_Helper::getGroups($juser->get('id'), 'all');
			// Get the groups the user has access to
			$usersgroups = ResourcesControllerResources::getUsersGroups($xgroups);
		} else {
			$usersgroups = array();
		}

		$helper->getFirstChild();

		if ($resource->access == 3 && !in_array($resource->group_owner, $usersgroups) && !$authorized) {
			$ghtml = JText::_('You must be logged in and a member of one of the following groups to access the full resource:').' ';
			$allowedgroups = $resource->getGroups();
			foreach ($allowedgroups as $allowedgroup)
			{
				$ghtml .= '<a href="'.JRoute::_('index.php?option=com_groups&cn='.$allowedgroup).'">'.$allowedgroup.'</a>, ';
			}
			$ghtml = substr($ghtml,0,strlen($ghtml) - 2);
			$html .= ResourcesHtml::warning( $ghtml )."\n";
		} else {
			if ($helper->firstChild || $resource->type == 7) {
				$html .= $primary_child;
			}
		}

		$supported = null;
		if ($this->bits['supportedtag']) {
			$supported = $rt->checkTagUsage( $this->bits['supportedtag'], $resource->id );
		}
		$xtra = '';

		if ($params->get('show_audience')) {
			include_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'tables'.DS.'audience.php');
			include_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'tables'.DS.'audience.level.php');
			$ra = new ResourceAudience( $database );
			$audience = $ra->getAudience($resource->id, 0, 1, 4);
			$xtra .= ResourcesHtml::showSkillLevel($audience, 0, 4, $params->get('audiencelink'));
		}
		if ($this->bits['supportedtag'] && $supported) {
			include_once(JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'handler.php');
			$tag = new TagsTableTag( $database );
			$tag->loadTag($config->get('supportedtag'));

			$sl = $config->get('supportedlink');
			if ($sl) {
				$link = $sl;
			} else {
				$link = JRoute::_('index.php?option=com_tags&tag='.$tag->tag);
			}

			$xtra .= '<p class="supported"><a href="'.$link.'">'.$tag->raw_tag.'</a></p>';
		}

		if ($params->get('show_metadata')) {
			$html .= ResourcesHtml::metadata($params, $resource->ranking, $statshtml, $resource->id, $sections, $xtra);
		}
		$html .= '<input type="hidden" name="rid" id="rid" value="'.$resource->id.'" /></li>';
		$html .= '</ul><script type="text/javascript">HUB.Base.popups();HUB.Base.launchTool();</script>';
	break;
}
echo $html;
