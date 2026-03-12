{{-- /**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */ --}}
@php
use Hubzero\Facades\Component;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Submenu;

Submenu::addEntry(
    Lang::txt('COM_GROUPS_PAGES'),
    Route::url('index.php?option=com_groups&controller=pages&gid=' . $group->get('cn'), false),
    Request::getCmd('controller', 'pages') == 'pages'
);

Submenu::addEntry(
    Lang::txt('COM_GROUPS_PAGES_CATEGORIES'),
    Route::url('index.php?option=com_groups&controller=categories&gid=' . $group->get('cn'), false),
    Request::getCmd('controller', 'pages') == 'categories'
);

$config = Component::params('com_groups');

if ($group->isSuperGroup() || $config->get('page_modules', 0)) {
    Submenu::addEntry(
        Lang::txt('COM_GROUPS_PAGES_MODULES'),
        Route::url('index.php?option=com_groups&controller=modules&gid=' . $group->get('cn'), false),
        Request::getCmd('controller', 'pages') == 'modules'
    );
}
@endphp
