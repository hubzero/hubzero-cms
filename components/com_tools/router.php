<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

function ToolsBuildRoute(&$query)
{
    $segments = array();

    return $segments;
}

function ToolsParseRoute($segments)
{
	$vars = array();
    $xhub = &XFactory::getHub();

	if (empty($segments))
		return $vars;

    if ($segments[0] == 'contribute') {
        $vars['option'] = 'com_contribtool';
    }
    else if (empty($segments[1])) {
        $vars['option'] = 'com_resources';
        $vars['alias'] = $segments[0];
    }

	if (!empty($segments[1])) {
		if ($segments[1] == 'report')
        {
			$xhub->redirect('/support/tickets?find=group:app-' . $segments[0]);
        }
		else if ($segments[1] == 'forge.png')
			$vars['view'] = 'image';
	}

        return $vars;
}

?>
