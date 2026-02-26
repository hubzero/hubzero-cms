<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\App;

class Dataviewer extends AbstractComponent
{
    protected function execute(): void
    {
        // The admin application gates only on core.login.admin, not per component, so
        // what separates one component's managers from another's is this test at the
        // entry file -- 47 of the 55 admin entries make it. This one did not, and the
        // component's own authorized() returns true for everyone whenever
        // access_limit_to_group is unset, which is the default. Any account that can
        // reach the admin area at all could therefore use the data viewer's admin.
        if (!\User::authorise('core.manage', 'com_dataviewer')) {
            \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        DvConfig::init();

        $document = App::get('document');
        $document->addCustomTag('<meta name="csrf-token" content="' . DB_RID . '" />');
        $document->addStyleSheet(DB_PATH . '/html/smoothness/jquery-ui.css');
        $document->addStyleSheet(DB_PATH . '/html/main.css');
        $document->addScript(DB_PATH . '/html/main.js');
        $document->setTitle(DvConfig::$conf['app_title']);

        Controller::dispatch();

        umask(DvConfig::$conf['sys_umask']);
    }
}
