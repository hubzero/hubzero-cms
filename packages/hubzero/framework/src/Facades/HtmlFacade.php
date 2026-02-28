<?php

namespace Hubzero\Framework\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * HubZero Html facade — delegates to Hubzero\Html\Builder instance.
 *
 * Html::select('option', ...) → Builder->select('option', ...)
 * → Builder\Select::option(...)
 */
class HtmlFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'hubzero.html';
    }
}
