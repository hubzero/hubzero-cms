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

//----------------------------------------------------------
//  Base Tagging class
//  
//  Generally, direct use is rare (and discouraged). It will 
//  typically be extended by another component, such as 
//  ResourcesTags or AnswersTags.
//----------------------------------------------------------

class Tags 
{
	public $_db  = NULL;  // Database
	public $_tbl = NULL;  // Secondary tag table, used for linking objects (such as resources) to tags
	public $_oid = NULL;  // The object to be tagged
	public $_tag_tbl = '#__tags';  // The primary tag table
	public $_obj_tbl = '#__tags_object';
	public $_normalized_valid_chars = 'a-zA-Z0-9';  // The regex-style set of characters that are valid for normalized tags.
	public $_normalize_tags = 1;    // Whether to normalize tags at all.
	public $_max_tag_length = 200;  // The maximum length of a tag.
	public $_block_multiuser_tag_on_object = 0;  // Whether to prevent multiple users from tagging the same object.

	//-----------

	public function get_tags_on_object($object_id, $offset=0, $limit=10, $tagger_id=NULL, $strength=0, $admin=0) 
	{
		if (!isset($object_id)) {
			return false;
		}
		
		if (isset($tagger_id) && ($tagger_id > 0)) {
			$tagger_sql = "AND rt.taggerid = $tagger_id";
		} else {
			$tagger_sql = "";
		}
		
		if ($strength == 0) {
			$strength_sql = "";
		} else {
			$strength_sql = "AND rt.strength=".$strength;
		}
		
		if ($admin == 0) {
			$admin_sql = "AND admin=0 ";
		} else {
			$admin_sql = "";
		}

		if ($limit <= 0) {
			$limit_sql = "";
		} else {
			$limit_sql = "LIMIT $offset, $limit";
		}

		$rs = array();
		$sql = "SELECT DISTINCT t.*";
		$sql .= (isset($tagger_id) && ($tagger_id > 0)) ? ", rt.taggerid" : ", NULL AS taggerid";
		$sql .= " FROM $this->_obj_tbl AS rt INNER JOIN $this->_tag_tbl AS t ON (rt.tagid = t.id)
			WHERE rt.objectid = $object_id AND rt.tbl='$this->_tbl'
			$strength_sql 
			$tagger_sql
			$admin_sql 
			ORDER BY t.raw_tag ASC
			$limit_sql
			";
		$this->_db->setQuery( $sql );
		$rs = $this->_db->loadObjectList();

		$retarr = array();
		if ($rs) {
			foreach ($rs as $r)
			{
				$retarr[] = array(
					'tag' => $r->tag,
					'raw_tag' => $r->raw_tag,
					'tagger_id' => $r->taggerid,
					'admin' => $r->admin,
					'tag_id' => $r->id
				);
			}
		}
		return $retarr;
	}

	//-----------
	
	public function safe_tag($tagger_id, $object_id, $tag, $strength=1) 
	{
		if (!isset($tagger_id) || !isset($object_id) || !isset($tag)) {
			die('safe_tag argument missing');
			return false;
		}

		$normalized_tag = $this->normalize_tag($tag);
		
		if ($normalized_tag === '0') {
			return true;
		}
		
		// First, check for duplicate of the normalized form of the tag on this object.
		$count = 0;
		if ($this->_block_multiuser_tag_on_object == 0) {
			$tagger_sql = " AND taggerid=$tagger_id";
		}
		$sql = "SELECT COUNT(*) 
			FROM $this->_obj_tbl AS rt INNER JOIN $this->_tag_tbl AS t ON (rt.tagid = t.id)
			WHERE rt.objectid='$object_id' AND rt.tbl='$this->_tbl'
			AND (t.tag='$normalized_tag' OR t.alias='$normalized_tag')";
		$sql .= $tagger_sql;
		$this->_db->setQuery( $sql );
		$count = $this->_db->loadResult();

		if ($count > 0) {
			return true;
		}

		// First see if a normalized tag in this form exists.
		$sql = "SELECT id FROM $this->_tag_tbl WHERE tag='$normalized_tag' OR alias='$normalized_tag' LIMIT 1";
		$this->_db->setQuery( $sql );
		$id = $this->_db->loadResult();

		if (!$id) {
			// Then see if a raw tag in this form exists.
			$sql = "SELECT id FROM $this->_tag_tbl WHERE raw_tag=".$this->_db->Quote(addslashes($tag))." LIMIT 1";
			$this->_db->setQuery( $sql );
			$id = $this->_db->loadResult();
		}
		if ($id) {
			$tag_id = $id;
		} else {
			// Add new tag! 
			$sql = "INSERT INTO $this->_tag_tbl (id, tag, raw_tag, alias, description, admin) VALUES ('','$normalized_tag', ".$this->_db->Quote(addslashes($tag)).", '', '', 0)";
			$this->_db->setQuery( $sql );
			if (!$this->_db->query()) {
				$err = $this->_db->getErrorMsg();
				die( $err );
			}
			// retrieve new tag's ID
			$this->_db->setQuery( "SELECT id FROM $this->_tag_tbl WHERE tag='$normalized_tag' LIMIT 1" );
			$tag_id = $this->_db->loadResult();
		}
		if (!($tag_id > 0)) {
			return false;
		}
		
		$sql = "INSERT INTO $this->_obj_tbl (id, objectid, tagid, strength, taggerid, taggedon, tbl) VALUES ('', '$object_id', '$tag_id', 0, '$tagger_id', NOW(), '$this->_tbl')";
		$this->_db->setQuery( $sql );
		if (!$this->_db->query()) {
			$err = $this->_db->getErrorMsg();
			die( $err );
		}

		return true;
	}

