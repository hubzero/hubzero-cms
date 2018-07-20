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
 *
 */

namespace Components\Resources\Api\Controllers;

use Components\Resources\Models\Entry;
use Components\Resources\Models\Type;
use Components\Tags\Models\Cloud;
use Hubzero\Component\ApiController;
use Component;
use Exception;
use stdClass;
use Request;
use Route;
use Lang;
use Date;
use App;

/**
 * API controller class for resources
 */
class Entriesv1_1 extends ApiController
{
	/**
	 * Get a list of resources
	 *
	 * @apiMethod GET
	 * @apiUri    /resources/list
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "start",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "type",
	 * 		"description":   "Type of resource to filter results.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "sortby",
	 * 		"description":   "Value to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "date",
	 * 		"allowedValues": "date, title, random"
	 * }
	 * @apiParameter {
	 * 		"name":          "search",
	 * 		"description":   "A word or phrase to search for.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @return    void
	 */
	public function listTask()
	{
		// Incoming
		$filters = array(
			'type'   => Request::getString('type', ''),
			'sortby' => Request::getCmd('sortby', 'date'),
			'limit'  => Request::getInt('limit', Config::get('list_limit')),
			'start'  => Request::getInt('limitstart', 0),
			'search' => Request::getString('search', '')
		);

		$admin = false;
		if (User::authorise('core.admin', 'com_resources'))
		{
			$admin = true;
			$filters['tag'] = '';
			$searchable = Request::getBool('searchable', false);

			require_once Component::path('com_tags') . '/models/cloud.php';
		}

		if (!in_array($filters['sortby'], array('date', 'date_published', 'date_created', 'date_modified', 'title', 'rating', 'ranking', 'random')))
		{
			App::abort(404, Lang::txt('Invalid sort value of "%s" used.', $filters['sortby']));
		}

		require_once Component::path('com_resources') . DS . 'models' . DS . 'entry.php';

		$query = Entry::all();

		$r = $query->getTableName();

		$query->whereEquals($r . '.standalone', 1);

		if ($filters['search'])
		{
			$filters['search'] = strtolower((string)$filters['search']);

			$query->whereLike($r . '.title', $filters['search'], 1)
					->orWhereLike($r . '.fulltxt', $filters['search'], 1)
					->resetDepth();
		}

		if ($filters['type'])
		{
			$query->whereEquals($r . '.type', $filters['type']);
		}

		$query->whereEquals($r . '.publish_up', '0000-00-00 00:00:00', 1)
			->orWhere($r . '.publish_up', '<=', Date::toSql(), 1)
			->resetDepth();

		$query->whereEquals($r . '.publish_down', '0000-00-00 00:00:00', 1)
			->orWhere($r . '.publish_down', '>=', Date::toSql(), 1)
			->resetDepth();

		$query->whereEquals($r . '.published', Entry::STATE_PUBLISHED);

		switch ($filters['sortby'])
		{
			case 'date_created':
				$query->order($r . '.created', 'desc');
				break;
			case 'date_modified':
				$query->order($r . '.modified', 'desc');
				break;
			case 'title':
				$query->order($r . '.title', 'asc');
				$query->order($r . '.publish_up', 'asc');
				break;
			case 'rating':
				$query->order($r . '.rating', 'desc');
				$query->order($r . '.times_rated', 'desc');
				break;
			case 'ranking':
				$query->order($r . '.ranking', 'desc');
				break;
			case 'date':
			case 'date_published':
			default:
				$query->order($r . '.publish_up', 'desc');
				$query->order($r . '.created', 'desc');
				break;
		}

		// encode results and return response
		$response = new stdClass;
		$response->records = array();
		$response->total   = with(clone $query)->total();

		if ($response->total)
		{
			// Get major types
			$types = array();
			foreach (Type::getMajorTypes()->toObject() as $type)
			{
				unset($type->params);
				unset($type->customFields);

				$types[$type->id] = $type;
			}

			$records = $query
				->paginated()
				->rows();

			if (isset($searchable) && $admin)
			{
				foreach ($records as $entry)
				{
					$obj = new stdClass;

					if ($entry->alias != '')
					{
						$obj->url = '/resources/' . $entry->alias;
					}
					else
					{
						$obj->url = '/resources/' . $entry->id;
					}

					$obj->title   = $entry->title;
					$obj->id      = 'resource-' . $entry->id;
					$obj->hubtype = 'resource';

					if (isset($types[$entry->get('type')]))
					{
						$obj->type = $types[$entry->get('type')]->type;
					}

					$description = $entry->get('fulltxt') . ' ' . $entry->get('introtext');
					$description = html_entity_decode($description);
					$description = \Hubzero\Utility\Sanitize::stripAll($description);
					$obj->description = $description;

					$authors = $entry
						->authors()
						->select('name')
						->rows()
						->toObject();

					$obj->author = array();
					foreach ($authors as $author)
					{
						array_push($obj->author, $author->name);
					}

					if ($entry->standalone != 1 || $entry->published != 1)
					{
						$obj->access_level = 'private';
					}
					else
					{
						switch ($entry->access)
						{
							case 0:
								$obj->access_level = 'public';
							break;
							case 1:
								$obj->access_level = 'registered';
							break;
							case 4:
							default:
								$obj->access_level = 'private';
						}
					}

					$tagCloud = new Cloud($entry->id, 'resources');
					$tags = $tagCloud->tags()->toObject();

					if (!empty($tags))
					{
						foreach ($tags as $tag)
						{
							$obj->tags[] = $tag->raw_tag;
						}
					}

					$groups = $entry->groups;
					if (!empty($groups))
					{
						foreach ($groups as $g => $group)
						{
							$grp = \Hubzero\User\Group::getInstance($group);
							if ($grp)
							{
								$groups[$g] = $grp->get('gidNumber');
							}
							// Group not found
							else
							{
								unset($groups[$g]);
							}
						}
						$groups = array_unique($groups);
						$obj->owner_type = 'group';
						$obj->owner = $groups;
					}
					else
					{
						$obj->owner_type = 'user';
						$obj->owner = $entry->created_by;
					}

					$response->resources[] = $obj;
				}
			}
			else
			{
				$base = rtrim(Request::base(), '/');

				foreach ($records as $record)
				{
					$entry = $record->toObject();
					$entry->params = $record->params->toObject();
					$entry->attribs = $record->attribs->toObject();
					$entry->url = str_replace('/api', '', $base . '/' . ltrim(Route::url($record->link()), '/'));

					if (isset($types[$entry->type]))
					{
						$entry->type = $types[$entry->type];
					}

					$response->resources[] = $entry;
				}
			}
		}

		$response->success = true;

		$this->send($response);
	}

