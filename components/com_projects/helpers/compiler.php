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

/**
 * Projects Git LaTeX and PDF compiler helper class
 */
class ProjectsCompiler extends JObject 
{
	/**
	 * Cache dir
	 * 
	 * @var string
	 */
	private $_outputFolder 	= './';		
	
	/**
	 * Is tex file?
	 * 
	 * @param      string	$file
	 *
	 * @return     array to be parsed
	 */
	public function isTexFile ($file = '', $mimeType = '') 
	{
		$tex = 0;
		
		// Get file extention
		if ($file)
		{
			$parts = explode('.', $file);
			$ext   = count($parts) > 1 ? array_pop($parts) : '';
			$tex   = $ext == 'tex' ? 1 : 0;
		}
		
		if ($mimeType && in_array($mimeType, array('application/x-tex', 'text/x-tex')))
		{
			$tex = 1;
		}
		
		return $tex;
	}
	
	/**
	 * Get array of file types
	 * 
	 * @param      string	$file
	 *
	 * @return     array
	 */
	public function getFormatsArray() 
	{		
		$formats = array (
		    'application' => array (
		      'application/pdf'
		    ),
		    'images'  => array (
		      'image/jpeg',
		      'image/jpg',
		      'image/png',
		      'image/x-png',
		      'image/gif'
			),
			'text'  => array (
		      'text/plain',
		      'text/css',
			  'text/x-tex',
			  'text/html'
			)
		);
		
		return $formats;
	}
	
	/**
	* Compile tex 
	*
	* @access	public
	* @param	string		fullpath
	* @param	string		data
	* @param	string		textpath
	* @param	string		outputDir
	* @param	integer		getPath
	* @param	string		&tempBase
	* @return	string		compressed data
	*/
	function compileTex( $fullpath = '', $data = '', $texpath = '', $outputDir = '', $getPath = 0, &$tempBase = '' )
	{
		if (!$texpath || !$data)
		{
			return false;
		}
		
		$cacheFolder = dirname($fullpath);
		$outputDir   = $outputDir ? $outputDir : $this->_outputFolder;
	
		if (!$tempBase)
		{
			$filename 	 = ProjectsHtml::takeOutExt(basename($fullpath));
			$texFile	 = $cacheFolder . DS . $filename . '__temp_' . ProjectsHtml::generateCode (6, 6, 0, 1, 1 );
			$tempBase	 = basename($texFile);
		}
		else
		{
			$texFile	 = $cacheFolder . DS . $tempBase;
		}
		
		$pdf = $tempBase . '.pdf';
		
		// Remove previous compilation
		if (file_exists($outputDir . DS . $pdf))
		{
			unlink($outputDir . DS . $pdf);
		} 
		
		// Create temp tex copy
		$fp = fopen($texFile . '.tex', 'w');
		fwrite($fp, $data);
		fclose($fp);
				
		chdir($cacheFolder);
		$command = $texpath . DS . 'pdflatex -output-directory=' . $outputDir . ' -interaction=batchmode ' . $texFile . '.tex';
		exec($command, $out);
				
		// Remove temp tex copy
		if (file_exists($texFile . '.tex'))
		{
			unlink($texFile . '.tex');
		}
		if (file_exists($texFile))
		{
			unlink($texFile);
		}
		
		if (file_exists($outputDir . DS . $pdf))
		{
			return $getPath ? basename($pdf) : file_get_contents($outputDir . DS . $pdf);
		}
		
		return false;
	}
}
