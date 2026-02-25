<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Antispam\Linkrife;

use Hubzero\Plugin\Plugin;

/**
 * Antispam plugin for a LinkRife spam detector
 */
class Linkrife extends Plugin
{
    /**
     * Instantiate and return a spam detector.
     *
     * @return  object  Hubzero\Spam\Detector\DetectorInterface
     * @since   1.3.2
     */
    public function onAntispamDetector()
    {

        $linkrife = new \Plugins\Antispam\LinkRife\Detector();
        $linkrife->setMaxLinkAllowed($this->params->get('linkFrequency', 10));
        $linkrife->setMaxRatio($this->params->get('linkRatio', 40));
        $linkrife->setLinkValidation($this->params->get('linkValidation', 0));

        return $linkrife;
    }
}
