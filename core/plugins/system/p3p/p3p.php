<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * P3P Header Plugin
 */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
class plgSystemP3p extends \Hubzero\Plugin\Plugin
{
    /**
     * Hook for after app initialization
     *
     * @return  void
     */
    public function onAfterInitialise()
    {
        // Get the header
        $header = $this->params->get('header', 'NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM');
        $header = trim($header);

        // Bail out on empty header (why would anyone do that?!)
        if (empty($header)) {
            return;
        }

        // Replace any existing P3P headers in the response
        App::get('response')->headers->set('P3P', 'CP="' . $header . '"', true);
    }
}
