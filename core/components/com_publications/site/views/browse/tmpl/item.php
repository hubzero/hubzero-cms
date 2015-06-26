<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// No direct access
defined('_HZEXEC_') or die();

$html  = "\t".'<li';
switch ($this->line->get('master_access'))
{
	case 1: $html .= ' class="registered"'; break;
	case 2: $html .= ' class="protected"'; break;
	case 3: $html .= ' class="private"'; break;
	case 0:
	default: $html .= ' class="public"'; break;
}
$html .= '>' . "\n";
$html .= "\t". "\t". '<div class="pub-thumb"><img src="' . Route::url($this->line->link('thumb')) . '" alt=""/></div>' . "\n";
$html .= "\t" . "\t" . '<div class="pub-details">' . "\n";
$html .= "\t\t".'<p class="title"><a href="' . Route::url($this->line->link()) . '">' .  $this->escape($this->line->title) . '</a>' . "\n";
$html .= '</p>' . "\n";

if ($this->params->get('show_ranking') && $this->config->get('show_ranking'))
{
	$database = \App::get('db');

	$ranking = round($this->line->get('master_ranking'), 1);

	$r = (10*$ranking);
	if (intval($r) < 10)
	{
		$r = '0' . $r;
	}

	$html .= "\t\t" . '<div class="metadata">' . "\n";
	$html .= "\t\t\t" . '<dl class="rankinfo">' . "\n";
	$html .= "\t\t\t\t" . '<dt class="ranking"><span class="rank-' . $r . '">' . Lang::txt('COM_PUBLICATIONS_THIS_HAS') . '</span> ' . number_format($ranking, 1) . ' '.Lang::txt('COM_PUBLICATIONS_RANKING') . '</dt>' . "\n";
	$html .= "\t\t\t\t".'<dd>' . "\n";
	$html .= "\t\t\t\t\t" . '<p>' . Lang::txt('COM_PUBLICATIONS_RANKING_EXPLANATION') . '</p>' . "\n";
	$html .= "\t\t\t\t\t" . '<div>' . "\n";
	$html .= "\t\t\t\t\t" . '</div>' . "\n";
	$html .= "\t\t\t\t" . '</dd>' . "\n";
	$html .= "\t\t\t" . '</dl>' . "\n";
	$html .= "\t\t" . '</div>' . "\n";
} elseif ($this->params->get('show_rating') && $this->config->get('show_rating')) {
	switch ($this->line->get('master_rating'))
	{
		case 0.5: $class = ' half-stars';      break;
		case 1:   $class = ' one-stars';       break;
		case 1.5: $class = ' onehalf-stars';   break;
		case 2:   $class = ' two-stars';       break;
		case 2.5: $class = ' twohalf-stars';   break;
		case 3:   $class = ' three-stars';     break;
		case 3.5: $class = ' threehalf-stars'; break;
		case 4:   $class = ' four-stars';      break;
		case 4.5: $class = ' fourhalf-stars';  break;
		case 5:   $class = ' five-stars';      break;
		case 0:
		default:  $class = ' no-stars';        break;
	}

	if ($this->line->get('master_rating') > 5)
	{
		$class = ' five-stars';
	}

	$html .= "\t\t" . '<div class="metadata">' . "\n";
	$html .= "\t\t\t" . '<p class="rating"><span title="' . Lang::txt('COM_PUBLICATIONS_OUT_OF_5_STARS', $this->line->get('master_rating')) . '" class="avgrating' . $class . '"><span>' . Lang::txt('COM_PUBLICATIONS_OUT_OF_5_STARS', $this->line->get('master_rating')) . '</span>&nbsp;</span></p>' . "\n";
	$html .= "\t\t" .'</div>' . "\n";
}

$info = array();
if ($this->thedate)
{
	$info[] = $this->thedate;
}

if (($this->line->category && !intval($this->filters['category'])))
{
	$info[] = $this->line->cat_name;
}
if ($this->authors && $this->params->get('show_authors'))
{
	$info[] = Lang::txt('COM_PUBLICATIONS_CONTRIBUTORS') . ': ' . \Components\Publications\Helpers\Html::showContributors( $this->authors, false, true );
}
if ($this->line->doi)
{
	$info[] = 'doi:' . $this->line->doi;
}

$html .= "\t\t" . '<p class="details">' . implode(' <span>|</span> ', $info) . '</p>' . "\n";
if ($this->line->get('abstract')) {
	$html .= "\t\t" . \Hubzero\Utility\String::truncate( stripslashes($this->line->get('abstract')), 300 ) . "\n";
} else if ($this->line->get('description')) {
	$html .= "\t\t" . \Hubzero\Utility\String::truncate( stripslashes($this->line->get('description')), 300 ) . "\n";
}
$html .= "\t" . "\t" . '</div>' . "\n";
$html .= "\t" . '</li>' . "\n";
echo $html;
?>