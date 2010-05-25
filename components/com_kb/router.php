<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

function kbBuildRoute(&$query)
{
	$segments = array();

	if (isset($query['task']))
	{
		if ($query['task'] == 'article')
			unset($query['task']);
	}

	if (isset($query['section']))
	{
		if (!empty($query['section']))
			$segments[] = $query['section'];

		unset($query['section']);
	}

	if (isset($query['category']))
	{
		if (!empty($query['category']))
			$segments[] = $query['category'];

		unset($query['category']);
	}

	if (isset($query['alias'])) {
		if (!empty($query['alias']))
			$segments[] = $query['alias'];

		unset($query['alias']);
	}

	if (isset($query['id'])) {
		if (!empty($query['id']))
			$segments[] = $query['id'];

		unset($query['id']);
	}

	return $segments;
}

function kbParseRoute($segments)
{
	$vars  = array();
	
	$vars['task'] = 'categories';

	if (empty($segments[0]))
		return $vars;

	$count = count($segments);
	
	// section/
	if ($count == 1) {
		$vars['task'] = 'category';
		$vars['alias'] = urldecode($segments[0]);
		$vars['alias'] = str_replace(':','-',$vars['alias']);
	} else if ($count == 2) {
		$title1 = urldecode($segments[0]);
		$title1 = str_replace(':','-',$title1);
		$title2 = urldecode($segments[1]);
		$title2 = str_replace(':','-',$title2);

		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_kb'.DS.'tables'.DS.'kb.article.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_kb'.DS.'tables'.DS.'kb.category.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_kb'.DS.'tables'.DS.'kb.helpful.php' );
		$db =& JFactory::getDBO();

		$category = new KbCategory( $db );
		$category->loadAlias( $title2 );
		
		if ($category->id) {
			// section/category
			$vars['task'] = 'category';
			$vars['alias'] = $title2; //urldecode($segments[1]);
			return $vars;
		} else {
			$category->loadAlias( $title1 );
		}

		$article = new KbArticle( $db );
		$article->loadAlias( $title2, $category->id );
		
		if ($article->id) {
			// section/article
			$vars['id'] = $article->id;
			$vars['task'] = 'article';
			//$vars['alias'] = $title2; //urldecode($segments[1]);
		}
	} else if ($count == 3) {
		// section/category/article
		$vars['task'] = 'article';
		$vars['alias'] = urldecode($segments[2]);
		$vars['alias'] = str_replace(':','-',$vars['alias']);
	}

	return $vars;
}
