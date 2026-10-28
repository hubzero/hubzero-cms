<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\System\Massmail;

use Hubzero\Plugin\Plugin;

/**
 * System plugin keeping the mass-mail delivery cron in lock-step with the
 * component's master "sending enabled" switch.
 *
 * The sender cron (plg_cron_massmail::processMassmailQueue) is what actually
 * moves queued mail to Postfix, but it lives in the Cron manager -- a separate
 * screen from the mass-mail Options. That split meant an admin could enable
 * sending and still see nothing go out because the cron job was left disabled
 * (the dashboard's "orange" state). Here we mirror the toggle both ways when
 * the component config is saved: enabling sending enables the cron, disabling
 * sending disables it, so the Options switch is the single control.
 */
class Massmail extends Plugin
{
    /**
     * After the component configuration is saved, sync the delivery cron's
     * enabled state to the mass-mail sending switch.
     *
     * @param   string   $context  Save context (expects 'com_config.component')
     * @param   object   $table    The saved extension record
     * @param   boolean  $isNew    Whether the record is new
     * @return  void
     */
    public function onConfigurationAfterSave($context, $table, $isNew)
    {
        if ($context != 'com_config.component' || !is_object($table)) {
            return;
        }

        if ($table->get('element') != 'com_massmail') {
            return;
        }

        $params  = new \Hubzero\Config\Registry($table->get('params'));
        $enabled = (int) $params->get('sending_enabled', 0) ? 1 : 0;

        // Only the sender cron follows the switch. The postfix-volume collector
        // is read-only monitoring and is intentionally left running.
        $db = \App::get('db');
        $db->setQuery(
            "UPDATE `#__cron_jobs`
			    SET `state` = " . $enabled . "
			  WHERE `plugin` = 'massmail'
			    AND `event`  = 'processMassmailQueue'
			    AND `state` <> " . $enabled
        );
        $db->query();
    }
}
