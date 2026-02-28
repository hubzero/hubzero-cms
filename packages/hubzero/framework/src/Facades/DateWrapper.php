<?php

namespace Hubzero\Framework\Facades;

use DateTime;
use DateTimeZone;

/**
 * Wraps PHP DateTime to provide HubZero's Date API.
 */
class DateWrapper
{
    private DateTime $datetime;

    public function __construct(mixed $date = 'now', mixed $tz = null)
    {
        $timezone = null;
        if ($tz instanceof DateTimeZone) {
            $timezone = $tz;
        } elseif (is_string($tz) && $tz !== '') {
            $timezone = new DateTimeZone($tz);
        }

        if ($date instanceof DateTime) {
            $this->datetime = clone $date;
            if ($timezone) {
                $this->datetime->setTimezone($timezone);
            }
        } else {
            $this->datetime = new DateTime((string) $date, $timezone);
        }
    }

    public function format(string $format = 'Y-m-d H:i:s'): string
    {
        return $this->datetime->format($format);
    }

    public function toSql(): string
    {
        return $this->datetime->format('Y-m-d H:i:s');
    }

    public function toLocal(string $format = 'Y-m-d H:i:s'): string
    {
        return $this->datetime->format($format);
    }

    public function toUnix(): int
    {
        return $this->datetime->getTimestamp();
    }

    public function toISO8601(): string
    {
        return $this->datetime->format('c');
    }

    public function relative(?string $unit = null): string
    {
        $now = new DateTime('now', $this->datetime->getTimezone());
        $diff = $now->diff($this->datetime);

        if ($diff->y > 0) {
            return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
        }
        if ($diff->m > 0) {
            return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
        }
        if ($diff->d > 0) {
            return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
        }
        if ($diff->h > 0) {
            return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        }
        if ($diff->i > 0) {
            return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        }

        return 'just now';
    }

    public function __toString(): string
    {
        return $this->toSql();
    }
}
