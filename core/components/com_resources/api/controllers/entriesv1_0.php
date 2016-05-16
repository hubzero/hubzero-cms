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

use Components\Resources\Tables\Resource;
use Components\Resources\Tables\Type;
use Hubzero\Component\ApiController;
use Component;
use Exception;
use stdClass;
use Request;
use App;

/**
 * API controller class for resources
 */
class Entriesv1_0 extends ApiController
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
			'type'   => Request::getVar('type', ''),
			'sortby' => Request::getCmd('sortby', 'date'),
			'limit'  => Request::getInt('limit', Config::get('list_limit')),
			'start'  => Request::getInt('limitstart', 0),
			'search' => Request::getVar('search', '')
		);

		if (!in_array($filters['sortby'], array('date', 'date_published', 'date_created', 'date_modified', 'title', 'rating', 'ranking', 'random')))
		{
			App::abort(404, Lang::txt('Invalid sort value of "%s" used.', $filters['sortby']));
		}

		require_once(Component::path('com_resources') . DS . 'tables' . DS . 'resource.php');
		require_once(Component::path('com_resources') . DS . 'tables' . DS . 'type.php');

		$database = App::get('db');

		// Instantiate a resource object
		$rr = new Resource($database);

		// encode results and return response
		$response = new stdClass;
		$response->records = array();
		$response->total   = $rr->getCount($filters);

		if ($response->total)
		{
			// Get major types
			$t = new Type($database);
			$types = array();
			foreach ($t->getMajorTypes() as $type)
			{
				unset($type->params);
				unset($type->customFields);

				$types[$type->id] = $type;
			}

			$response->records = $rr->getRecords($filters);

			$base = rtrim(Request::base(), '/');

			foreach ($response->records as $i => $entry)
			{
				$entry->url = str_replace('/api', '', $base . '/' . ltrim(Route::url('index.php?option=com_resources&' . ($entry->alias ? 'alias=' . $entry->alias : 'id=' . $entry->id)), '/'));

				if (isset($types[$entry->type]))
				{
					$entry->type = $types[$entry->type];
				}

				$response->records[$i] = $entry;
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
		$limit    = Request::getVar('limit', 25);
		$period   = Request::getVar('period', 'month');
		$category = Request::getVar('category', 'resources');

		require_once(Component::path('com_whatsnew') . DS . 'helpers' . DS . 'finder.php');

		$whatsnew = \Components\Whatsnew\Helpers\Finder::getBasedOnPeriodAndCategory($period, $category, $limit);

		// encode results and return response
		$object = new stdClass();
		$object->whatsnew = $whatsnew;

		$this->send($object);
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
		$expression = Request::getVar('expression', '');

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
