<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$no_html = Request::getInt('no_html', 0);

$assets = $this->model->attachments('list', array(
	'ticket'  => $this->ticket,
	'comment_id' => $this->comment
));
if ($assets->total() > 0)
{
	$i = 0;
	foreach ($assets as $asset)
	{
		$this->view('_asset')
		     ->set('i', $i)
		     ->set('option', $this->option)
		     ->set('controller', $this->controller)
		     ->set('asset', $asset)
		     ->set('no_html', $no_html)
		     ->display();

		$i++;
	}
}
