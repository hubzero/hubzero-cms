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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Plugin\Plugin;
use Hubzero\Filesystem\Manager;
use Hubzero\Filesystem\File;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Plugin class for latex file handling
 */
class plgHandlersLatex extends Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Determines if the given collection can be handled by this plugin
	 *
	 * @param   \Hubzero\Filesystem\Collection  $collection  The file collection to assess
	 * @return  void
	 **/
	public function canHandle(\Hubzero\Filesystem\Collection $collection)
	{
		$need = [
			'tex' => 1
		];

		// Check extension to make sure we can proceed
		if (!$collection->hasExtensions($need))
		{
			return false;
		}

		return true;
	}

	/**
	 * Handles view events for latex files
	 *
	 * @param   \Hubzero\Filesystem\Collection  $collection  The file collection to view
	 * @return  void
	 **/
	public function onHandleView(\Hubzero\Filesystem\Collection $collection)
	{
		if (!$this->canHandle($collection))
		{
			return false;
		}

		$file = $collection->findFirstWithExtension('tex');

		// Create view
		$view = new \Hubzero\Plugin\View([
			'folder'  => 'handlers',
			'element' => 'latex',
			'name'    => 'latex',
			'layout'  => 'view'
		]);

		// Build path for storing temp previews
		$outputDir = PATH_APP . DS . trim($this->params->get('compile_dir', 'site/latex/compiled'), DS);
		$adapter   = Manager::adapter('local', ['path' => $outputDir]);
		$uniqid    = md5(uniqid());
		$temp      = File::fromPath($uniqid . '.tex', $adapter);

		// Clean up data from Windows characters - important!
		$data = preg_replace('/[^(\x20-\x7F)\x0A]*/','', $file->read());

		// Store file locally
		$temp->write($data);

		// Build the command
		$command  = DS . trim($this->params->get('texpath', '/usr/bin/pdflatex'), DS);
		$command .= ' -output-directory=' . $outputDir . ' -interaction=batchmode ' . escapeshellarg($temp->getAbsolutePath());

		// Exec and capture output
		exec($command, $out);

		$compiled = File::fromPath($uniqid . '.pdf', $adapter);
		$log      = File::fromPath($uniqid . '.log', $adapter);

		if (!$compiled->size())
		{
			$view->setError(Lang::txt('PLG_HANDLERS_LATEX_ERROR_COMPILE_TEX_FAILED'));
		}

		// Read log (to show in case of error)
		if ($log->size())
		{
			$view->log = $log->read();
		}

		$view->compiled = $compiled;

		return $view;
	}
}