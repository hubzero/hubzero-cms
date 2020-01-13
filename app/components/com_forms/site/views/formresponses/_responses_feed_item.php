<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$item = $this->item;
$itemAction = $item->get('action');

switch ($itemAction)
{
	case 'activity':
		$this->view('_responses_feed_activity')
			->set('item', $item)
			->display();
		break;
	case 'comment':
		$this->view('_responses_feed_comment')
			->set('item', $item)
			->display();
		break;
}
