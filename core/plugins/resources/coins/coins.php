<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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

		$genre = 'article';

		$assocs = (array)$this->params->get('type', array());

		foreach ($assocs as $assoc)
		{
			if ($assoc->resource == $model->get('type'))
			{
				$genre = $assoc->genre;
				break;
			}
		}

		$fields = $model->fields();

		$title = array(
			'ctx_ver=Z39.88-2004',
			'rft.genre=' . $genre,
			'rft.date=' . Date::of($model->date)->toLocal('Y')
		);

		$items = array(
			'issn'  => 'issn',
			'eissn' => 'issn',
			'isbn'  => 'isbn'
		);

		switch ($genre)
		{
			case 'book':
				$title[] = 'rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Abook';  //info:ofi/fmt:kev:mtx:journal
				$title[] = 'rft.title=' . urlencode($model->title);
			break;

			case 'bookitem':
				$title[] = 'rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Abook';  //info:ofi/fmt:kev:mtx:journal
				$title[] = 'rft.atitle=' . urlencode($model->title);

				$items['booktitle'] = 'title';
				$items['pages']     = 'pages';
			break;

			case 'article':
			default:
				$title[] = 'rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Ajournal';  //info:ofi/fmt:kev:mtx:journal
				$title[] = 'rft.atitle=' . urlencode($model->title);

				$items['series'] = 'jtitle';
				$items['volume'] = 'volume';
				$items['issue']  = 'issue';
				$items['pages']  = 'pages';
			break;
		}

		foreach ($items as $item => $coin)
		{
			if (isset($fields[$item]) && $fields[$item])
			{
				$title[] = 'rft.' . $coin . '=' . urlencode($fields[$item]);
			}
		}

		// DOI/identifier
		$doi = null;

		if (isset($fields['url']) && $fields['url'])
		{
			$doi = $fields['url'];
		}
		elseif (isset($fields['doi']) && $fields['doi'])
		{
			$doi = $fields['doi'];
			if (substr($doi, 0, strlen('http')) != 'http')
			{
				$doi = 'https://doi.org/' . ltrim($doi, '/');
			}
		}

		if (!$doi && $model->isTool())
		{
			// Get contribtool params
			$tconfig = Component::params('com_tools');

			if ($model->doi && ($model->doi_shoulder || $tconfig->get('doi_shoulder')))
			{
				$doi = 'https://doi.org/' . ($model->doi_shoulder ? $model->doi_shoulder : $tconfig->get('doi_shoulder')) . '/' . strtoupper($model->doi);
			}
		}

		if ($this->params->get('payload_url'))
		{
			$firstchild = $model->children()
				->whereEquals('standalone', 0)
				->whereEquals('published', \Components\Resources\Models\Entry::STATE_PUBLISHED)
				->order('ordering', 'asc')
				->limit(1)
				->row();

			if ($firstchild && $firstchild->id)
			{
				$doi = Components\Resources\Helpers\Html::processPath('com_resources', $firstChild, $model->id, '');
				if (substr($doi, 0, strlen('http')) != 'http')
				{
					$doi = Route::url($doi, true, 1);
				}
			}
		}

		if (!$doi)
		{
			$doi = Route::url($model->link(), true, 1);
		}

		if (!$doi)
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
			$auths = array();

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

				$auths[] = urlencode($lastname) . ', ' . urlencode($firstname);
			}

			if (!empty($auths))
			{
				$title[] = 'rft.au=' . implode(';', $auths);
			}
		}

		return '<span class="Z3988" title="' . implode('&amp;', $title) . '"></span>' . "\n";
	}
}
