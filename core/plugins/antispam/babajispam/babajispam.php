<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable PSR1.Files.SideEffects


/**
 * Babajispam Anti-spam Plugin
 */
// phpcs:ignore PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ValidClassName.NotCamelCaps
class plgAntispamBabajispam extends \Hubzero\Plugin\Plugin
{
    /**
     * Instantiate and return a spam detector.
     *
     * @return  object  Hubzero\Spam\Detector\DetectorInterface
     * @since   1.3.2
     */
    public function onAntispamDetector()
    {
        include_once __DIR__ . DS . 'Detector.php';

        return new \Plugins\Antispam\Babajispam\Detector();
    }
}
