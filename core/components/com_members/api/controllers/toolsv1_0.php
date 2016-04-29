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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Api\Controllers;

use Hubzero\Component\ApiController;
use Component;
use Exception;
use stdClass;
use Request;
use Route;
use Lang;
use App;

/**
 * Members API controller class
 */
class Toolsv1_0 extends ApiController
{
	/**
	 * Get a member's tool sessions
	 *
	 * @apiMethod GET
	 * @apiUri    /members/{id}/tools/sessions
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Member identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return  void
	 */
	public function sessionsTask()
	{
		$this->requiresAuthentication();

		$userid = Request::getInt('id', 0);
		$result = User::getInstance($userid);

		if ($result === false)
		{
			throw new Exception(Lang::txt('COM_MEMBERS_ERROR_USER_NOT_FOUND'), 404);
		}

		// Include middleware utilities
		include_once(Component::path('com_tools') . DS . 'helpers' . DS . 'utils.php');
		include_once(Component::path('com_tools') . DS . 'tables' . DS . 'session.php');

		// Get db connection
		$db = \App::get('db');

		// Get Middleware DB connection
		$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

		// Get com_tools params
		$mconfig = Component::params('com_tools');

		// Check to make sure we have a connection to the middleware and its on
		if (!$mwdb || !$mconfig->get('mw_on') || $mconfig->get('mw_on') > 1)
		{
			throw new Exception(Lang::txt('Middleware Service Unavailable'), 503);
		}

		// Get request vars
		$format = Request::getVar('format', 'json');
		$order  = Request::getVar('order', 'id_asc' );

		// Get my sessions
		$ms = new \Components\Tools\Tables\Session($mwdb);
		$sessions = $ms->getRecords($result->get("username"), '', false);

		// Run middleware command to create screenshots
		$cmd = "/bin/sh ". Component::path('com_tools') . "/scripts/mw screenshot " . $result->get('username') . " 2>&1 </dev/null";
		exec($cmd, $results, $status);

		$results = array();
		foreach ($sessions as $session)
		{
			$r = array(
				'id'         => $session->sessnum,
				'app'        => $session->appname,
				'name'       => $session->sessname,
				'started'    => $session->start,
				'accessed'   => $session->accesstime,
				'owner'      => ($result->get('username') == $session->username) ? 1 : 0,
				'ready-only' => ($session->readonly == 'No') ? 0 : 1
			);
			$results[] = $r;
		}

		// Make sure we have an acceptable ordering
		$accepted_ordering = array('id_asc', 'id_desc', 'started_asc', 'started_desc', 'accessed_asc', 'accessed_desc');
		if (in_array($order, $accepted_ordering))
		{
			switch ($order)
			{
				case 'id_asc':
					break;
				case 'id_desc':
					usort($results, array($this, "id_sort_desc"));
					break;
				case 'started_asc':
					break;
				case 'started_desc':
					usort($results, array($this, "started_date_sort_desc"));
					break;
				case 'accessed_asc':
					usort($results, array($this, "accessed_date_sort_asc"));
					break;
				case 'accessed_desc':
					usort($results, array($this, "accessed_date_sort_desc"));
					break;
			}
		}

		// Encode sessions for return
		$object = new stdClass();
		$object->sessions = $results;

		// Set format and content
		$this->send($object);
	}

	/**
	 * Sort by ID DESC
	 *
	 * @param   array $a
	 * @param   array $b
	 * @return  array
	 */
	private function id_sort_desc($a, $b)
	{
		return $a['id'] < $b['id'] ? 1 : -1;
	}

	/**
	 * Sort by started date DESC
	 *
	 * @param   array $a
	 * @param   array $b
	 * @return  array
	 */
	private function started_date_sort_desc($a, $b)
	{
		return (strtotime($a['started']) < strtotime($b['started'])) ? 1 : -1;
	}

