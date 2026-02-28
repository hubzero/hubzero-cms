<?php

namespace Hubzero\Framework\Facades;

use Illuminate\Support\Facades\Facade;

class NotifyFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'hubzero.notify';
    }
}