	/**
	 * Get a list of new content for a given time period
	 *
	 * @apiMethod GET
	 * @apiUri    /resources/whatsnew
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "start",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "period",
	 * 		"description":   "Time period.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "month"
	 * }
	 * @apiParameter {
	 * 		"name":          "category",
	 * 		"description":   "Type of resource to filter results.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "resources"
	 * }
	 * @return  void
	 */
	public function whatsnewTask()
	{
		$limit    = Request::getInt('limit', 25);
		$period   = Request::getString('period', 'month');
		$category = Request::getString('category', 'resources');

		require_once Component::path('com_whatsnew') . DS . 'helpers' . DS . 'finder.php';

		$whatsnew = \Components\Whatsnew\Helpers\Finder::getBasedOnPeriodAndCategory($period, $category, $limit);

		// encode results and return response
		$object = new stdClass();
		$object->whatsnew = $whatsnew;

		$this->send($object);
	}

	/**
	 * A simple search on title and id for use with autocomplete
	 *
	 * @apiMethod GET
	 * @apiUri    /resources/autocomplete
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "search",
	 * 		"description":   "Term to search resource id or title",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "existingCids",
	 * 		"description":   "List of Resource IDs to exclude from the search",
	 * 		"type":          "array",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @return  void
	 */
	public function autocompleteTask()
	{
		$limit    = Request::getInt('limit', 25);
		$search   = Request::getString('search', '');
		$existingCids = Request::getArray('existingCids');

		require_once Component::path('com_resources') . DS . 'models' . DS . 'entry.php';
		$response = new stdClass;

		$query = Entry::all();
		if (!empty($search))
		{
			if (is_numeric($search))
			{
				$query->whereEquals('id', $search);
			}
			else
			{
				$query->where('title', 'LIKE', '%' . $search . '%', 'OR');
			}
		}

		if (!empty($existingCids))
		{
			$query->where('id', 'NOT IN', $existingCids);
		}
		$response->records = $query
			->select('id, title')
			->whereEquals('standalone', 1)
			->paginated()
			->rows()->toArray();
		$response->success = true;
		$this->send($response);
	}

