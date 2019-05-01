<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$database = \App::get('db');
switch ($this->filters['sortby'])
{
	case 'date_created':
		$show_date = 1;
		break;
	case 'date_modified':
		$show_date = 2;
		break;
	case 'date':
	default:
		$show_date = 3;
		break;
}

// Get version authors
$pa = new \Components\Publications\Tables\Author( $database );
?>

<ol class="results" id="publications">
<?php

foreach ($this->results as $line)
{

	$authors = $pa->getAuthors($line->version_id);

	// Get parameters
	$params = clone($this->config);
	$rparams = new \Hubzero\Config\Registry( $line->params );
	$params->merge( $rparams );

	// Set the display date
	switch ($show_date)
	{
		case 0:
			$thedate = '';
			break;
		case 1:
			$thedate = $line->created();
			break;
		case 2:
			$thedate = $line->modified();
			break;
		case 3:
			$thedate = $line->published();
			break;
	}

	// Display List of items
	$this->view('item')
	     ->set('option', 'com_publications')
	     ->set('filters', $this->filters)
	     ->set('config', $this->config)
	     ->set('authors', $authors)
	     ->set('line', $line)
	     ->set('thedate', $thedate)
	     ->set('params', $params)
	     ->display();
}
?>
</ol>