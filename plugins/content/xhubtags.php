<?php
/**
 * @package     hubzero-cms
 * @author      Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$mainframe->registerEvent( 'onPrepareContent', 'plgContentXHubTags' );

function plgContentXHubTags( &$row, &$params, $page=0 ) 
{
	// expression to search for
	$regex = "/\{xhub:\s*[^\}]*\}/i";
    
	if (!is_object($params)) // weblinks is somehow calling this with null params
		return false;

	// check whether plugin has been unpublished
	if ( !$params->get( 'enabled', 1 ) ) {
		$row->text = preg_replace( $regex, '', $row->text );

		return true;
	}

	// find all instances of plugin and put in $matches
	$count = preg_match_all( $regex, $row->text, $matches );

	if ( $count )
		plgContentXHubTagsProcess( $row, $matches, $count, $regex);
}

function plgContentXHubTagsProcess( &$row, &$matches, $count, $regex )
{
	for ( $i=0; $i < $count; $i++ )
	{
		$regex = "/\{xhub:\s*([^\s]+)\s*(.*)/i";
		if ( preg_match($regex, $matches[0][$i], $tag) ) 
		{
			if ($tag[1] == "include")
				$text = plgContentXHubTagsInclude($tag[2]);
			else if ($tag[1] == "image")
				$text = plgContentXHubTagsImage($tag[2]);
			else if ($tag[1] == "module")
				$text = plgContentXHubTagsModules($tag[2]);
			else if (($tag[1] == 'templatedir') || ($tag[1] == 'templatedir}'))
				$text = plgContentXHubTagsTemplateDir();
			else if (($tag[1] == 'getCfg') || ($tag[1] == 'getcfg'))
				$text = plgContentXhubTagsGetCfg($tag[2]);
			else
				$text = "";

			$row->text = str_replace($matches[0][$i], $text, $row->text);
		}
	}
}

/*
 * {xhub:module position="position" style="style"}
 */

function plgContentXHubTagsModules($options)
{
    global $mainframe;

    $regex = "/position\s*=\s*(\"|&quot;)([^\"]+)(\"|&quot;)/i";
    
	if (!preg_match($regex, $options, $position))
        return "";

    $regex = "/style\s*=\s*(\"|&quot;)([^\"]+)(\"|&quot;)/i";

	if (!preg_match($regex, $options, $style))
        $style[2] = "-2";

    ximport('Hubzero_Module_Helper');

    return Hubzero_Module_Helper::renderModules($position[2],$style[2]);
}

/*
 * {xhub:templatedir}
 *
 */

function plgContentXhubTagsTemplateDir()
{
	global $mainframe;

	$template = $mainframe->getTemplate();
	return "/templates/$template";
}

/*
 * {xhub:include type="script" component="component" filename="filename"}
 * {xhub:include type="stylesheet" component="component" filename="filename"}
 */

function plgContentXHubTagsInclude($options)
{
	global $mainframe;

	$regex = "/type\s*=\s*(\"|&quot;)(script|stylesheet)(\"|&quot;)/i";

	if (!preg_match($regex, $options, $type))
		return "";

	$regex = "/filename\s*=\s*(\"|&quot;)([^\"&]+)(\"|&quot;)/i";
    
	if (!preg_match($regex, $options, $file))
		return "";

	$regex = "/component\s*=\s*(\"|&quot;)([^\"&]+)(\"|&quot;)/i";
	
	$template = $mainframe->getTemplate();

	if ($file[2][0] == '/')
		$filename = $file[2];
	else if (preg_match($regex, $options, $component))  {
		$filename = 'templates/' . $template . '/html/' . $component[2] . '/' . $file[2];
		if (!file_exists(JPATH_SITE . DS . $filename))
			$filename  = 'components/' . $component[2] . '/' . $file[2];
		$filename = DS.$filename;
		//$filename = JURI::base() . $filename;
	}
	else
	{
		//$filename = JURI::base(). "templates/$template/";
		// Removed JURI::base() because it would add http:// to files even 
		// when the site is https:// thus causing warnings in browsers
		$filename = "/templates/$template/";
		if ($type[2] == 'script')
			$filename .= 'js/';
		else
			$filename .= 'css/';
		$filename .= $file[2];
	}

	$document = &JFactory::getDocument();

	if ($type[2] == "script")
		$document->addScript($filename);
	else if ($type[2] == "stylesheet")
		$document->addStyleSheet($filename,"text/css","screen");

	return "";
}

/* {xhub:image component="component" filename="filename"} */

function plgContentXHubTagsImage($options)
{
	global $mainframe;

	$regex = "/filename\s*=\s*(\"|&quot;)([^\"&]+)(\"|&quot;)/i";

	if (!preg_match($regex, $options, $file))
	                return "";

	$regex = "/component\s*=\s*(\"|&quot;)([^\"&]+)(\"|&quot;)/i";

	if (!preg_match($regex, $options, $component))
	{
		$regex = "/module\s*=\s*(\"|&quot;)([^\"&]+)(\"|&quot;)/i";

		preg_match($regex, $options, $module);
	}

        ximport('Hubzero_Document');
	$template = $mainframe->getTemplate();
	if (empty($component) && empty($module))
		return substr(Hubzero_Document::getHubImage($file[2]),1);
	else if (!empty($component))
		return substr(Hubzero_Document::getComponentImage($component[2], $file[2]),1);
	else if (!empty($module))
		return substr(Hubzero_Dcoument::getModuleImage($module[2],$file[2]),1);
	
	return "";
}

/* {xhub:getcfg variable} */

function plgContentXhubTagsGetCfg($options)
{
	$options = trim($options," \n\t\r}");

	$xhub =& Hubzero_Factory::getHub();

	return $xhub->getCfg($options);
}
		
?>
