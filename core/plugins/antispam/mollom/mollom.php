<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die;

/**
 * Mollom antispam Plugin
 */
class plgAntispamMollom extends \Hubzero\Plugin\Plugin
{
	/**
	 * Instantiate and return a spam detector.
	 *
	 * @return  object  Hubzero\Spam\Detector\DetectorInterface
	 * @since   1.3.2
	 */
	public function onAntispamDetector()
	{
		include_once(__DIR__ . DS . 'Service' . DS . 'Provider.php');

		$mollom = new \Plugins\Antispam\Mollom\Service\Provider();
		$mollom->set('apiPublicKey', $this->params->get('apiPublicKey'))
		       ->set('apiPrivateKey', $this->params->get('apiPrivateKey'))
		       ->set('user_email', User::get('email'))
		       ->set('user_id', User::get('id'))
		       ->set('user_name', User::get('name'));

		return $mollom;
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
		if (!$content) return;

		if (!$this->params->get('learn', 0)) return;

		include_once(__DIR__ . DS . 'Service' . DS . 'Provider.php');

		$mollom = new \Plugins\Antispam\Mollom\Service\Provider();
		$mollom->set('apiPublicKey', $this->params->get('apiPublicKey'))
		       ->set('apiPrivateKey', $this->params->get('apiPrivateKey'))
		       ->set('user_email', User::get('email'))
		       ->set('user_id', User::get('id'))
		       ->set('user_name', User::get('name'));

		$mollom->learn($content, $isSpam);
	}
}
