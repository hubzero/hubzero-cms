<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Cron plugin that feeds queued mass-mail messages to Postfix under a rate
 * budget. The heavy lifting lives in the component's Sender helper; this is a
 * thin wrapper that honors the master "sending enabled" switch.
 */
class plgCronMassmail extends \Hubzero\Plugin\Plugin
{
    /**
     * Return a list of events
     *
     * @return  object
     */
    public function onCronEvents()
    {
        $this->loadLanguage();

        $obj = new stdClass();
        $obj->plugin = $this->_name;
        $obj->events = array(
            array(
                'name'   => 'processMassmailQueue',
                'label'  => Lang::txt('PLG_CRON_MASSMAIL_PROCESS_QUEUE'),
                'params' => ''
            ),
            array(
                'name'   => 'collectPostfixVolume',
                'label'  => Lang::txt('PLG_CRON_MASSMAIL_COLLECT'),
                'params' => ''
            )
        );

        return $obj;
    }

    /**
     * Collect Postfix outbound volume into hourly/daily rollups for the
     * dashboard. Read-only monitoring -- runs regardless of the send switch.
     *
     * @param   object   $job  \Components\Cron\Models\Job
     * @return  boolean
     */
    public function collectPostfixVolume(\Components\Cron\Models\Job $job)
    {
        require_once Component::path('com_massmail') . DS . 'helpers' . DS . 'collector.php';

        \Components\Massmail\Helpers\PostfixCollector::collect();

        return true;
    }

    /**
     * Feed a rate-limited batch of queued mass-mail messages.
     *
     * @param   object   $job  \Components\Cron\Models\Job
     * @return  boolean
     */
    public function processMassmailQueue(\Components\Cron\Models\Job $job)
    {
        require_once Component::path('com_massmail') . DS . 'models' . DS . 'mailing.php';
        require_once Component::path('com_massmail') . DS . 'models' . DS . 'recipient.php';
        require_once Component::path('com_massmail') . DS . 'models' . DS . 'attachment.php';
        require_once Component::path('com_massmail') . DS . 'models' . DS . 'status.php';
        require_once Component::path('com_massmail') . DS . 'helpers' . DS . 'sender.php';

        $params = Component::params('com_massmail');

        // Master kill-switch: nothing leaves until an admin enables sending.
        if (!(int) $params->get('sending_enabled', 0)) {
            return true;
        }

        \Components\Massmail\Helpers\Sender::run(false);

        return true;
    }
}
