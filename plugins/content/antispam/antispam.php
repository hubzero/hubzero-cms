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

// No direct access
defined('_JEXEC') or die;

/**
 * Antispam Content Plugin
 */
class plgContentAntispam extends JPlugin
{
	/**
	 * Finder before save content method
	 * Article is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @param  string   $context  The context of the content passed to the plugin (added in 1.6)
	 * @param  object   $article  A JTableContent object
	 * @param  boolean  $isNew    If the content is just about to be created
	 * @since  2.5
	 */
	public function onContentBeforeSave($context, $article, $isNew)
	{
		if ($article instanceof \Hubzero\Base\Object)
		{
			$key = $this->_key($context);

			$content = ltrim($article->get($key));
		}
		else if (is_object($article) || is_array($article))
		{
			return;
		}
		else
		{
			$content = $article;
		}

		if (!$content) return;

		include_once(__DIR__ . '/Service/Provider.php');

		$service = new \Hubzero\Antispam\Service(new \Plugins\Content\Antispam\Service\Provider);

		$service->set('linkFrequency', $this->params->get('linkFrequency', 5))
		        ->set('blacklist', $this->params->get('blacklist'))
		        ->set('badwords', $this->params->get('badwords', 'viagra, pharmacy, xanax, phentermine, dating, ringtones, tramadol, hydrocodone, levitra, '
				. 'ambien, vicodin, fioricet, diazepam, cash advance, free online, online gambling, online prescriptions, '
				. 'debt consolidation, baccarat, loan, slots, credit, mortgage, casino, slot, texas holdem, teen nude, '
				. 'orgasm, gay, fuck, crap, shit, asshole, cunt, fucker, fuckers, motherfucker, fucking, milf, cocksucker, '
				. 'porno, videosex, sperm, hentai, internet gambling, kasino, kasinos, poker, lottery, texas hold em, '
				. 'texas holdem, fisting'));

		if ($service->isSpam($content))
		{
			if ($message = $this->params->get('message'))
			{
				\JFactory::getApplication()->enqueueMessage($message, 'error');
			}
			return false;
		}
	}

	/**
	 * Check if the context provided the content field name as
	 * it may vary between models.
	 *
	 * @param   string  $context  A dot-notation string
	 * @return  string
	 */
	private function _key($context)
	{
		$parts = explode('.', $context);
		$key = 'content';
		if (isset($parts[2]))
		{
			$key = $parts[2];
		}
		return $key;
	}
}
