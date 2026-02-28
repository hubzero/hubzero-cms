<?php

namespace Hubzero\Framework\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * App facade — provides container access matching HubZero's App::get() API.
 *
 * Delegates to a service that maps HubZero service keys to Laravel
 * container bindings.
 */
class AppFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'hubzero.app';
    }
}
