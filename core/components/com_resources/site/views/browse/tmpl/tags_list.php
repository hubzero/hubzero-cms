<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$html = '';
switch ($this->level)
{
	case 1:
		$tags = $this->bits['tags'];
		$tg   = $this->bits['tg'];
		$tg2  = $this->bits['tg2'];
		$type = $this->bits['type'];
		$id   = $this->bits['id'];
		$d    = 0;

		$html .= '<h3>' . Lang::txt('COM_RESOURCES_TAG');
		if ($tg2)
		{
			$html .= ' + ' . $tg2;
		}
		$html .= '</h3>';

		$html .= '<ul id="ultags">';
		if (!$tg2)
		{
			$html .= '<li><a id="col1_all" class="';
			if ($tg == '')
			{
				$html .= 'open';
			}
			$html .= '" href="javascript:HUB.TagBrowser.nextLevel(\''.$type->get('id').'\',\'\',\'\',2,\'col1_all\',\''.$id.'\');">[ All ]</a></li>';
		}
		$lis = '';
		$i = 0;
		foreach ($tags as $tag)
		{
			$i++;
			$li  = '<li';
			if ($this->bits['supportedtag'] && $tag->tag == $this->bits['supportedtag'])
			{
				$li .= ' class="supported"';
				$i = 0;
			}
			$li .= '><a id="col1_'.$tag->tag.'" class="';
			if ($tg == $tag->tag)
			{
				$li .= 'open';
				$d = $i;
			}

			$li .= '" href="javascript:HUB.TagBrowser.nextLevel(\''.$type->get('id').'\',\''.$tag->tag.'\',\''.$tg2.'\',2,\'col1_'.$tag->tag.'\',\''.$id.'\');">'.stripslashes($tag->raw_tag).' ('.$tag->ucount.')</a></li>';

			if ($this->bits['supportedtag'] && $tag->tag == $this->bits['supportedtag'])
			{
				$html .= $li;
			}
			else
			{
				$lis .= $li;
			}
		}
		if ($tg == '')
		{
			$tg = 'all';
		}
		$html .= $lis;
		$html .= '</ul><input type="hidden" name="atg" id="atg" value="'. htmlentities($tg) .'" /><input type="hidden" name="d" id="d" value="'.$d.'" />';
	break;

	case 2:
		$tools     = $this->bits['tools'];
		$type      = $this->bits['type'];
		$rt        = $this->bits['rt'];
		$params    = $this->bits['params'];
		$filters   = $this->bits['filters'];

		$sortbys = array(
			'date'  => Lang::txt('COM_RESOURCES_SORT_BY').' '.Lang::txt('COM_RESOURCES_DATE'),
			'title' => Lang::txt('COM_RESOURCES_SORT_BY').' '.Lang::txt('COM_RESOURCES_TITLE')
		);
		if ($this->bits['ranking'])
		{
			$sortbys['ranking'] = Lang::txt('COM_RESOURCES_SORT_BY').' '.Lang::txt('COM_RESOURCES_RANKING');
		}
		if ($type->isForTools())
		{
			$sortbys['users'] = Lang::txt('COM_RESOURCES_SORT_BY').' '.Lang::txt('COM_RESOURCES_USERS');
			$sortbys['jobs'] = Lang::txt('COM_RESOURCES_SORT_BY').' '.Lang::txt('COM_RESOURCES_JOBS');
		}

		$html .= '<h3>'.Lang::txt('COM_RESOURCES').' '.\Components\Resources\Helpers\Html::formSelect('sortby', $sortbys, $this->bits['sortby'], '" onchange="javascript:HUB.TagBrowser.changeSort();"').'</h3>';
		$html .= '<ul id="ulitems">';
		if ($tools && count($tools) > 0)
		{
			foreach ($tools as $tool)
			{
				$tool->title = \Hubzero\Utility\Str::truncate($tool->title, 40);

				$supported = null;
				if ($this->bits['supportedtag'])
				{
					if (in_array($tool->id, $this->bits['supportedtagusage']))
					{
						$supported = true;
					}
					//$supported = $rt->checkTagUsage( $this->bits['supportedtag'], $tool->id );
				}

				$html .= '<li ';
				if ($this->bits['supportedtag'] && ($this->bits['tag'] == $this->bits['supportedtag'] || $supported))
				{
					$html .= 'class="supported" ';
				}
				$html .= '><a id="col2_'.$tool->id.'" href="javascript:HUB.TagBrowser.nextLevel(\''.$type->get('id').'\',\''.$tool->id.'\',\'\',3,\'col2_'.$tool->id.'\',\'\');">'.stripslashes($tool->title).'</a></li>';
			}
		}
		else
		{
			$html .= '<li><span>'.Lang::txt('COM_RESOURCES_NO_RESULTS').'</span></li>';
		}
		$html .= '</ul>';
		if ($type->isForTools() && $params->get('show_ranking'))
		{
			$this->bits['filter'] = is_array ($this->bits['filter']) ? $this->bits['filter'] : array();
			if (!empty($filters))
			{
				$html .= '<div id="filteroptions">';
				$html .= ' <div>'.Lang::txt('Show:');
				foreach ($filters as $avalue => $alabel)
				{
					$html .= ' <label class="skill_'.$avalue.'"><input type="checkbox" class="option" name="filter" value="'.$avalue.'" onchange="javascript:HUB.TagBrowser.changeSort();" ';
					$html .= in_array($avalue, $this->bits['filter']) ? 'checked="checked"' : '';
					$html .= ' /> '.$alabel.'</label>';
				}
				if ($params->get('audiencelink'))
				{
					$html .= ' <span>'.Lang::txt('COM_RESOURCES_WHATS_THIS').' <a href="'.$params->get('audiencelink').'">'.Lang::txt('About audience levels').' &rsaquo;</a></span>';
				}
				$html .= ' </div>';
				$html .= '</div>';
			}
		}
	break;

	case 3:
		$resource   = $this->bits['resource'];
		$sef        = $this->bits['sef'];
		$sections   = $this->bits['sections'];
		$primary_child = (isset($this->bits['primary_child'])) ? $this->bits['primary_child'] : '';
		$params     = $this->bits['params'];
		$rt         = $this->bits['rt'];
		$config     = $this->bits['config'];
		$authorized = $this->bits['authorized'];

		$database = App::get('db');

		$statshtml = '';
		if ($params->get('show_ranking'))
		{
			if ($resource->isTool())
			{
				$stats = new \Components\Resources\Helpers\Usage\Tools($database, $resource->id, $resource->type, $resource->rating);
			}
			else
			{
				$stats = new \Components\Resources\Helpers\Usage\Andmore($database, $resource->id, $resource->type, $resource->rating);
			}

			$statshtml = $stats->display();
		}

		$html .= '<h3>'.Lang::txt('COM_RESOURCES_INFO').'</h3>';
		$html .= '<ul id="ulinfo">';
		$html .= '<li>';
		$html .= '<h4';
		switch ($resource->access)
		{
			case 1:
				$html .= ' class="registered"';
				break;
			case 2:
				$html .= ' class="special"';
				break;
			case 3:
				$html .= ' class="protected"';
				break;
			case 4:
				$html .= ' class="private"';
				break;
			case 0:
			default:
				$html .= ' class="public"';
				break;
		}
		$html .= '><a href="'.$sef.'">'.$this->escape(stripslashes($resource->title)).'</a></h4>';
		$html .= '<p>'.\Hubzero\Utility\Str::truncate(stripslashes($resource->introtext), 400).' &nbsp; <a href="'.$sef.'">'.Lang::txt('COM_RESOURCES_LEARN_MORE').'</a></p>';

		$usersgroups = array();
		if (!User::isGuest())
		{
			$xgroups = \Hubzero\User\Helper::getGroups(User::get('id'), 'all');
			// Get the groups the user has access to
			if (!empty($xgroups))
			{
				foreach ($xgroups as $group)
				{
					if ($group->regconfirmed)
					{
						$usersgroups[] = $group->cn;
					}
				}
			}
		}

		if ($resource->access == 3 && !in_array($resource->group_owner, $usersgroups) && !$authorized)
		{
			$ghtml = array();
			foreach ($resource->groups as $allowedgroup)
			{
				$ghtml[] = '<a href="'.Route::url('index.php?option=com_groups&cn='.$allowedgroup).'">'.$allowedgroup.'</a>';
			}

			$html .= '<p class="warning">' . Lang::txt('You must be logged in and a member of one of the following groups to access the full resource:') . ' ' . implode(', ', $ghtml) . '</p>' ."\n";
		}
		else
		{
			$firstChild = $resource->children()
				->whereEquals('standalone', 0)
				->whereEquals('published', \Components\Resources\Models\Entry::STATE_PUBLISHED)
				->ordered()
				->rows()
				->first();

			if ($firstChild || $resource->isTool())
			{
				$html .= $primary_child;
			}
		}

		$supported = null;
		if ($this->bits['supportedtag'])
		{
			$supported = $rt->checkTagUsage($this->bits['supportedtag'], $resource->id);
		}
		$xtra = '';

		if ($params->get('show_audience'))
		{
			include_once Component::path('com_resources') . DS . 'models' . DS . 'audience.php';

			$audience = \Components\Resources\Models\Audience::all()
				->whereEquals('rid', $resource->id)
				->row();

			$view = $this->view('_audience', 'view')
				->set('audience', $audience)
				->set('showtips', 0)
				->set('numlevels', 4)
				->set('audiencelink', $params->get('audiencelink'));

			$xtra .= $view->loadTemplate();
		}
		if ($this->bits['supportedtag'] && $supported)
		{
			$tag = \Components\Tags\Models\Tag::oneByTag($config->get('supportedtag'));

			$sl = $config->get('supportedlink');
			if ($sl)
			{
				$link = $sl;
			}
			else
			{
				$link = Route::url($tag->link());
			}

			$xtra .= '<p class="supported"><a href="'.$link.'">'.$tag->get('raw_tag').'</a></p>';
		}

		if ($params->get('show_metadata'))
		{
			$view = $this->view('_metadata', 'view');
			$view->set('option', 'com_resources');
			$view->set('sections', $sections);
			$view->set('model', $resource);

			$html .= $view->loadTemplate();
		}
		$html .= '<input type="hidden" name="rid" id="rid" value="'.$resource->id.'" /></li>';
		$html .= '</ul>';
	break;
}
echo $html;
