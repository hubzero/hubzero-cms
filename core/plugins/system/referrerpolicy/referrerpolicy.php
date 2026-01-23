<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Referrer Policy Header Plugin
 */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
class plgSystemReferrerpolicy extends \Hubzero\Plugin\Plugin
{
    /**
     * Hook for after app initialization
     *
     * @return  void
     */
    public function onAfterInitialise()
    {
        // Get the policy
        $policy = $this->params->get('policy', 'same-origin');
        $policy = trim($policy);

        // Bail out on empty policy
        if (empty($policy)) {
            return;
        }

        // Add to the headers
        App::get('response')->headers->set('Referrer-Policy', $policy, true);
    }
}
