<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Plugins\Content\Mollom\Service;

use Hubzero\Antispam\Adapter\AbstractAdapter;
use Plugins\Content\Mollom\Service\Mollom;
use Exception;

require_once __DIR__ . '/Mollom/Mollom.php';

/**
 * Mollom anti-comment spam service
 */
class Provider extends AbstractAdapter
{
	/**
	 * Referrer
	 *
	 * @var string
	 */
	private $referrer = '';

	/**
	 * User IP
	 *
	 * @var string
	 */
	private $title = '';

	/**
	 * User IP
	 *
	 * @var string
	 */
	private $user_ip = '';

	/**
	 * Permalink
	 *
	 * @var integer
	 */
	private $user_id = 0;

	/**
	 * Content author
	 *
	 * @var string
	 */
	private $user_name = '';

	/**
	 * Content author email
	 *
	 * @var string
	 */
	private $user_email = '';

	/**
	 * Content author URL
	 *
	 * @var string
	 */
	private $user_url = '';

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
	 * Tests for spam.
	 *
	 * @param    string $value Conent to test
	 * @return   bool True if the comment is spam, false if not
	 */
	public function isSpam($value = null)
	{
		if ($value)
		{
			$this->setValue($value);
		}

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
			throw new Exception('The content moderation system is currently unavailable. Please try again later.');
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