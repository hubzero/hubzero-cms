<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$this->css('assets/css/share.css')
     ->js('assets/js/share.js');

$jconfig = JFactory::getConfig();

$i = 1;
$limit = intval($this->_params->get('icons_limit')) ? $this->_params->get('icons_limit') : 0;

$popup = '<ol class="sharelinks">';
$title = JText::sprintf('PLG_PUBLICATION_SHARE_VIEWING',$jconfig->getValue('config.sitename'),stripslashes($this->publication->title));
$metadata  = '<div class="share">'."\n";
$metadata .= "\t".JText::_('PLG_PUBLICATION_SHARE').': ';

// Available options
$sharing = array('facebook', 'twitter', 'google', 'pinterest', 'linkedin', 'delicious', 'reddit');

foreach ($sharing as $shared)
{
	if ($this->_params->get('share_' . $shared, 1) == 1) {
		// Show activity
		$link = $this->view('_item')
	     ->set('option', $this->option)
	     ->set('publication', $this->publication)
		 ->set('name', $shared)
	     ->loadTemplate();

		$metadata .= (!$limit || $i <= $limit) ? $link : '';
		$popup 	  .= '<li class="';
		$popup 	  .= ($i % 2) ? 'odd' : 'even';
		$popup    .= '">'. $link . '</li>';
		$i++;
	}
}

// Pop up more
if ($limit > 0 && $i > $limit)
{
	$metadata .= '...';
}
$popup .= '</ol>';

// Show pop-up?
if ($limit > 0)
{
	$metadata .= '<dl class="shareinfo">'."\n";
	$metadata .= "\t".'<dd>'."\n";
	$metadata .= "\t\t".'<p>'."\n";
	$metadata .= "\t\t\t".JText::_('PLG_PUBLICATION_SHARE_RESOURCE')."\n";
	$metadata .= "\t\t".'</p>'."\n";
	$metadata .= "\t\t".'<div>'."\n";
	$metadata .= $popup;
	$metadata .= "\t\t".'</div>'."\n";
	$metadata .= "\t".'</dd>'."\n";
	$metadata .= '</dl>'."\n";
}
$metadata .= '</div>'."\n";

echo $metadata;
?>