<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
 
defined('_JEXEC') or die('Restricted access');

class RSFormProXLS
{
    /* private */
    var $position = 0;          // stream pointer
    var $mode = "rb";           // default stream open mode
    var $xlsfilename = null;    // stream name
    var $fp = null;             // internal stream pointer to physical file
    var $buffer = null;         // internal write buffer
    var $endian = "unknown";	// little | unknown | big endian mode
	var $bin = array(
		"big" => "v",
		"little" => "s",
		"unknown" => "s",
	);
	var $start_row = 0;
	var $end_row = 0;
	var $use_headers = 0;
	
	/**
	 * detect server endian mode
	 * thanks to Charles Turner for picking this one up
	 * @access	private
	 * @params	void
	 * @returns	void
	 * @see		http://www.phpdig.net/ref/rn45re877.html
	 */
	function _detect()
	{
		// A hex number that may represent 'abyz'
		$abyz = 0x6162797A;

		// Convert $abyz to a binary string containing 32 bits
		// Do the conversion the way that the system architecture wants to
		switch (pack ('L', $abyz))
		{
		    // Compare the value to the same value converted in a Little-Endian fashion
		    case pack ('V', $abyz):
		        $this->endian = "little";
		        break;

		    // Compare the value to the same value converted in a Big-Endian fashion
		    case pack ('N', $abyz):
		        $this->endian = "big";
		        break;

		    default:
		        $this->endian = "unknown";
		        break;
		}
	}

    /**
     * called by fopen() to the stream
     * @param   (string)    $path           file path
     * @param   (string)    $mode           stream open mode
     * @param   (int)       $options        stream options (STREAM_USE_PATH |
     *                                      STREAM_REPORT_ERRORS)
     * @param   (string)    $opened_path    stream opened path
     */
    function open($path, $mode, $start_row=0)
    {
        $this->xlsfilename = $path;
        $this->position = 0;
        $this->mode = $mode;

		$this->_detect();	// detect endian mode

        //@TODO: test for invalid mode and trigger error if required

        // open underlying resource
        $this->fp = @fopen($this->xlsfilename, $this->mode);
        if (is_resource($this->fp))
        {
            // empty the buffer
            $this->buffer = "";

            if (preg_match("/^w|x/", $this->mode))
            {
                // write an Excel stream header
                $str = pack(str_repeat($this->bin[$this->endian], 6), 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
                fwrite($this->fp, $str);
                $this->position = strlen($str);
            }
			if (preg_match("/a/", $this->mode))
			{
				$this->position = filesize($this->xlsfilename);
				$this->start_row = (int) $start_row;
			}
			if ($this->use_headers)
				$this->start_row += 1;
        }
        return is_resource($this->fp);
    }

    /**
     * read the underlying stream resource (automatically called by fread/fgets)
     * @todo    modify this to convert an excel stream to an array
     * @param   (int)       $byte_count     number of bytes to read (in 8192 byte blocks)
     */
    function read($byte_count)
    {
        if (is_resource($this->fp) && !feof($this->fp))
        {
            $data .= fread($this->fp, $byte_count);
            $this->position = strlen($data);
        }
        return $data;
    }

    /**
     * called automatically by an fwrite() to the stream
     * @param   (string)    $data           serialized array data string
     *                                      representing a tabular worksheet
     */
    function write($data)
    {
		if (is_array($data))
			$data = serialize($data);
			
        // buffer the data
        $this->buffer .= $data;
        $bufsize = strlen($data);
        return $bufsize;
    }
	
	function write_headers($_data)
	{
		$row = 0;
		// write the column headers only if we are not appending
		if (preg_match("/^w|x/", $this->mode))
		{
			// write the column headers
			foreach (array_values($_data) as $col => $val)
			{
				// next line intentionally commented out
				// since we don't want a warning about the
				// extra bytes written
				// $size += $this->write($row, $col, $val);
				$this->_xlsWriteCell($row, $col, $val);
			}
			return true;
		}
		return false;
	}

    /**
     * pseudo write function to manipulate the data
     * stream before writing it
     * modify this to suit your data array
     * @access  private
     * @param   (array)     $data           associative array representing
     *                                      a tabular worksheet
     */
    function _xls_stream_write($data)
    {
		$size = 0;
        if (is_array($data) && !empty($data))
        {
            $row = $this->start_row;
            foreach (array_values($data) as $_data)
            {
                if (is_array($_data) && !empty($_data))
                {
                    foreach (array_values($_data) as $col => $val)
                    {
                        $size += $this->_xlsWriteCell($row, $col, $val);
                    }
                    $row++;
                }
            }
			$this->end_row = $row;
        }
        return $size;
    }

    /**
     * Excel worksheet cell insertion
     * (single-worksheet supported only)
     * @access  private
     * @param   (int)       $row            worksheet row number (0...65536)
     * @param   (int)       $col            worksheet column number (0..255)
     * @param   (mixed)     $val            worksheet row number
     */
    function _xlsWriteCell($row, $col, $val)
    {
        if (is_float($val) || is_int($val))
        {
            // doubles, floats, integers
            $str  = pack(str_repeat($this->bin[$this->endian], 5), 0x203, 14, $row, $col, 0x0);
            $str .= pack("d", $val);
        }
        else
        {
			$val = utf8_decode($val);
			
            // everything else is treated as a string
            $l    = strlen($val);
            $str  = pack(str_repeat($this->bin[$this->endian], 6), 0x204, 8 + $l, $row, $col, 0x0, $l);
            $str .= $val;
        }
        fwrite($this->fp, $str);
        $this->position += strlen($str);
        return strlen($str);
    }

    /**
     * called by an fclose() on the stream
     */
    function close()
    {
		$was_empty = empty($this->buffer);
		
		// flush the buffer
        $bufsize = $this->_xls_stream_write(unserialize($this->buffer));
		// ...and empty it
        $this->buffer = null;
		
		// write the buffer when we are appending and the buffer is empty
		// this should be the last step
        if (preg_match("/a/", $this->mode) && $was_empty)
        {
            // write the xls EOF
            $str = pack(str_repeat($this->bin[$this->endian], 2), 0x0A, 0x00);
            $this->position += strlen($str);
            fwrite($this->fp, $str);
        }

        // ...and close the internal stream
        return fclose($this->fp);
    }

    function eof()
    {
        $eof = true;
        if (is_resource($this->fp))
        {
            $eof = feof($this->fp);
        }
        return $eof;
    }
}
?>