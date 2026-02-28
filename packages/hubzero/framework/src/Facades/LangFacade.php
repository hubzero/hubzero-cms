<?php

namespace Hubzero\Framework\Facades;

use Illuminate\Support\Facades\Facade;

class LangFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'hubzero.lang';
    }
}
