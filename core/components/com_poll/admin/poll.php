<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Poll\Admin;

// Authorization check
if (!\User::authorise('core.manage', 'com_poll'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once dirname(__DIR__) . DS . 'models' . DS . 'poll.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php';
require_once __DIR__ . DS . 'controllers' . DS . 'polls.php';

// Create the controller
$controller = new Controllers\Polls();
$controller->execute();
