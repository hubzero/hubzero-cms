<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Spam\Tests\Mock;

use Hubzero\Spam\Detector\Service;

/**
 * Mock spam detector
 *
 * @codeCoverageIgnore
 */
class Detector extends Service
{
    /**
     * Run content through spam detection
     *
     * @codeCoverageIgnore
     * @param   mixed  $data  Whatever the checker is given
     * @return  bool
     */
    public function detect($data)
    {
        if (!is_array($data) || !isset($data['text'])) {
            return false;
        }

        if (stristr($data['text'], 'spam')) {
            $this->message = 'Text contained the word "spam".';
            return true;
        }

        return false;
    }
}
