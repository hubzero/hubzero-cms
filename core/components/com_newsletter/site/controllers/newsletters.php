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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Site\Controllers;

use Components\Newsletter\Models\Newsletter;
use Hubzero\Component\SiteController;
use Pathway;
use Request;
use Route;
use Lang;
use App;

/**
 * Newsletter Controller
 */
class Newsletters extends SiteController
{
	/**
	 * Override parent execute method
	 *
	 * @return  void
	 */
	public function execute()
	{
		//get request vars
		$this->id = Request::getInt('id', 0);

		$this->registerDefaultTask('view');

		parent::execute();
	}

	/**
	 * Override parent build title method
	 *
	 * @param   object  $newsletter  Newsletter object for adding campaign name to title
	 * @return  void
	 */
	public function _buildTitle($newsletter = null)
	{
		//default if no campaign
		$this->_title = Lang::txt(strtoupper($this->_option));

		//add campaign name to title
		if (is_object($newsletter) && $newsletter->id)
		{
			$this->_title = Lang::txt('COM_NEWSLETTER_NEWSLETTER') . ': ' . $newsletter->name;
		}

		//set title of browser window
		App::get('document')->setTitle($this->_title);
	}


	/**
	 * Override parent build pathway method
	 *
	 * @param   object  $newsletter  Newsletter object for adding campaign name pathway
	 * @return  void
	 */
	public function _buildPathway($newsletter = null)
	{
		//add 'newlsetters' item to pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(Lang::txt(strtoupper($this->_option)), 'index.php?option=' . $this->_option);
		}

		//add campaign
		if (is_object($newsletter) && $newsletter->id)
		{
			Pathway::append($newsletter->name, 'index.php?option=' . $this->_option . '&id=' . $newsletter->id);
		}
	}

	/**
	 * View Campaign Task
	 *
	 * @return  void
	 */
	public function viewTask()
	{
		// get the newsletter id
		$id = $this->id;

		// do we want to stip tags
		$stripTags = true;
		if (Request::getCmd('tmpl', '') == 'component')
		{
			$stripTags = false;
		}

		$newsletter = '';
		$current = null;

		// get the current campaign
		if ($id)
		{
			$current = Newsletter::oneOrNew($id);

			if (!$current->published)
			{
				$current = null;
			}
		}

		if (!$current)
		{
			$current = Newsletter::current();
			$id = $current->get('id');
		}

		if (is_object($current))
		{
			//build newsletter
			$newsletter = $current->buildnewsletter($current, $stripTags);
			$newsletter = str_replace("{{UNSUBSCRIBE_LINK}}", '', $newsletter);
		}

		//are we trying to output the newsletter by itself?
		if (Request::getInt('no_html', 0))
		{
			echo $newsletter;
			return;
		}

		//get list of campaigns
		$newsletters = Newsletter::all()
			->whereEquals('published', 1)
			->whereEquals('deleted', 0)
			->ordered()
			->rows();

		//build title
		$this->_buildTitle($current);

		//build pathway
		$this->_buildPathway($current);

		//set vars for view
		$title = $current->get('name', "No Newsletters");

		//display
		$this->view
			->set('id', $id)
			->set('newsletter', $newsletter)
			->set('newsletters', $newsletters)
			->set('title', $title)
			->setLayout('view')
			->display();
	}

	/**
	 * Output Letter content as PDF
	 * @return void
	 */
	public function outputTask()
	{
		//get the newsletter id
		$id = $this->id;

		//get newsletter
		$newsletter = Newsletter::oneOrFail($id);

		//build url to newsletter with no html
		$newsletterUrl = 'https://' . $_SERVER['HTTP_HOST'] . DS . 'newsletter' . DS . $newsletter->alias . '?no_html=1';

		//path to newsletter file
		$newsletterPdfFolder = PATH_APP . DS . 'site' . DS . 'newsletter' . DS . 'pdf';
		$newsletterPdf = $newsletterPdfFolder . DS . $newsletter->alias . '.pdf';

		// check for upload path
		if (!is_dir($newsletterPdfFolder))
		{
			// Build the path if it doesn't exist
			if (!\Filesystem::makeDirectory($newsletterPdfFolder))
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&id=' . $id),
					Lang::txt('Unable to create the filepath.'),
					'error'
				);
				return;
			}
		}

		// check multiple places for wkhtmltopdf lib
		// fallback on phantomjs
		$cmd = '';
		$fallback = '';
		if (file_exists('/usr/bin/wkhtmltopdf') && file_exists('/usr/bin/xvfb-run'))
		{
			//$cmd = '/usr/bin/wkhtmltopdf ' . $newsletterUrl . ' ' . $newsletterPdf;
			$cmd = '/usr/bin/xvfb-run -a -s "-screen 0 640x480x16" wkhtmltopdf ' . $newsletterUrl . ' ' . $newsletterPdf;
		}
		else if (file_exists('/usr/local/bin/wkhtmltopdf') && file_exists('/usr/local/bin/xvfb-run'))
		{
			//$cmd = '/usr/local/bin/wkhtmltopdf ' . $newsletterUrl . ' ' . $newsletterPdf;
			$cmd = '/usr/local/bin/xvfb-run -a -s "-screen 0 640x480x16" wkhtmltopdf ' . $newsletterUrl . ' ' . $newsletterPdf;
		}

		if (file_exists('/usr/bin/phantomjs'))
		{
			$rasterizeFile = PATH_CORE . DS . 'components' . DS . 'com_newsletter' . DS . 'assets' . DS . 'js' . DS . 'rasterize.js';
			$fallback = '/usr/bin/phantomjs --ssl-protocol=any --ignore-ssl-errors=yes --web-security=false ' . $rasterizeFile . ' ' . $newsletterUrl . ' ' . $newsletterPdf . ' 8.5in*11in';
			if (!$cmd)
			{
				$cmd = $fallback;
			}
		}

		if (isset($cmd))
		{
			// exec command
			exec($cmd, $ouput, $status);

			// wkhtmltopdf failed, so let's try phantomjs
			if (!file_exists($newsletterPdf) && $fallback && $cmd != $fallback)
			{
				exec($fallback, $ouput, $status);
			}
		}

		//make sure we have a file to output
		if (!file_exists($newsletterPdf))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&id=' . $id),
				Lang::txt('COM_NEWSLETTER_VIEW_OUTPUT_PDFERROR'),
				'error'
			);
			return;
		}

		//output as attachment
		header("Content-type: application/pdf");
		header("Content-Disposition: attachment; filename=" . str_replace(' ', '_', $newsletter->name) . ".pdf");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo file_get_contents($newsletterPdf);
		exit();
	}
}