	/**
	 * Sort by accessed date ASC
	 *
	 * @param   array $a
	 * @param   array $b
	 * @return  array
	 */
	private function accessed_date_sort_asc($a, $b)
	{
		return (strtotime($a['accessed']) < strtotime($b['accessed'])) ? -1 : 1;
	}

	/**
	 * Sort by accessed date DESC
	 *
	 * @param   array $a
	 * @param   array $b
	 * @return  array
	 */
	private function accessed_date_sort_desc($a, $b)
	{
		return (strtotime($a['accessed']) < strtotime($b['accessed'])) ? 1 : -1;
	}

	/**
	 * Get recent tools for a user
	 *
	 * @apiMethod GET
	 * @apiUri    /members/{id}/tools/recent
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Member identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return  void
	 */
	public function recenttoolsTask()
	{
		$this->requiresAuthentication();

		$userid = Request::getInt('id', 0);
		$result = User::getInstance($userid);

		if ($result === false)
		{
			throw new Exception(Lang::txt('COM_MEMBERS_ERROR_USER_NOT_FOUND'), 404);
		}

		// Load database object
		$database = \App::get('db');

		// Get the supported tag
		$rconfig = Component::params('com_resources');
		$supportedtag = $rconfig->get('supportedtag', '');

		// Get supportedtag usage
		include_once(Component::path('com_resources') . DS . 'helpers' . DS . 'tags.php');
		$this->rt = new \Components\Resources\Helpers\Tags(0);
		$supportedtagusage = $this->rt->getTagUsage($supportedtag, 'alias');

		// Load users recent tools
		$sql = "SELECT r.alias, tv.toolname, tv.title, tv.description, tv.toolaccess as access, tv.mw, tv.instance, tv.revision
				FROM `#__resources` as r, `#__recent_tools` as rt, `#__tool_version` as tv
				WHERE r.published=1
				AND r.type=7
				AND r.standalone=1
				AND r.access!=4
				AND r.alias=tv.toolname
				AND tv.state=1
				AND rt.uid={$result->get("uidNumber")}
				AND rt.tool=r.alias
				GROUP BY r.alias
				ORDER BY rt.created DESC";

		$database->setQuery($sql);
		$recent_tools = $database->loadObjectList();

		$r = array();
		foreach ($recent_tools as $k => $recent)
		{
			$r[$k]['alias'] = $recent->alias;
			$r[$k]['title'] = $recent->title;
			$r[$k]['description'] = $recent->description;
			$r[$k]['version'] = $recent->revision;
			$r[$k]['supported'] = (in_array($recent->alias, $supportedtagusage)) ? 1 : 0;
		}

		// Encode sessions for return
		$object = new stdClass();
		$object->recenttools = $r;

		$this->send($object);
	}

	/**
	 * Get a resource based on tool name
	 *
	 * @apiMethod GET
	 * @apiUri    /members/{id}/tools/diskusage
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Member identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return  void
	 */
	public function diskusageTask()
	{
		$this->requiresAuthentication();

		$userid = App::get('authn')['user_id'];
		$result = User::getInstance($userid);

		if ($result === false)
		{
			throw new Exception(Lang::txt('COM_MEMBERS_ERROR_USER_NOT_FOUND'), 404);
		}

		require_once Component::path('com_tools') . DS . 'helpers' . DS . 'utils.php';

		$du = \Components\Tools\Helpers\Utils::getDiskUsage($result->get('username'));
		if (count($du) <=1)
		{
			// Error
			$percent = 0;
		}
		else
		{
			bcscale(6);
			$val = (isset($du['softspace']) && $du['softspace'] != 0) ? bcdiv($du['space'], $du['softspace']) : 0;
			$percent = round($val * 100);
		}

		$amt = ($percent > 100) ? '100' : $percent;
		$total = (isset($du['softspace'])) ? $du['softspace'] / 1024000000 : 0;

		// Encode sessions for return
		$object = new stdClass();
		$object->amount = $amt;
		$object->total  = $total;

		$this->send($object);
	}
}