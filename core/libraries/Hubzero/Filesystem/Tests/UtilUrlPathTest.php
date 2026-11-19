<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem\Tests;

use Hubzero\Test\Basic;
use Hubzero\Filesystem\Util;

/**
 * The web addresses of files in core and under the document root
 */
class UtilUrlPathTest extends Basic
{
    /**
     * A path in core is addressed under the core folder's name, wherever core is
     *
     * @return  void
     */
    public function testCorePathIsAddressedUnderCore()
    {
        $dir = PATH_CORE . '/components/com_storefront/site';

        $this->assertEquals('/core/components/com_storefront/site', Util::coreUrlPath($dir));
        $this->assertEquals('/core/components/com_storefront/site', Util::urlPath($dir));
    }

    /**
     * A path under the document root outside core is addressed from the root
     *
     * @return  void
     */
    public function testPathUnderRootIsAddressedFromRoot()
    {
        $template = PATH_ROOT . '/app/site/groups/1003/template';

        $this->assertEquals('/app/site/groups/1003/template', Util::urlPath($template));
    }
}
