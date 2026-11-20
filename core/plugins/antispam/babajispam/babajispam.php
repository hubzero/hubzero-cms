<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     * @return  \Plugins\Antispam\Babajispam\Detector  Hubzero\Spam\Detector\DetectorInterface
     * @since   1.3.2
     */
    public function onAntispamDetector()
    {

        return new \Plugins\Antispam\Babajispam\Detector();
    }
}
