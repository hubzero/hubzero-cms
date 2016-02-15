<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

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
			$html .= '<h3>'.Lang::txt('COM_RESOURCES_TAG').' + '.$tg2.'</h3>';
		} else {
			$html .= '<h3>'.Lang::txt('COM_RESOURCES_TAG').'</h3>';
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
			'date'=>Lang::txt('COM_RESOURCES_SORT_BY').' '.Lang::txt('COM_RESOURCES_DATE'),
			'title'=>Lang::txt('COM_RESOURCES_SORT_BY').' '.Lang::txt('COM_RESOURCES_TITLE'),
			'ranking'=>Lang::txt('COM_RESOURCES_SORT_BY').' '.Lang::txt('COM_RESOURCES_RANKING')
		);
		if ($type == 7) {
			$sortbys['users'] = Lang::txt('COM_RESOURCES_SORT_BY').' '.Lang::txt('COM_RESOURCES_USERS');
			$sortbys['jobs'] = Lang::txt('COM_RESOURCES_SORT_BY').' '.Lang::txt('COM_RESOURCES_JOBS');
		}

		$html .= '<h3>'.Lang::txt('COM_RESOURCES').' '.\Components\Resources\Helpers\Html::formSelect('sortby', $sortbys, $this->bits['sortby'], '" onchange="javascript:HUB.TagBrowser.changeSort();"').'</h3>';
		$html .= '<ul id="ulitems">';
		if ($tools && count($tools) > 0) {
			//$database = App::get('db');
			foreach ($tools as $tool)
			{
				$tool->title = \Hubzero\Utility\String::truncate($tool->title, 40);

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
			$html .= '<li><span>'.Lang::txt('COM_RESOURCES_NO_RESULTS').'</span></li>';
		}
		$html .= '</ul>';
		if ($type == 7 && $params->get('show_ranking')) {
			$this->bits['filter'] = is_array ($this->bits['filter']) ? $this->bits['filter'] : array();
			if (!empty($filters)) {
				$html .= '<div id="filteroptions">';
				$html .= ' <div>'.Lang::txt('Show:');
				foreach ($filters as $avalue => $alabel)
				{
					$html .= ' <label class="skill_'.$avalue.'"><input type="checkbox" class="option" name="filter" value="'.$avalue.'" onchange="javascript:HUB.TagBrowser.changeSort();" ';
					$html .= in_array($avalue, $this->bits['filter']) ? 'checked="checked"' : '';
					$html .= ' /> '.$alabel.'</label>';
				}
				if ($params->get('audiencelink')) {
					$html .= ' <span>'.Lang::txt('COM_RESOURCES_WHATS_THIS').' <a href="'.$params->get('audiencelink').'">'.Lang::txt('About audience levels').' &rsaquo;</a></span>';
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

		$database = App::get('db');

		$statshtml = '';
		if ($params->get('show_ranking')) {
			$helper->getLastCitationDate();

			if ($resource->type == 7) {
				$stats = new \Components\Resources\Helpers\Usage\Tools($database, $resource->id, $resource->type, $resource->rating, $helper->citationsCount, $helper->lastCitationDate);
			} else {
				$stats = new \Components\Resources\Helpers\Usage\Andmore($database, $resource->id, $resource->type, $resource->rating, $helper->citationsCount, $helper->lastCitationDate);
			}

			$statshtml = $stats->display();
		}

		$html .= '<h3>'.Lang::txt('COM_RESOURCES_INFO').'</h3>';
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
		$html .= '><a href="'.$sef.'">'.$this->escape(stripslashes($resource->title)).'</a></h4>';
		$html .= '<p>'.\Hubzero\Utility\String::truncate(stripslashes($resource->introtext), 400).' &nbsp; <a href="'.$sef.'">'.Lang::txt('COM_RESOURCES_LEARN_MORE').'</a></p>';

		if (!User::isGuest()) {
			$xgroups = \Hubzero\User\Helper::getGroups(User::get('id'), 'all');
			// Get the groups the user has access to
			$usersgroups = \Components\Resources\Site\Controllers\Resources::getUsersGroups($xgroups);
		} else {
			$usersgroups = array();
		}

		$helper->getFirstChild();

		if ($resource->access == 3 && !in_array($resource->group_owner, $usersgroups) && !$authorized) {
			$ghtml = Lang::txt('You must be logged in and a member of one of the following groups to access the full resource:').' ';
			$allowedgroups = $resource->getGroups();
			foreach ($allowedgroups as $allowedgroup)
			{
				$ghtml .= '<a href="'.Route::url('index.php?option=com_groups&cn='.$allowedgroup).'">'.$allowedgroup.'</a>, ';
			}
			$ghtml = substr($ghtml,0,strlen($ghtml) - 2);
			$html .= '<p class="warning">' . $ghtml . '</p>' ."\n";
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
			include_once(PATH_CORE.DS.'components'.DS.'com_resources'.DS.'tables'.DS.'audience.php');
			include_once(PATH_CORE.DS.'components'.DS.'com_resources'.DS.'tables'.DS.'audiencelevel.php');
			$ra = new \Components\Resources\Tables\Audience($database);
			$audience = $ra->getAudience($resource->id, 0, 1, 4);

			$view = $this->view('_audience', 'view')
						->set('audience', $audience)
						->set('showtips', 0)
						->set('numlevels', 4)
						->set('audiencelink', $params->get('audiencelink'));
			$xtra .= $view->loadTemplate();
		}
		if ($this->bits['supportedtag'] && $supported) {
			include_once(PATH_CORE.DS.'components'.DS.'com_tags'.DS.'models'.DS.'cloud.php');

			$tag = \Components\Tags\Models\Tag::oneByTag($config->get('supportedtag'));

			$sl = $config->get('supportedlink');
			if ($sl) {
				$link = $sl;
			} else {
				$link = Route::url('index.php?option=com_tags&tag='.$tag->get('tag'));
			}

			$xtra .= '<p class="supported"><a href="'.$link.'">'.$tag->get('raw_tag').'</a></p>';
		}

		if ($params->get('show_metadata')) {
			$view = $this->view('_metadata', 'view');
			$view->option = 'com_resources';
			$view->sections = $sections;
			$view->model = \Components\Resources\Models\Resource::getInstance($resource->id);

			$html .= $view->loadTemplate();
		}
		$html .= '<input type="hidden" name="rid" id="rid" value="'.$resource->id.'" /></li>';
		$html .= '</ul>';
	break;
}
echo $html;