	/**
	 * Render LaTeX expression
	 *
	 * @apiMethod GET
	 * @apiUri    /resources/renderlatex
	 * @apiParameter {
	 * 		"name":          "expression",
	 * 		"description":   "LaTeX expression",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       ""
	 * }
	 * @return    void
	 */
	public function renderlatexTask()
	{
		$expression = Request::getString('expression', '');

		$dir = PATH_APP . DS . 'cache' . DS . 'ckeditor' . DS . 'hubzeroequation' . DS;
		$filename = uniqid('equation_');
		$error = null;

		// build tex document
		$doc  = '\documentclass[12pt]{article}'."\n";
		$doc .= '\usepackage[utf8]{inputenc}'."\n";
		$doc .= '\usepackage{amssymb,amsmath}'."\n";
		$doc .= '\usepackage{color}'."\n";
		$doc .= '\usepackage{amsfonts}'."\n";
		$doc .= '\usepackage{amssymb}'."\n";
		$doc .= '\usepackage{pst-plot}'."\n";
		$doc .= '\begin{document}'."\n";
		$doc .= '\pagestyle{empty}'."\n";
		$doc .= '\begin{displaymath}'."\n";
		$doc .= $expression."\n";
		$doc .= '\end{displaymath}'."\n";
		$doc .= '\end{document}'."\n";

		// if cache doesn't exist, create it
		if (!is_dir($dir))
		{
			\Filesystem::makeDirectory($dir);
		}

		if (file_put_contents($dir . DS . $filename . '.tex', $doc) === false)
		{
			throw new Exception('Failed to open target file');
		}

		try
		{
			// execute latex to build dvi
			$command = 'cd ' . $dir . '; /usr/bin/latex ' . $filename . '.tex < /dev/null |grep ^!|grep -v Emergency > ' . $dir . DS . $filename . '.error 2> /dev/null 2>&1';
			exec($command, $output_lines, $exit_status);

			// execute dvi2png to build png
			$command = "/usr/bin/dvipng -bg 'transparent' -q -T tight -D 100 -o " . $dir . DS . $filename . '.png '. $dir . DS . $filename . '.dvi 2>&1';
			exec($command, $output_lines, $exit_status);

			if ($exit_status != 0)
			{
				throw new Exception("dvi2png failed");
			}
		}
		catch (Exception $e)
		{
			$error = $e->getMessage();
		}

		// build response
		$object = new stdClass();

		if ($error)
		{
			$object->error = $error;
			$object->img = 'data:image/png;base64,';
		}
		else
		{
			// no errors - send base64 encoded image
			$object->error = "";
			$imgbinary = fread(fopen($dir . DS . $filename . '.png', 'r'), filesize($dir . DS . $filename .'.png'));
			$base64img = 'data:image/png;base64,'.base64_encode($imgbinary);
			$object->img = $base64img;
		}

		$object->expression = $expression;

		// clean up our cache mess
		shell_exec('rm ' . $dir . $filename . '.*');

		$this->send($object);
	}
}
