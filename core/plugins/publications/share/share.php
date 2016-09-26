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
 * Publication Plugin class for showing social sharing options
 */
class plgPublicationsShare extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param   object   $publication
	 * @param   string   $version
	 * @param   boolean  $extended
	 * @return  array
	 */
	public function &onPublicationAreas($publication, $version = 'default', $extended = true)
	{
		$areas = array();
		return $areas;
	}

	/**
	 * Return data on a publication view (this will be some form of HTML)
	 *
	 * @param   object   $publication
	 * @param   string   $option
	 * @param   array    $areas
	 * @param   string   $rtrn
	 * @param   string   $version
	 * @param   boolean  $extended
	 * @return  array
	 */
	public function onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true)
	{
		$arr = array(
			'html'     => '',
			'metadata' => '',
			'name'     => 'share'
		);

		// Hide if version not published
		if (!$extended || in_array($publication->state, array(4, 5, 6)))
		{
			return $arr;
		}

		$sef = Route::url('index.php?option=' . $option . '&id=' . $publication->id);
		$sef = rtrim($sef, '/') . '/?v=' . $publication->version_number;
		$url = Request::base() . ltrim($sef, '/');

		$mediaUrl = Request::base() . trim($sef, '/') . '/' . $publication->version_id . '/Image:master';

		// Incoming action
		$sharewith = Request::getVar('sharewith', '');

		if ($sharewith)
		{
			if (!User::isGuest())
			{
				// Log the activity
				Event::trigger('system.logActivity', [
					'activity' => [
						'action'      => 'shared',
						'scope'       => 'publication',
						'scope_id'    => $publication->id,
						'description' => Lang::txt('PLG_PUBLICATIONS_SHARE_ENTRY_SHARED', '<a href="' . $sef . '">' . $publication->title . '</a>', $sharewith),
						'details'     => array(
							'with'    => $sharewith,
							'title'   => $publication->title,
							'url'     => $sef,
							'version' => $publication->version_number
						)
					],
					'recipients' => [
						['publication', $publication->id],
						['user', $publication->created_by],
						['user', User::get('id')]
					]
				]);
			}

			return $this->share($sharewith, $url, $mediaUrl, $publication, $version);
		}

		// Build the HTML meant for the "about" tab's metadata overview
		if ($rtrn == 'all' || $rtrn == 'metadata')
		{
			// Instantiate a view
			$view = $this->view('default', 'options')
				->set('option', $option)
				->set('publication', $publication)
				->set('version', $version)
				->set('_params', $this->params)
				->set('url', $url)
				->setErrors($this->getErrors());

			// Return the output
			$arr['metadata'] = $view->loadTemplate();
		}

		return $arr;
	}

	/**
	 * Redirect to social sharer
	 *
	 * @param   string  $with
	 * @param   string  $url
	 * @param   string  $mediaUrl
	 * @param   object  $publication
	 * @param   object  $version
	 * @return  void
	 */
	public function share($with, $url, $mediaUrl, $publication, $version)
	{
		$link = '';
		$description = \Hubzero\Utility\String::truncate(stripslashes($publication->abstract), 250);
		$description = urlencode($description);
		$title = stripslashes($publication->title);
		$title = urlencode($title);

		switch ($with)
		{
			case 'facebook':
				$link = 'https://www.facebook.com/sharer/sharer.php?u=' . $url . '&t=' . $title;
				break;

			case 'twitter':
				$link = 'http://twitter.com/home?status=' . urlencode(Lang::txt('PLG_PUBLICATION_SHARE_VIEWING', Config::get('sitename'), stripslashes($publication->title) . ' ' . $url));
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
				$link = 'https://www.linkedin.com/shareArticle?mini=true&url=' . $url . '&title=' . $title . '&summary=' . $description;
				break;

			case 'pinterest':
				$link = 'https://pinterest.com/pin/create/button/?url=' . $url . '&media=' . $mediaUrl . '&description=' . $title . ': ' . $description;
				break;
		}

		if ($link)
		{
			App::redirect($link, '', '');
		}
	}
}
