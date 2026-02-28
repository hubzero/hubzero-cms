<?php

namespace Hubzero\Framework\Facades;

use Illuminate\Support\Facades\Facade;

class DocumentFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'hubzero.document';
    }
}
