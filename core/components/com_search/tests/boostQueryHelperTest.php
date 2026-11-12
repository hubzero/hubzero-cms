<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests;

use Components\Search\Helpers\BoostQueryHelper as Helper;
use Components\Search\Tests\Traits\CanMock;
use Hubzero\Test\Basic;
use Component;

class BoostQueryHelperTest extends Basic
{
    use CanMock;

    public function testGetAllQueriesInvokesOne(): void
    {
        $boosts = [1, 1, 1];
        $factoryMock = $this->mock([
            'class' => 'BoostQueries',
            'methods' => ['one'],
        ]);
        $ormMock = $this->mock([
            'class' => 'Boost',
            'methods' => ['all' => $boosts],
        ]);
        $helper = new Helper([
            'boosts' => $ormMock,
            'queries' => $factoryMock,
        ]);

        $factoryMock
            ->expects($this->once())
            ->method('one')
            ->with(['boosts' => $boosts]);

        $boostQueries = $helper->getAllQueries();
    }
}
