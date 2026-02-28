<?php

namespace Hubzero\Framework\Facades;

/**
 * HubZero Date facade — static class for date operations.
 *
 * Bridges Date::of() and Date::toSql() to PHP DateTime.
 */
class DateFacade
{
    public static function of(mixed $date = 'now', mixed $tz = null): DateWrapper
    {
        return new DateWrapper($date, $tz);
    }

    public static function toSql(): string
    {
        return (new DateWrapper('now'))->toSql();
    }
}