	//-----------

	public function tag_object($tagger_id, $object_id, $tag_string, $strength, $admin=false) 
	{
		$tagArray  = $this->_parse_tags($tag_string);   // array of normalized tags
		$tagArray2 = $this->_parse_tags($tag_string,1); // array of normalized => raw tags
		if ($admin == 1) {
			$oldTags = $this->get_tags_on_object($object_id, 0, 0, 0, 0); // tags currently assigned to an object
		} else {
			$oldTags = $this->get_tags_on_object($object_id, 0, 0, $tagger_id, 0); // tags currently assigned to an object
		}

		$preserveTags = array();

		if (count($oldTags) > 0) {
			foreach ($oldTags as $tagItem) 
			{
				if (!in_array($tagItem['tag'], $tagArray)) {
					// We need to delete old tags that don't appear in the new parsed string.
					$this->remove_tag($tagger_id, $object_id, $tagItem['tag'], $admin);
				} else {
					// We need to preserve old tags that appear (to save timestamps)
					$preserveTags[] = $tagItem['tag'];
				}
			}
		}
		$newTags = array_diff($tagArray, $preserveTags);

		foreach ($newTags as $tag) 
		{
			$tag = trim($tag);
			if (($tag != '') && (strlen($tag) <= $this->_max_tag_length)) {
				if (get_magic_quotes_gpc()) {
					$tag = addslashes($tag);
				}
				$thistag = $tagArray2[$tag];
				$this->safe_tag($tagger_id, $object_id, $thistag, $strength);
			}
		}
		return true;
	}

	//-----------

	public function remove_tag($tagger_id, $object_id, $tag, $admin) 
	{
		if (!isset($object_id) || !isset($tag)) {
			die('remove_tag argument missing');
			return false;
		}

		$tag_id = $this->get_tag_id($tag);

		if ($tag_id > 0) {
			if ($admin == 1) {
				$sql = "DELETE FROM $this->_obj_tbl
					WHERE objectid='$object_id' AND tbl='$this->_tbl'
					AND tagid='$tag_id'";
			} else {
				$sql = "DELETE FROM $this->_obj_tbl
					WHERE taggerid='$tagger_id' 
					AND objectid='$object_id' AND tbl='$this->_tbl'
					AND tagid='$tag_id'";
			}

			$this->_db->setQuery( $sql );
			if (!$this->_db->query()) {
				$err = $this->_db->getErrorMsg();
				die( $err );
			}
			return true;
		} else {
			return false;
		}
	}

	//-----------

	public function remove_all_tags($object_id) 
	{
		if ($object_id > 0) {
			$this->_db->setQuery( "DELETE FROM $this->_obj_tbl WHERE objectid = $object_id AND tbl='$this->_tbl'" );
			return true;
		} else {
			return false;	
		}
	}

	//-----------
	
	public function normalize_tag($tag) 
	{
		if ($this->_normalize_tags) {
			$normalized_valid_chars = $this->_normalized_valid_chars;
			$normalized_tag = preg_replace("/[^$normalized_valid_chars]/", "", $tag);
			return strtolower($normalized_tag);
		} else {
			return $tag;
		}
	}
	
	//-----------
	
	public function get_tag_id($tag) 
	{
		if (!isset($tag)) {
			die('get_tag_id argument missing');
			return false;
		}

		$this->_db->setQuery( "SELECT id FROM $this->_tag_tbl WHERE tag='$tag' OR alias='$tag' LIMIT 1" );
		return $this->_db->loadResult();
	}

	//-----------

