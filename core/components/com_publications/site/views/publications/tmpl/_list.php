<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$database = \App::get('db');

// Get version authors
$pa = new \Components\Publications\Tables\Author( $database );
?>

<ul class="mypubs">
<?php

foreach ($this->results as $row)
{
	// Get version authors
	$authors = $pa->getAuthors($row->version_id);

	$info = array();
	$info[] = Date::of($row->published_up)->toLocal('d M Y');
	$info[] = $row->cat_name;
	$info[] = Lang::txt('COM_PUBLICATIONS_CONTRIBUTORS') . ': '
		. \Components\Publications\Helpers\Html::showContributors( $authors, false, true );

	// Display List of items
	$this->view('_item')
	     ->set('option', 'com_publications')
	     ->set('row', $row)
	     ->set('info', $info)
	     ->display();
}
?>
</ul>