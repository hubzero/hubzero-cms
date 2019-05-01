<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$authIDs = array();
$html = '';
$i = 1;
$option = $this->option;

if ($this->authNames != null)
{
	$html = '<ul id="author-list">'."\n";
	foreach ($this->authNames as $authname)
	{
		$authIDs[] = $authname->id;
		$name = $authname->name;

		$org = ($authname->organization)
			? $this->escape($authname->organization) : '';
		$credit = ($authname->credit)
			? $this->escape($authname->credit) : '';
		$userid = $authname->user_id ? $authname->user_id : 'unregistered';

		$html .= "\t".'<li id="author_'.$authname->id.'" class="pick reorder">'
			. '<span class="ordernum">' . $i . '</span>. ' . $name . ' (' . $userid . ')';
		$html .= $org ? ' - <span class="org">' . $org . '</span>' : '';
		$html .= ' <a class="editauthor" href="' . Route::url('index.php?option=' . $option . '&controller=items&task=editauthor&author=' . $authname->id) . '" >' . Lang::txt('COM_PUBLICATIONS_EDIT') . '</a> ';
		$html .= ' <a class="editauthor" href="' . Route::url('index.php?option=' . $option . '&controller=items&task=deleteauthor&aid=' . $authname->id) .'"  > ' . Lang::txt('COM_PUBLICATIONS_DELETE') . '</a> ';
		if ($credit)
		{
			$html .= '<br />' . Lang::txt('COM_PUBLICATIONS_CREDIT') . ': ' . $credit;
		}
		$html .= '</li>' . "\n";
		$i++;
	}
	$html.= '</ul>';
}
else
{
	$html.= '<p class="notice">' . Lang::txt('COM_PUBLICATIONS_NO_AUTHORS') . '</p>';
}
if (count($this->authNames) > 1)
{
	$html.= '<input type="hidden" value="" name="list" id="neworder" />';
	$html.= '<p class="tip">' . Lang::txt('COM_PUBLICATIONS_AUTHORS_REORDER_TIP') . '</p>';
	$html.= '<input type="button" onclick="submitbutton(\'saveorder\');" class="btn" value="Save Order" id="saveorder" />';
}

echo $html;
