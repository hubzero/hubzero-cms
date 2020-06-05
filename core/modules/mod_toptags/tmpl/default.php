<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$exclude = explode(',', $this->params->get('exclude', ''));
$exclude = array_map('trim', $exclude);

$tl = array();
if ($this->tags->count() > 0)
{
	$html  = '<ol class="tags">' . "\n";
	foreach ($this->tags as $tag)
	{
		if (!in_array($tag->get('raw_tag'), $exclude))
		{
			$tl[$tag->get('tag')] = "\t" . '<li><a class="tag" href="' . Route::url('index.php?option=com_tags&tag=' . $this->escape($tag->get('tag'))) . '">' . $this->escape($tag->get('raw_tag')) . '</a></li>';
		}
	}
	if ($this->params->get('sortby') == 'alphabeta')
	{
		ksort($tl);
	}
	$html .= implode("\n", $tl);
	$html .= '</ol>' . "\n";
	if ($this->params->get('morelnk'))
	{
		$html .= '<p class="more"><a href="' . Route::url('index.php?option=com_tags') . '">' . Lang::txt('MOD_TOPTAGS_MORE') . '</a></p>' . "\n";
	}
}
else
{
	$html  = '<p>' . $this->params->get('message', 'No tags found.') . '</p>' . "\n";
}
echo $html;
