<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die;

/**
 * Akismet antispam Plugin
 */
class plgAntispamAkismet extends \Hubzero\Plugin\Plugin
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

		if (!$this->params->get('apiKey')) return;

		$akismet = new \Plugins\Antispam\Akismet\Service\Provider();
		$akismet->set('apiKey', $this->params->get('apiKey'))
		        ->set('apiPort', $this->params->get('apiPort', 80))
		        ->set('akismetServer', $this->params->get('akismetServer', 'rest.akismet.com'))
		        ->set('akismetVersion', $this->params->get('akismetVersion', '1.1'));

		return $akismet;
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

		$akismet = new \Plugins\Antispam\Akismet\Service\Provider();
		$akismet->set('apiKey', $this->params->get('apiKey'))
		        ->set('apiPort', $this->params->get('apiPort', 80))
		        ->set('akismetServer', $this->params->get('akismetServer', 'rest.akismet.com'))
		        ->set('akismetVersion', $this->params->get('akismetVersion', '1.1'));

		$akismet->learn($content, $isSpam);
	}
}
