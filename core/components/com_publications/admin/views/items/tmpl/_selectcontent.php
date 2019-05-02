<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$pub = $this->pub;
$database = App::get('db');

if (!$pub->_attachments)
{
	return '<p class="notice">' . Lang::txt('COM_PUBLICATIONS_NO_CONTENT') . '</p>';
}
$html   = '';
$prime  = $pub->_attachments[1];
$second = $pub->_attachments[2];

if (isset($pub->_curationModel))
{
	$prime    = $pub->_curationModel->getElements(1);
	$second   = $pub->_curationModel->getElements(2);
	$gallery  = $pub->_curationModel->getElements(3);

	// Get attachment type model
	$attModel = new \Components\Publications\Models\Attachments($database);

	foreach ($prime as $i => $elm)
	{
		$prime[$i]->manifest->params->typeParams->multiZip = 0;
	}

	foreach ($second as $i => $elm)
	{
		$second[$i]->manifest->params->typeParams->multiZip = 0;
	}

	foreach ($gallery as $i => $elm)
	{
		$gallery[$i]->manifest->params->typeParams->multiZip = 0;
	}

	// Draw list of primary elements
	$html .= '<h5>' . Lang::txt('COM_PUBLICATIONS_PRIMARY_CONTENT') . '</h5>';
	$list  = $attModel->listItems(
		$prime,
		$pub,
		'administrator'
	);
	$html .= $list ? $list : '<p class="notice">' . Lang::txt('COM_PUBLICATIONS_NO_CONTENT') . '</p>';

	// Draw list of secondary elements
	$html .= '<h5>' . Lang::txt('COM_PUBLICATIONS_SUPPORTING_CONTENT') . '</h5>';
	$list  = $attModel->listItems(
		$second,
		$pub,
		'administrator'
	);
	$html .= $list ? $list : '<p class="notice">' . Lang::txt('COM_PUBLICATIONS_NO_CONTENT') . '</p>';

	// Draw list of gallery elements
	$html .= '<h5>' . Lang::txt('COM_PUBLICATIONS_GALLERY') . '</h5>';
	$list  = $attModel->listItems(
		$gallery,
		$pub,
		'administrator'
	);
	$html .= $list ? $list : '<p class="notice">' . Lang::txt('COM_PUBLICATIONS_NO_CONTENT') . '</p>';
}
else
{
	$html .= '<h5>' . Lang::txt('COM_PUBLICATIONS_PRIMARY_CONTENT') . '</h5>';
	if ($prime)
	{
		$html .= '<ul class="content-list">';
		foreach ($prime as $att)
		{
			$type = $att->type;
			if ($att->type == 'file')
			{
				$ext  = explode('.', $att->path);
				$type = strtoupper(end($ext));
			}
			$title = $att->title ? $att->title : $att->path;
			$html .= '<li>(' . $type . ') ';
			$html .= $att->title ? $att->title : $att->path;
			$html .= $att->title != $att->path ? '<br /><span class="ctitle">' . $att->path . '</span>' : '';
			$html .= '</li>'."\n";
		}
		$html .= '</ul>';
	}
	else
	{
		$html .= '<p class="notice">' . Lang::txt('COM_PUBLICATIONS_NO_CONTENT') . '</p>';
	}
	$html .= '<h5>' . Lang::txt('COM_PUBLICATIONS_SUPPORTING_CONTENT') . '</h5>';
	if ($second)
	{
		$html .= '<ul class="content-list">';
		foreach ($second as $att)
		{
			$type = $att->type;
			if ($att->type == 'file')
			{
				$ext  = explode('.', $att->path);
				$type = strtoupper(end($ext));
			}
			$title = $att->title ? $att->title : $att->path;
			$html .= '<li>(' . $type . ') ';
			$html .= $att->title ? $att->title : $att->path;
			$html .= $att->title != $att->path ? '<br /><span class="ctitle">' . $att->path.'</span>' : '';
			$html .= '</li>' . "\n";
		}
		$html .= '</ul>';
	}
	else
	{
		$html .= '<p class="notice">' . Lang::txt('COM_PUBLICATIONS_NO_CONTENT') . '</p>';
	}
}

echo $html;
