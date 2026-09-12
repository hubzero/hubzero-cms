<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Remove the resources Windows tools plugin
 *
 * It generated an invoke URL for a simulation tool delivered to the browser
 * as a Windows session, looked up in the middleware database. The platform
 * does not do that any more, the resource type it answered for has been
 * retired, and the plugin's files are gone from the tree - so the row in
 * #__extensions is an entry pointing at nothing, which the plugin dispatcher
 * has to step over on every request that touches a resource.
 *
 * There is no down(). Bringing the row back would name a plugin that is not
 * there; the way back is the revert that puts the files back, and its own
 * install migration will do this again.
 **/
class Migration20260912093000PlgResourcesWindowstools extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->deletePluginEntry('resources', 'windowstools');
    }
}
