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

class XSearchPhrase 
{
	private $_text  = NULL;     // The original search text - should NEVER BE CHANGED
	private $_stem  = NULL;     // A flag for if we should stem words or not
	private $_data  = array();  // Processed text
	private $_error = NULL;     // Error holder
	
	//-----------
	
	public function __construct( $text=NULL, $stem=false )
	{		
		$this->_text = $text;
		$this->_stem = $stem;
		$this->searchTokens = array();
	}

	//-----------
	
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}

	//-----------
	
	public function process() 
	{
		if (trim($this->_text) == '') {
			return;
		}
		
		// An array for all the keywords
		$words = array();
		$phrases = array();
		
		$keyword = stripslashes($this->_text);
		// Look for anything in quotes, indicating an exact phrase search
		if (preg_match_all('/"([^"]*)"|\'([^\']*)\'/', $keyword, $matches)) {
			// Find all the matches and store them in the phrases array
			// then remove them from the keyword string
			foreach ($matches[0] as $match) 
			{
				$keyword = str_replace($match, '', $keyword);
				
				$phrases[] = trim(substr($match, 1, -1));
			}
			
			$keyword = trim($keyword);
		}

		// Explode the remaining keyword string into individual words
		$bits = explode(' ', $keyword);
		
		// Loop through each word
		foreach ($bits as $bit) 
		{
			// Trim it and make sure it's actually a word
			// Prevents cases with double spaces between words. example: Joe  Smith
			$bit = trim($bit);
			if ($bit != '') {
				$words[] = $bit;
				// Are we stemming?
				if ($this->_stem) {
					// Get the stem
					$stem = PorterStemmer::Stem($bit);
					// Make sure it's different than the original word
					if ($stem != $bit) {
						$words[] = $stem;
					}
				}
			}
		}
		
		$this->searchPhrases = $phrases;
		$this->searchWords   = $words;
		$this->searchTokens  = array_merge($phrases, $words);
		$this->searchText    = $this->_text;
	}
}
?>