<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$database = \App::get('db');

switch ($this->filters['sortby']):
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
endswitch;

// Pre-load authors for every visible publication in one round trip
// (one SQL for the join + one SQL for the deduped #__xprofiles fetch),
// keyed by version_id. The per-row getAuthors() call this replaces was
// running a separate query plus ~k Member::oneOrNew() loads per row,
// which is the N+1 that crashed limit=1000 with OOM.
$pa = new \Components\Publications\Tables\Author($database);
$_versionIds = array();
foreach ($this->results as $line)
{
	if ($line->version_id)
	{
		$_versionIds[] = $line->version_id;
	}
}
$authorsByVersion = $pa->getAuthorsForVersions($_versionIds);
?>

<ol class="results" id="publications">
	<?php
	foreach ($this->results as $line):
		// Get parameters
		$params = clone($this->config);
		$rparams = new \Hubzero\Config\Registry($line->params);
		$params->merge($rparams);

		// Set the display date
		switch ($show_date):
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
		endswitch;

		$authors = isset($authorsByVersion[(int) $line->version_id])
			? $authorsByVersion[(int) $line->version_id]
			: array();

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
	endforeach;
	?>
</ol>