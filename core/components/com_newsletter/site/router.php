<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Site;

use Hubzero\Component\Router\Base;

/**
 * Routing class for the component
 */
class Router extends Base
{
	// Accommodate specific controllers used by com_reply
	// so is there a dash or not?
	private	$replyControllers = ['email-subscriptions', 'emailsubscriptions', 'pages', 'replies'];
	/**
	 * Build the route for the component.
	 *
	 * @param   array  &$query  An array of URL arguments
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 */
	public function build(&$query)
	{
		$segments = array();
		$isReply = false;

		// Accommodate specifics from com_reply
		$replyTasks = ['display', 'update', 'create'];

		// com_reply query params will almost always specify a controller, and
		// 		if no reply task is specified, that task will not be picked up
		// Except: if all instructions are already in the URL, then nothing to build
		if (!empty($query['controller']))
		{
			if (in_array($query['controller'], $this->replyControllers))
			{
				// we have a reply controller:
				$isReply = true;
				$segments[] = $query['controller'];
				unset($query['controller']);

				// if 'task' and a reply controller:
				if (!empty($query['task']))
				{
					// is it an expected reply task?
					if (in_array($query['task'], $replyTasks))
					{
						$segments[] = $query['task'];
						unset($query['task']);
					}
				}
				// if 'id' and a reply controller:
				if (!empty($query['id']))
				{
					$segments[] = $query['id'];
					unset($query['id']);
				}
			}
		}

		// straight com_newsletter logic:
		// here we need to fetch the alias from the database, using the query id:
		if (!empty($query['id']))
		{
			$database = \App::get('db');
			$sql = "SELECT `alias` FROM `#__newsletters` WHERE `id`=" . $database->quote( $query['id'] );
			$database->setQuery($sql);
			$campaign = $database->loadResult();
			$segments[] = strtolower(str_replace(" ", "", $campaign));
			unset($query['id']);
		}

		// com_newsletter tasks:
		if (!empty($query['task']))
		{
			if (in_array($query['task'], array('subscribe', 'unsubscribe', 'resendconfirmation')))
			{
				$segments[] = $query['task'];
				unset($query['task']);
			}
		}

		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 * @return  array  The URL attributes to be used by the application.
	 */
	public function parse(&$segments)
	{
		$vars = array();

		$isReply = false;

		if (empty($segments))
		{
			return $vars;
		}

		if (isset($segments[0]))
		{
			// is it a reply controller?
			if (in_array($segments[0], $this->replyControllers))
			{
				// Yes, it's a reply controller:
				$vars['controller'] = $segments[0];
				$isReply = true;

				// obtain the reply task or id:
				if (isset($segments[1]))
				{
					if (is_numeric($segments[1]))
					{
						$vars['id'] = $segments[1];
					}
					else
					{
						$vars['task'] = $segments[1];
					}
				}
				// obtain the reply id:
				if (isset($segments[2]))
				{
					$vars['id'] = $segments[2];
				}

			} else {
				// handle as com_newsletter
				// Determine the alias from passed id, if possible:
				$database = \App::get('db');
				$sql = "SELECT `id` FROM `#__newsletters` WHERE `alias`=" . $database->quote($segments[0]);
				$database->setQuery($sql);
				$campaignId = $database->loadResult();

				if ($campaignId)
				{
					$vars['id'] = $campaignId;
				}
				else
				{
					switch ($segments[0])
					{
						case 'track':
							$vars['task'] = 'track';
							$vars['type'] = $segments[1];
							$vars['controller'] = 'mailings';
							break;
						case 'confirm':
						case 'remove':
						case 'subscribe':
						case 'dosubscribe':
						case 'unsubscribe':
						case 'dounsubscribe':
						case 'resendconfirmation':
							$vars['task'] = $segments[0];
							$vars['controller'] = 'mailinglists';
							break;
						default:
							$vars['task'] = $segments[0];
					}
				}
			}
		}

		return $vars;
	}
}
