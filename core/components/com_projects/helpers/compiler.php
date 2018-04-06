<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Helpers;

use Hubzero\Base\Obj;

/**
 * Projects Git LaTeX and PDF compiler helper class
 */
class Compiler extends Obj
{
	/**
	 * Cache dir
	 *
	 * @var  string
	 */
	private $_outputFolder 	= './';

	/**
	 * Is tex file?
	 *
	 * @param   string   $file
	 * @param   string   $mimeType
	 * @return  integer
	 */
	public static function isTexFile($file = '', $mimeType = '')
	{
		$tex = 0;

		// Get file extension
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
	 * @return  array
	 */
	public static function getFormatsArray()
	{
		$formats = array (
			'application' => array (
				'application/pdf'
			),
			'images' => array (
				'image/jpeg',
				'image/jpg',
				'image/png',
				'image/x-png',
				'image/gif'
			),
			'text' => array (
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
	 * @param   string   $fullpath
	 * @param   string   $data
	 * @param   string   $textpath
	 * @param   string   $outputDir
	 * @param   integer  $getPath
	 * @param   string   &$tempBase
	 * @return  string   compressed data
	 */
	public function compileTex($fullpath = '', $data = '', $texpath = '', $outputDir = '', $getPath = 0, &$tempBase = '')
	{
		if (!$texpath || !$data)
		{
			return false;
		}

		$cacheFolder = dirname($fullpath);
		$outputDir   = $outputDir ? $outputDir : $this->_outputFolder;

		if (!$tempBase)
		{
			$filename = Html::takeOutExt(basename($fullpath));
			$texFile  = $cacheFolder . DS . $filename . '__temp_' . Html::generateCode(6, 6, 0, 1, 1 );
			$tempBase = basename($texFile);
		}
		else
		{
			$texFile = $cacheFolder . DS . $tempBase;
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
		$command = $texpath . DS . 'pdflatex -output-directory=' . $outputDir . ' -interaction=batchmode ' . escapeshellarg($texFile . '.tex');
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
