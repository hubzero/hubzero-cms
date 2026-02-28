<?php

namespace Hubzero\Framework\Facades;

use Illuminate\Support\Facades\Facade;

class RequestFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'hubzero.request';
    }
}
