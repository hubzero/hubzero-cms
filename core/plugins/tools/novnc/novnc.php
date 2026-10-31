<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Plugin class for NoVNC session rendering
 */
class plgToolsNovnc extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return some info about this plugin
	 * 
	 * @return  object
	 */
	public function onToolSessionIdentify()
	{
		$obj = new stdClass;
		$obj->name  = $this->_name;
		$obj->type  = $this->_type;
		$obj->title = Lang::txt('PLG_' . $this->_type . '_' . $this->_name . '_TITLE');

		return $obj;
	}

	/**
	 * Render a tool session
	 * 
	 * @param   object  $tool
	 * @param   object  $session
	 * @param   boolean $readOnly
	 * @return  string
	 */
	public function onToolSessionView($tool, $session, $readOnly=false)
	{
		$us = App::get('session');

		$declared = Request::getWord('viewer');
		$viewer = ($declared ? $declared : $us->get('tool_viewer'));

		if ((isset($session->rendered) && $session->rendered)
		 || ($viewer && $viewer != $this->_name))
		{
			return;
		}

		if (!$this->canRender())
		{
			return;
		}

		$session->rendered = $this->_name;

		if (!$declared)
		{
			//$us->set('tool_viewer', $this->_name);
		}

		$view = new \Hubzero\Plugin\View(array(
			'folder'  => $this->_type,
			'element' => $this->_name,
			'name'    => 'session',
			'layout'  => 'default'
		));

		return $view->set('option', Request::getCmd('option', 'com_tools'))
					->set('controller', Request::getWord('controller', 'sessions'))
					->set('output', $session)
					->set('app', $tool)
					->set('readOnly', $readOnly)
					->set('clipboard', $this->clipboardAllowed($tool))
					->set('params', $this->params)
					->loadTemplate();
	}

	/**
	 * Whether copy and paste may cross between the session and the browser
	 *
	 * Off for tools bound, by their version's host requirements, to a host
	 * listed in the clipboard_block_hostreq config: hosts nothing may be
	 * copied out of. This only stops the viewer from offering it; the
	 * host's own policy is what actually holds.
	 *
	 * @param   object   $tool
	 * @return  boolean
	 */
	protected function clipboardAllowed($tool)
	{
		$blocked = array_filter(array_map('trim', explode(',', strtolower((string) $this->params->get('clipboard_block_hostreq', '')))));

		if (!$blocked || empty($tool->name))
		{
			return true;
		}

		$db = App::get('db');
		$db->setQuery(
			"SELECT h.`hostreq` FROM `#__tool_version_hostreq` AS h
			INNER JOIN `#__tool_version` AS v ON v.`id`=h.`tool_version_id`
			WHERE v.`instance`=" . $db->quote($tool->name)
		);
		$hostreqs = array_map('trim', array_map('strtolower', (array) $db->loadColumn()));

		return !array_intersect($hostreqs, $blocked);
	}

	/**
	 * Check if the plugin can render for the provided browser
	 * 
	 * @return  boolean
	 */
	protected function canRender()
	{
		$browser = new \Hubzero\Browser\Detector();

		if ($allowed = trim($this->params->get('browsers','')))
		{
			$browsers = array();

			$allowed = str_replace("\r", '', $allowed);
			$allowed = str_replace('\n', "\n", $allowed);
			$allowed = explode("\n", $allowed);
			foreach ($allowed as $allow)
			{
				$allow = trim($allow);

				if (preg_match('/(.+?),\s+([^\s]+)\s+(\d+)\.(\d+)/i', $allow, $matches))
				{
					$req = new stdClass;
					$req->name  = strtolower(trim($matches[2]));
					$req->major = intval($matches[3]);
					$req->minor = intval($matches[4]);
					$req->os    = strtolower(trim($matches[1]));

					$browsers[] = $req;
				}
			}

			$matched = false;

			foreach ($browsers as $minimum)
			{
				if ($minimum->os != '*' && $minimum->os != strtolower($browser->platform()))
				{
					continue;
				}

				if ($minimum->name != strtolower($browser->name()))
				{
					continue;
				}

				// If we get to here, we have a matching OS and browser

				if ($minimum->major > $browser->major())
				{
					return false;
				}

				if ($minimum->major == $browser->major() && $minimum->minor > $browser->minor())
				{
					return false;
				}

				$matched = true;
			}

			if (!$matched)
			{
				return false;
			}
		}

		if ($regexes = trim($this->params->get('regexes','')))
		{
			$regexes = str_replace("\r", '', $regexes);
			$regexes = str_replace('\n', "\n", $regexes);
			$regexes = explode("\n", $regexes);
			foreach ($regexes as $disallow)
			{
				$disallow = trim($disallow);

				if (preg_match("/$disallow/i", $browser->agent(), $matches))
				{
					return false;
				}
			}
		}

		return true;
	}
}
