<?php

namespace Hubzero\Framework\Facades;

use Illuminate\Support\Facades\Facade;

class PathwayFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'hubzero.pathway';
    }
}
