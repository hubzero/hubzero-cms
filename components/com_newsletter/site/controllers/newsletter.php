<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Newsletter\Site\Controllers;

use Components\Newsletter\Tables\Newsletter as Letter;
use Hubzero\Component\SiteController;
use Pathway;
use Request;
use Route;
use Lang;
use App;

/**
 * Newsletter Controller
 */
class Newsletter extends SiteController
{
	/**
	 * Override parent execute method
	 *
	 */
	public function execute()
	{
		//get request vars
		$this->id = Request::getInt('id', 0);

		//disable default task
		$this->disableDefaultTask();

		//register task when no newsletter is passed in
		$this->registerTask('', 'view');

		//call parent
		parent::execute();
	}

	/**
	 * Override parent build title method
	 *
	 * @param 	object	$newsletter		Newsletter object for adding campaign name to title
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
	 * @param 	object	$campaign		Newsletter object for adding campaign name pathway
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
			Pathway::append(Lang::txt($newsletter->name), 'index.php?option=' . $this->_option . '&id=' . $newsletter->id);
		}
	}


	/**
	 * View Campaign Task
	 *
	 */
	public function viewTask()
	{
		//set layout
		$this->view->setLayout('view');

		//get the newsletter id
		$this->view->id = $this->id;

		//do we want to stip tags
		$stripTags = true;
		if (Request::getCmd('tmpl', '') == 'component')
		{
			$stripTags = false;
		}

		//instantiate campaign object
		$newsletterNewsletter = new Letter($this->database);

		//get the current campaign
		$currentNewsletter = $newsletterNewsletter->getCurrentNewsletter();

		if (is_object($currentNewsletter))
		{
			//do we have a newsletter id
			if ($this->view->id)
			{
				$newsletter = $newsletterNewsletter->getNewsletters($this->view->id);
				if (is_object($newsletter) && $newsletter->published)
				{
					$currentNewsletter = $newsletter;
				}
			}
			else
			{
				$this->view->id = $currentNewsletter->id;
			}

			//build newsletter
			$this->view->newsletter = $newsletterNewsletter->buildnewsletter($currentNewsletter, $stripTags);
			$this->view->newsletter = str_replace("{{UNSUBSCRIBE_LINK}}", '', $this->view->newsletter);
		}
		else
		{
			$this->view->newsletter = '';
		}

		//are we trying to output the newsletter by itself?
		if (Request::getInt('no_html', 0))
		{
			echo $this->view->newsletter;
			return;
		}

		//get list of campaigns
		$this->view->newsletters = $newsletterNewsletter->getNewsletters();

		//build title
		$this->_buildTitle($currentNewsletter);

		//build pathway
		$this->_buildPathway($currentNewsletter);

		//set vars for view
		$this->view->title = $this->_title;

		//get errors if any
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		//display
		$this->view->display();
	}

	/**
	 * Output Letter content as PDF
	 * @return void
	 */
	public function outputTask()
	{
		//get the newsletter id
		$id = $this->id;

		//instantiate campaign object
		$newsletterNewsletter = new Letter($this->database);

		//get newsletter
		$newsletter = $newsletterNewsletter->getNewsletters($id);

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