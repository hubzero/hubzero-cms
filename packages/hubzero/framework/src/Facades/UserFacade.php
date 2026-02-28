<?php

namespace Hubzero\Framework\Facades;

use Illuminate\Support\Facades\Facade;

class UserFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'hubzero.user';
    }
}
