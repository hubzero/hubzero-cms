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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$juri = JURI::getInstance();
$jconfig = JFactory::getConfig();
$ih = new MembersImgHandler();

$dateFormat = 'M d, Y';

$baseManage = 'publications/submit';
$baseView = 'publications';

$base = trim(preg_replace('/\/administrator/', '', $juri->base()), DS);

$mconfig = JComponentHelper::getParams( 'com_members' );
$pPath   = trim($mconfig->get('webpath'), DS);
$profileThumb = NULL;

$append = '?from=' . $this->juser->get('email');
$lastMonth = date('M Y', strtotime("-1 month"));

$message  = 'Here is the monthly update on your recent publications usage' . "\n";
$message .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'."\n\n";

for ($a = 0; $a < count($this->pubstats); $a++)
{
	// Check against limit
	if ($a >= $this->limit)
	{
		break;
	}

	$stat = $this->pubstats[$a];

	$sefManage 	= $baseManage . DS . $stat->publication_id . $append;
	$sefView 	= $baseView . DS . $stat->publication_id . $append;

	$message .= 'Publication #' . $stat->publication_id . ' "' . stripslashes($stat->title) . '"' . "\n";
	$message .= 'View publication:          ' . $base . DS . trim($sefView, DS) . "\n";
	$message .= 'Manage publication:        ' . $base . DS . trim($sefManage, DS) . "\n\n";

	$message .= 'Usage in the past month... ' . "\n";
	$message .= 'Page views:                ' . $stat->monthly_views. "\n";
	$message .= 'Downloads:  				' . $stat->monthly_primary. "\n";
	$message .= 'Total downloads to date: 	' . $stat->total_primary. "\n";

	$message .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'."\n\n";
}

$message = str_replace('<br />', '', $message);
$message = preg_replace('/\n{3,}/', "\n\n", $message);

echo $message;
