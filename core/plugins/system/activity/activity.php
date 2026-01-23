<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * System plugin for activity
 */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
class plgSystemActivity extends \Hubzero\Plugin\Plugin
{
    /**
     * Log an activity
     *
     * @param   array  $activity
     * @param   array  $recipients
     * @return  void
     */
    public function logActivity($activity, $recipients = array())
    {
        if (!App::isSite() && !App::isApi()) {
            return;
        }

        if (!$activity) {
            return;
        }

        Hubzero\Activity\Log::log($activity, $recipients);
    }
}
