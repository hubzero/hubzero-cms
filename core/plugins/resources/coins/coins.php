<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Resources Plugin class for adding COinS metadata to the document
 */
class plgResourcesCoins extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param   object  $model   Current model
	 * @param   string  $option  Name of the component
	 * @param   array   $areas   Active area(s)
	 * @param   string  $rtrn    Data to be returned
	 * @return  void
	 */
	public function onResources($model, $option, $areas, $rtrn='all')
	{
		if (!App::isSite())
		{
			return;
		}

		if (Request::getWord('tmpl') || Request::getWord('format') || Request::getInt('no_html'))
		{
			return;
		}

		if (!$model->id)
		{
			return;
		}

		$arr = array(
			'area'     => '',
			'html'     => '',
			'metadata' => $this->coins($model)
		);

		return $arr;
	}

	/**
	 * Return data on a resource for the /browse list
	 *
	 * @param   object  $model  Current model
	 * @return  string
	 */
	public function onResourcesList($model)
	{
		return $this->coins($model);
	}

	/**
	 *  Generate microformat
	 *
	 * @param   object  $model  Current model
	 * @return  string
	 */
	public function coins($model)
	{
		if (!is_object($model))
		{
			return;
		}

		$title = array(
			'ctx_ver=Z39.88-2004',
			'rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Ajournal',  //info:ofi/fmt:kev:mtx:journal
			'rft.genre=article',
			'rft.atitle=' . urlencode($model->title),
			'rft.date=' . Date::of($model->date)->toLocal('Y')
		);

		// DOI/identifier
		$tconfig = Component::params('com_tools');

		if ($doi = $model->get('doi'))
		{
			if ($tconfig->get('doi_shoulder'))
			{
				$doi = $tconfig->get('doi_shoulder') . '/' . strtoupper($doi);
			}
		}
		else if ($model->get('doi_label'))
		{
			$doi = '10254/' . $tconfig->get('doi_prefix') . $model->get('id') . '.' . $model->get('doi_label');
		}
		else
		{
			$uri = Hubzero\Utility\Uri::getInstance();

			$doi = $uri->getVar('host') . ':' . Config::get('sitename');
		}

		$title[] = 'rft_id=info%3Adoi%2F' . urlencode($doi);

		// Authors
		if (isset($model->revision) && $model->revision != 'dev')
		{
			$authors = $model->contributors('tool');
		}
		else
		{
			$authors = $model->contributors('!submitter');
		}

		if (!empty($authors))
		{
			$i = 0;
			foreach ($authors as $author)
			{
				if (!$author->surname || !$author->givenName)
				{
					$name = explode(' ', $author->name);

					$author->givenName = array_shift($name);
					$author->surname   = array_pop($name);
				}

				$lastname  = $author->surname ? $author->surname : $author->name;
				$firstname = $author->givenName ? $author->givenName : $author->name;

				$title[] = 'rft.aulast=' . urlencode($lastname);
				$title[] = 'rft.aufirst=' . urlencode($firstname);

				if ($i == 0)
				{
					break;
				}

				$i++;
			}
		}

		// Add custom fields
		/*foreach ($model->fields() as $key => $value)
		{
		}*/

		return '<span class="Z3988" title="' . implode('&amp;', $title) . '"></span>' . "\n";
	}
}
