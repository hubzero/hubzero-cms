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

class XContentServer
{
	var $_filename;
	var $_acceptranges;
	var $_disposition;
	var $_saveas;
        var $_mimetype;

	function saveas($saveas = null)
	{
		if (!is_null($saveas)) 
			$this->_saveas = basename($saveas);

		return($this->_saveas);
	}

	function filename($filename = null)
	{
		if (!is_null($filename)) 
			$this->_filename = $filename;

		return($this->_filename);
	}

	function acceptranges($acceptranges = null)
	{
		if (!is_null($acceptranges))
			$this->_acceptranges = ($acceptranges) ? true : false;

		return($this->_acceptranges);
	}

	function disposition($disposition = null)
	{
		if (!is_null($disposition)) {
			if ( strcasecmp($disposition,"inline") == 0)
				$disposition = "inline";
			else if ( strcasecmp($disposition,"attachment") == 0)
				$disposition = "attachment";
			else
				$disposition = "inline";

			$this->_disposition = $disposition;
		}

		return($this->_disposition);
	}

        function mimetype($mimetype = null)
	{
		if (!is_null($mimetype))
			$this->_mimetype = $mimetype;

		return($this->_mimetype);
	}

	function XContentServer()
	{
	}

	function serve()
	{
		return XContentServer::serve_file($this->_filename, $this->_saveas, $this->_disposition, $this->_acceptranges, $this->_mimetype);
	}

	function serve_attachment($filename, $saveas = null, $acceptranges = true)
	{
		return XContentServer::serve_file($filename, $saveas, 'attachment', $acceptranges);
	}

	function serve_inline($filename, $acceptranges = true)
	{
		return XContentServer::serve_file($filename, null, 'inline', $acceptranges);
	}

	function serve_file($filename, $saveas=null, $disposition='inline', $acceptranges=true, $mimetype=null)
	{
		$fp = fopen($filename,"rb");

		if ($fp == false)
			return false;

		$fileinfo = pathinfo($filename);

		if (empty($saveas))
			$saveas = $fileinfo['basename'];
		else
			$saveas = basename($saveas);

		$saveas   = addslashes($saveas);

		$filesize = filesize($filename);

		$extension =  $fileinfo['extension'];

                if(!is_null($mimetype)){
                  $type = $mimetype;
                }else{
		  if (strcasecmp($extension, 'jnlp') == 0)
			$type = 'application/x-java-jnlp-file';
		  else if (strcasecmp($extension, 'pdf') == 0)
			$type = 'application/pdf';
		  else if (strcasecmp($extension, 'docx') == 0)
			$type = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
		  else
			$type = 'application/octet-stream';
                }

		if ($acceptranges                        && 
		    $_SERVER['REQUEST_METHOD']=='GET' && 
		    isset($_SERVER['HTTP_RANGE'])     && 
		    $range=stristr(trim($_SERVER['HTTP_RANGE']),'bytes=')) {
    			$range=substr($range,6);
    			$boundary='g45d64df96bmdf4sdgh45hf5';//set a random boundary
    			$ranges=explode(',',$range);
			$partial = true;
  		}
		else {
			$ranges = array("0-" . ($filesize - 1));
			$partial = false;
		}

		$multipart = (count($ranges) > 1);
      		$content_length=0;

		foreach ($ranges as $range) {

			preg_match("/^\s*(\d*)\s*-\s*(\d*)\s*$/", $range, $match);

  			$first=isset($match[1]) ? $match[1] : '';
  			$last=isset($match[2])  ? $match[2] : '';

			if ($first!='') // byte-range-set
			{
				if (($last >= $filesize) || ($last == ''))
					$last = $filesize - 1;
			}		
			else if ($last != '') // suffix-byte-range-set
			{
    				$first = $filesize - $last;
    				$last  = $filesize - 1;

    				if ($first < 0) 	
					$first = 0;
  			} 

  			if ( ($first > $last) || ($last == '') ) // unsatisfiable range
			{
    				header("Status: 416 Requested range not satisfiable");
    				header("Content-Range: */$filesize");
    				exit;
  			}

			$result[$range]['first'] = $first;
			$result[$range]['last'] = $last;

			if ($multipart) {
				$content_length+=strlen("\r\n--$boundary\r\n");
				$content_length+=strlen("Content-type: $type\r\n");
				$content_length+=strlen("Content-range: bytes $first-$last/$filesize\r\n\r\n");
			}

			$content_length+=$last-$first+1;          
		}

		if ($multipart)
			$content_length+=strlen("\r\n--$boundary--\r\n");

		//output headers

		if ($partial)
			header("HTTP/1.1 206 Partial content");

		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$msie = preg_match("/MSIE (\d+)\.(\d+)([^;]*);/i", $_SERVER['HTTP_USER_AGENT'], $matches); // MSIE
		} else {
			$msie = false;
		}
		
		if(!$partial && ($disposition=="attachment")) {
                	if ($msie && ($matches[1] < 6) ) // untested IE 5.5 workaround
                       		header("Content-Disposition: filename=" . $saveas);
                	else
                       		header("Content-Disposition: attachment; filename=\"" . $saveas . "\"");
		}

		if ($multipart)
			header("Content-Type: multipart/x-byteranges; boundary=$boundary");
		else {
			header("Content-Type: $type");
		}

		// IE6 "save as" chokes on pragma no-cache or no-cache being 
		// first on the Cache-Control header

		if (!$msie)
			header("Pragma: no-cache");
		
		header("Expires: 0");
		header("Cache-Control: no-store, no-cache, must-revalidate");

		if ($acceptranges)
			header("Accept-Ranges: bytes");

		header("Content-Length: $content_length");

		//output the content

		//unset magic quotes; otherwise, file contents will be modified
	    $old_magic_quotes = get_magic_quotes_runtime();

		if ($old_magic_quotes)
			set_magic_quotes_runtime(0);

		ob_end_clean();

		foreach ($ranges as $range) {
			$first = $result[$range]['first'];
			$last  = $result[$range]['last'];

			if ($multipart)
				echo "\r\n--$boundary\r\n";

			if ($partial) {
				echo "Content-type: $type\r\n";
				echo "Content-range: bytes $first-$last/$filesize\r\n\r\n";
			}

			$buffer_size = 8096;

			fseek($fp, $first);

			if ( ($last + 1) == $filesize )
				fpassthru($fp);
			else {
				$bytes_left=$last-$first+1;

				while($bytes_left>0 && !feof($fp)) {
    
					if($bytes_left>$buffer_size)
						$bytes_to_read=$buffer_size;
					else
						$bytes_to_read=$bytes_left;

					$bytes_left-=$bytes_to_read;

					echo fread($fp, $bytes_to_read);

					flush();
				}
			}
		}
				
		if ($multipart)
			echo "\r\n--$boundary--\r\n";

  		fclose($fp);

		if ($old_magic_quotes)
			set_magic_quotes_runtime(1);

		ob_start(); // restart buffering so we can throw away any extraneous output after this point

		return true;

	}
}

?>
