<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Demo\Site\Controllers;

use Hubzero\Framework\Component\SiteController;
use Hubzero\Framework\Facades\DocumentFacade as Document;
use Hubzero\Framework\Facades\PathwayFacade as Pathway;
use Hubzero\Framework\Facades\RequestFacade as Request;

class Demo extends SiteController
{
    public function displayTask(): void
    {
        Document::setTitle('Demo Component');
        Pathway::append('Demo', '/demo');

        $greeting = Request::getString('name', 'World');

        $this->view
            ->set('greeting', $greeting)
            ->display();
    }
}
