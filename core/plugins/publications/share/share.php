<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
		$sharewith = Request::getString('sharewith', '');

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
		$description = \Hubzero\Utility\Str::truncate(stripslashes($publication->abstract), 250);
		$description = urlencode($description);
		$title = stripslashes($publication->title);
		$title = urlencode($title);

		switch ($with)
		{
			case 'facebook':
				$link = 'https://www.facebook.com/sharer/sharer.php?u=' . $url . '&t=' . $title;
				break;

			case 'twitter':
				$link = 'http://twitter.com/intent/tweet?text=' . urlencode(Lang::txt('PLG_PUBLICATION_SHARE_VIEWING', Config::get('sitename'), stripslashes($publication->title) . ' ' . $url));
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
