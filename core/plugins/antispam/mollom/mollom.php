<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_JEXEC') or die;

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
