<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Antispam\Spamassassin;

use Hubzero\Plugin\Plugin;

/**
 * Spam Assassin antispam Plugin
 */
class Spamassassin extends Plugin
{
    /**
     * Instantiate and return a spam detector.
     *
     * @return  object  Hubzero\Spam\Detector\DetectorInterface
     * @since   1.3.2
     */
    public function onAntispamDetector()
    {

        $service = new \Plugins\Antispam\SpamAssassin\Service\Provider();

        $service->set('client', $this->params->get('client', 'local'))
            ->set('hostname', $this->params->get('hostname', 'localhost'))
            ->set('port', $this->params->get('port', 783))
            ->set('protocolVersion', $this->params->get('protocolVersion', '1.5'))
            ->set('socket', $this->params->get('socket'))
            ->set('socketPath', $this->params->get('socketPath'))
            ->set('enableZlib', $this->params->get('enableZlib', 0))
            ->set('server', $this->params->get('server', 'http://spamcheck.postmarkapp.com/filter'))
            ->set('verbose', $this->params->get('verbose', 0));

        return $service;
    }

    /**
     * Event for training spam
     *
     * @param  string   $content  The content to train on
     * @param  boolean  $isSpam   If the content is spam or not
     * @since  1.3.2
     */
    public function onAntispamTrain($content, $isSpam)
    {
        if (!$content) {
            return;
        }

        if (!$this->params->get('learn', 0)) {
            return;
        }

        $service = new \Plugins\Antispam\SpamAssassin\Service\Provider();

        $service->set('client', $this->params->get('client', 'local'))
            ->set('hostname', $this->params->get('hostname', 'localhost'))
            ->set('port', $this->params->get('port', 783))
            ->set('protocolVersion', $this->params->get('protocolVersion', '1.5'))
            ->set('socket', $this->params->get('socket'))
            ->set('socketPath', $this->params->get('socketPath'))
            ->set('enableZlib', $this->params->get('enableZlib', 0))
            ->set('server', $this->params->get('server', 'http://spamcheck.postmarkapp.com/filter'))
            ->set('verbose', $this->params->get('verbose', 0));

        $service->learn($content, $isSpam);
    }
}
