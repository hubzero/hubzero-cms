<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Antispam\Babajispam;

use Hubzero\Plugin\Plugin;

/**
 * Babajispam Anti-spam Plugin
 */
class Babajispam extends Plugin
{
    /**
     * Instantiate and return a spam detector.
     *
     * @return  object  Hubzero\Spam\Detector\DetectorInterface
     * @since   1.3.2
     */
    public function onAntispamDetector()
    {

        return new \Plugins\Antispam\Babajispam\Detector();
    }
}
