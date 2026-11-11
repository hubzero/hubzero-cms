<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\App;
use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{results: mixed} $this
 */

$database = App::get('db');

// Get version authors
$pa = new \Components\Publications\Tables\Author($database);
?>

<ul class="mypubs">
<?php

foreach ($this->results as $row) {
    // Get version authors
    $authors = $pa->getAuthors($row->version_id);

    $info = [];
    $info[] = Date::of($row->published_up)->toLocal('d M Y');
    $info[] = $row->cat_name;
    $info[] = Lang::txt('COM_PUBLICATIONS_CONTRIBUTORS') . ': '
        . \Components\Publications\Helpers\Html::showContributors($authors, false, true);

    // Display List of items
    $this
         ->view('_item')
         ->set('option', 'com_publications')
         ->set('row', $row)
         ->set('info', $info)
         ->display();
}
?>
</ul>