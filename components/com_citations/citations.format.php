<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
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

class CitationsFormat
{
	protected $_format = 'APA';
	protected $_reference = null;
	protected $_formatters = array();
	
	public function __construct($reference=null, $format='APA') 
	{
		$this->setFormat($format);
		$this->setReference($reference);
	}
	
	public function setFormat($format) 
	{
		$this->_format = trim($format);
	}
	
	public function getFormat() 
	{
		return $this->_format;
	}
	
	public function setReference($reference) 
	{
		$this->_reference = $reference;
	}
	
	public function getReference() 
	{
		return $this->_reference;
	}
	
	public function setFormatter($formatter, $format='') 
	{
		$format = ($format) ? $format : $this->_format;
		
		$this->_formatter[$format] = $formatter;
	}
	
	public function getFormatter($format='') 
	{
		$format = ($format) ? $format : $this->_format;
		
		return (isset($this->_formatter[$format])) ? $this->_formatter[$format] : NULL;
	}
	
	public function formatReference($reference=null, $highlight=null) 
	{
		if (!$reference) {
			$reference = $this->getReference();
		} else {
			$this->setReference($reference);
		}
		
		if (!$reference || (!is_array($reference) && !is_object($reference))) {
			return '';
		}
		
		$format = $this->getFormat();

		$formatter = $this->getFormatter($format);
		
		if (!$formatter || !is_object($formatter)) {
			$cls = 'CitationsFormat'.$format;
			
			if (is_file(JPATH_ROOT.DS.'components'.DS.'com_citations'.DS.'format'.DS.strtolower($format).'.php')) {
				include_once(JPATH_ROOT.DS.'components'.DS.'com_citations'.DS.'format'.DS.strtolower($format).'.php');
			}
			if (!class_exists($cls)) {
				JError::raiseError( 500, JText::_('Format unavailable.') );
				return;
			}

			$formatter = new $cls();

			if (!$formatter instanceof CitationsFormatAbstract) {
				JError::raiseError( 500, JText::_('Invalid formatter specified.') );
				return;
			}

			$this->setFormatter($formatter, $format);
		}
		
		ximport('Hubzero_View_Helper_Html');
		
		return $formatter->format($reference, 'none', $highlight);
	}
}

