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

namespace Plugins\Antispam\Mollom\Service;

use Hubzero\Spam\Detector\Service as AbstractService;
use Plugins\Antispam\Mollom\Service\Mollom\Mollom;
use Exception;

require_once __DIR__ . '/Mollom/Mollom.php';

/**
 * Mollom anti-comment spam service
 */
class Provider extends AbstractService
{
	/**
	 * Referrer
	 *
	 * @var string
	 */
	protected $referrer = '';

	/**
	 * User IP
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * User IP
	 *
	 * @var string
	 */
	protected $user_ip = '';

	/**
	 * Permalink
	 *
	 * @var integer
	 */
	protected $user_id = 0;

	/**
	 * Content author
	 *
	 * @var string
	 */
	protected $user_name = '';

	/**
	 * Content author email
	 *
	 * @var string
	 */
	protected $user_email = '';

	/**
	 * Content author URL
	 *
	 * @var string
	 */
	protected $user_url = '';

	/**
	 * Constructor
	 *
	 * @param    mixed $properties
	 * @return   void
	 */
	public function __construct($properties = null)
	{
		$this->set('user_agent', $_SERVER['HTTP_USER_AGENT']);
		if (isset($_SERVER['HTTP_REFERER']))
		{
			$this->set('referrer', $_SERVER['HTTP_REFERER']);
		}

		// This is necessary if the server PHP5 is running on has been set up to run PHP4 and
		// PHP5 concurently and is actually running through a separate proxy al a these instructions:
		// http://www.schlitt.info/applications/blog/archives/83_How_to_run_PHP4_and_PHP_5_parallel.html
		// and http://wiki.coggeshall.org/37.html
		// Otherwise the user_ip appears as the IP address of the PHP4 server passing the requests to the
		// PHP5 one...
		$this->set('user_ip', ($_SERVER['REMOTE_ADDR'] != getenv('SERVER_ADDR') ? $_SERVER['REMOTE_ADDR'] : getenv('HTTP_X_FORWARDED_FOR')));

		if ($properties !== null)
		{
			$this->setProperties($properties);
		}
	}

	/**
	 * Run content through spam detection
	 *
	 * @param   array  $data
	 * @return  bool
	 */
	public function detect($data)
	{
		$this->setValue($data['text']);
		$this->set('user_name', $data['name']);
		$this->set('user_ip', $data['ip']);
		$this->set('user_email', $data['email']);

		if (!$this->getValue())
		{
			return false;
		}

		$mollom = new Mollom(array(
			'publicKey'  => $this->get('apiPublicKey'),
			'privateKey' => $this->get('apiPrivateKey')
		));

		$result = $mollom->checkContent(array(
			'checks'     => array('spam'),
			'postTitle'  => $this->get('title'),
			'postBody'   => $this->getValue(),
			'authorName' => $this->get('user_name'),
			'authorUrl'  => $this->get('user_url'),
			'authorIp'   => $this->get('user_ip'),
			'authorId'   => $this->get('user_id'), // If the author is logged in.
		));

		if (!is_array($result) || !isset($result['id']))
		{
			// Log the error and continue
			error_log('The content moderation system is currently unavailable. Please try again later.');
			return false;
		}

		// Check the final spam classification.
		switch ($result['spamClassification'])
		{
			case 'ham':
				// Do nothing. (Accept content.)
				return false;
			break;

			case 'spam':
				// Discard (block) the form submission.
				$this->setError('Your submission has triggered the spam filter and will not be accepted.');
				return true;
			break;

			case 'unsure':
				/*
				// Require to solve a CAPTCHA to get the post submitted.
				$captcha = $mollom->createCaptcha(array(
					'contentId' => $result['id'],
					'type'      => 'image',
				));
				if (!is_array($captcha) || !isset($captcha['id']))
				{
					print "The content moderation system is currently unavailable. Please try again later.";
					die();
				}
				// Output the CAPTCHA.
				print '<img src="' . $captcha['url'] . '" alt="Type the characters you see in this picture." />';
				print '<input type="text" name="captcha" size="10" value="" autocomplete="off" />';
				*/
				// Re-inject the submitted form values, re-render the form,
				// and ask the user to solve the CAPTCHA.
			break;

			default:
				// If we end up here, Mollom responded with a unknown spamClassification.
				// Normally, this should not happen.
			break;
		}

		return false;
	}
}
