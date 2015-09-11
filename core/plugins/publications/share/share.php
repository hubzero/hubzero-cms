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

// No direct access
defined('_HZEXEC_') or die();

/**
 * Short description for 'plgPublicationsShare'
 *
 * Long description (if any) ...
 */
class plgPublicationsShare extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Short description for 'onPublicationsAreas'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $publication Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function &onPublicationAreas($publication, $version = 'default', $extended = true)
	{
		$areas = array();
		return $areas;
	}

	/**
	 * Short description for 'onPublications'
	 *
	 * Long description (if any) ...
	 *
	 * @param      mixed $publication Parameter description (if any) ...
	 * @param      string $option Parameter description (if any) ...
	 * @param      unknown $areas Parameter description (if any) ...
	 * @param      string $rtrn Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true)
	{
		$arr = array(
			'html'=>'',
			'metadata'=>'',
			'name'=>'share'
		);

		// Hide if version not published
		if (!$extended || $publication->state == 4 || $publication->state == 5 || $publication->state == 6)
		{
			return $arr;
		}

		$sef = Route::url('index.php?option=' . $option . '&id=' . $publication->id);
		$url = Request::base() . trim($sef, DS);
		$url = $url . DS . '?v=' . $publication->version_number;

		$mediaUrl = Request::base() . trim($sef, DS) . DS . $publication->version_id . DS . 'Image:master';

		// Incoming action
		$sharewith = Request::getVar('sharewith', '');
		if ($sharewith && $sharewith != 'email')
		{
			$this->share($sharewith, $url, $mediaUrl, $publication, $version);
			return;
		}

		// Build the HTML meant for the "about" tab's metadata overview
		if ($rtrn == 'all' || $rtrn == 'metadata')
		{
			// Instantiate a view
			$view = $this->view('default', 'options');

			// Pass the view some info
			$view->option      = $option;
			$view->publication = $publication;
			$view->version     = $version;
			$view->_params     = $this->params;
			$view->url         = $url;
			if ($this->getError())
			{
				$view->setError($this->getError());
			}

			// Return the output
			$arr['metadata'] = $view->loadTemplate();
		}

		return $arr;
	}

	/**
	 * Short description for 'share'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $with Parameter description (if any) ...
	 * @param      string $url Parameter description (if any) ...
	 * @param      mixed $publication Parameter description (if any) ...
	 * @return     void
	 */
	public function share($with, $url, $mediaUrl, $publication, $version)
	{
		$link = '';
		$description = $publication->abstract
			? \Hubzero\Utility\String::truncate(stripslashes($publication->abstract), 250) : '';
		$description = urlencode($description);
		$title = stripslashes($publication->title);
		$title = urlencode($title);

		switch ($with)
		{
			case 'facebook':
				$link = 'https://www.facebook.com/sharer/sharer.php?u=' . $url . '&t=' . $title;
				break;

			case 'twitter':
				$link = 'http://twitter.com/home?status=' . urlencode(Lang::txt('PLG_PUBLICATION_SHARE_VIEWING',
						Config::get('sitename'),
						stripslashes($publication->title) . ' ' . $url));
				break;

			case 'google':
				$link = 'https://plus.google.com/share?url=' . $url;
				break;

			case 'delicious':
				$link = 'http://del.icio.us/post?url=' . $url . '&title=' . $title;
				break;

			case 'reddit':
				$link = 'http://reddit.com/submit?url=' . $url . '&title=' . $title;
				break;

			case 'linkedin':
				$link = 'https://www.linkedin.com/shareArticle?mini=true&url=' . $url . '&title='
				. $title . '&summary=' . $description;
				break;

			case 'pinterest':
				$link = 'https://pinterest.com/pin/create/button/?url=' . $url . '&media='
				. $mediaUrl . '&description=' . $title . ': ' . $description;
				break;
		}

		if ($link)
		{
			App::redirect($link, '', '');
		}
	}
}