	public function get_raw_tag_id($tag) 
	{
		if (!isset($tag)) {
			die('get_raw_tag_id argument missing');
			return false;
		}

		$this->_db->setQuery( "SELECT id FROM $this->_tag_tbl WHERE raw_tag=".$this->_db->Quote($tag, false)." LIMIT 1" );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function count_tags($admin=0)
	{
		if ($admin == 0) {
			$filter = "WHERE admin=0 ";
		} else {
			$filter = "";
		}
		
		$this->_db->setQuery( "SELECT COUNT(*) FROM $this->_tag_tbl $filter" );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function get_tag_cloud($showsizes=0, $admin=0, $oid=NULL)
	{
		// set some variables
		$min_font_size = 1;
		$max_font_size = 1.8;
		
		$filter = "";
		if ($oid) {
			$filter .= "WHERE rt.objectid=".$oid;
		}
		if ($admin == 0) {
			if ($oid) {
				$filter .= " AND t.admin=0 ";
			} else {
				$filter .= "WHERE t.admin=0 ";
			}
		} else {
			$filter .= "";
		}
		
		// find all tags
		$sql = "SELECT t.tag, t.raw_tag, t.admin, COUNT(*) as count
				FROM $this->_tag_tbl AS t INNER JOIN $this->_obj_tbl AS rt ON (rt.tagid = t.id) AND rt.tbl='$this->_tbl' $filter
				GROUP BY raw_tag
				ORDER BY raw_tag ASC";
		$this->_db->setQuery( $sql );
		$tags = $this->_db->loadObjectList();
	
		$html = '';
		
		if ($tags && count($tags) > 0) {
			if ($showsizes) {
				$retarr = array();
				foreach ($tags as $tag)
				{
					$retarr[$tag->raw_tag] = $tag->count;
				}
				ksort($retarr);

				$max_qty = max(array_values($retarr));  // Get the max qty of tagged objects in the set
				$min_qty = min(array_values($retarr));  // Get the min qty of tagged objects in the set

				// For ever additional tagged object from min to max, we add $step to the font size.
				$spread = $max_qty - $min_qty;
				if (0 == $spread) { // Divide by zero
					$spread = 1;
				}
				$step = ($max_font_size - $min_font_size)/($spread);
			}
			
			// build HTML
			$html = '<ol class="tags">'."\n";
			foreach ($tags as $tag)
			{
				$class = '';
				if ($tag->admin == 1) {
					$class = ' class="admin"';
				}

				$tag->raw_tag = stripslashes($tag->raw_tag);
				$tag->raw_tag = str_replace( '&amp;', '&', $tag->raw_tag );
				$tag->raw_tag = str_replace( '&', '&amp;', $tag->raw_tag );
				if ($showsizes == 1) {
					$size = $min_font_size + ($tag->count - $min_qty) * $step;
					$html .= "\t".'<li'.$class.'><span style="font-size: '. round($size,1) .'em"><a href="'.JRoute::_('index.php?option=com_tags&amp;tag='.$tag->tag).'">'.stripslashes($tag->raw_tag).'</a></span></li>'."\n";
				} elseif ($showsizes == 2) {
					$html .= ' <li'.$class.'><a href="javascript:void(0);" onclick="addtag(\''.$tag->tag.'\');">'.stripslashes($tag->raw_tag).'</a></li>'."\n";
				} else {
					$html .= "\t".'<li'.$class.'><a href="'.JRoute::_('index.php?option=com_tags&amp;tag='.$tag->tag).'">'.stripslashes($tag->raw_tag).'</a></li>'."\n";
				}
			}
			$html .= '</ol>'."\n";
		}

		return $html;
	}
	
	//-----------
	
	public function get_tag_string( $oid, $offset=0, $limit=0, $tagger_id=NULL, $strength=0, $admin=0 ) 
	{
		$tags = $this->get_tags_on_object( $oid, $offset, $limit, $tagger_id, $strength, $admin );
		
		if ($tags && count($tags) > 0) {
			$tagarray = array();
			foreach ($tags as $tag)
			{
				$tagarray[] = $tag['raw_tag'];
			}
			$tags = implode( ', ', $tagarray );
		} else {
			$tags = (is_array($tags)) ? implode('',$tags) : '';
		}
		return $tags;
	}
	
	//-----------
	
	public function _parse_tags( $tag_string, $keep=0 ) 
	{
		$newwords = array();
		
		// If the tag string is empty, return the empty set.
		if ($tag_string == '') {
			return $newwords;
		}
		
		// Perform tag parsing
		$tag_string = trim($tag_string);
		$raw_tags = explode(',',$tag_string);
		
		foreach ($raw_tags as $raw_tag)
		{
			$raw_tag = trim($raw_tag);
			$nrm_tag = $this->normalize_tag($raw_tag);
			if ($keep != 0) {
				$newwords[$nrm_tag] = $raw_tag;
			} else {
				$newwords[] = $nrm_tag;
			}
		}
		return $newwords;
	}
}
?>