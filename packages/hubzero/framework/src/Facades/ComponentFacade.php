<?php

namespace Hubzero\Framework\Facades;

use Illuminate\Support\Facades\Facade;

class ComponentFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'hubzero.component.facade';
    }
}
