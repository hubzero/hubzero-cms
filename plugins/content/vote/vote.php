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

// No direct access.
defined('_JEXEC') or die;

/**
 * Vote plugin.
 */
class plgContentVote extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param  object  $subject  The object to observe
	 * @param  array   $config   An array that holds the plugin configuration
	 * @since  1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Prepare content
	 *
	 * @param   string   $context  The context of the content being passed to the plugin.
	 * @param   object   $article  The article object.  Note $article->text is also available
	 * @param   object   $params   The article params
	 * @param   integer  $page     The 'page' number
	 * @return  void
	 */
	public function onContentBeforeDisplay($context, &$row, &$params, $page=0)
	{
		$html = '';

		if ($params->get('show_vote'))
		{
			$rating = intval(@$row->rating);
			$rating_count = intval(@$row->rating_count);

			$view = Request::getString('view', '');
			$img = '';

			// look for images in template if available
			$starImageOn  = JHtml::_('image', 'system/rating_star.png', NULL, NULL, true);
			$starImageOff = JHtml::_('image', 'system/rating_star_blank.png', NULL, NULL, true);

			for ($i=0; $i < $rating; $i++)
			{
				$img .= $starImageOn;
			}
			for ($i=$rating; $i < 5; $i++)
			{
				$img .= $starImageOff;
			}
			$html .= '<span class="content_rating">';
			$html .= Lang::txt( 'PLG_VOTE_USER_RATING', $img, $rating_count );
			$html .= "</span>\n<br />\n";

			if ($view == 'article' && $row->state == 1)
			{
				$uri = JFactory::getURI();
				$uri->setQuery($uri->getQuery() . '&hitcount=0');

				$html .= '<form method="post" action="' . htmlspecialchars($uri->toString()) . '">';
				$html .= '<div class="content_vote">';
				$html .= Lang::txt('PLG_VOTE_POOR');
				$html .= '<input type="radio" title="' . Lang::txt('PLG_VOTE_VOTE', '1') . '" name="user_rating" value="1" />';
				$html .= '<input type="radio" title="' . Lang::txt('PLG_VOTE_VOTE', '2') . '" name="user_rating" value="2" />';
				$html .= '<input type="radio" title="' . Lang::txt('PLG_VOTE_VOTE', '3') . '" name="user_rating" value="3" />';
				$html .= '<input type="radio" title="' . Lang::txt('PLG_VOTE_VOTE', '4') . '" name="user_rating" value="4" />';
				$html .= '<input type="radio" title="' . Lang::txt('PLG_VOTE_VOTE', '5') . '" name="user_rating" value="5" checked="checked" />';
				$html .= Lang::txt('PLG_VOTE_BEST');
				$html .= '&#160;<input class="button" type="submit" name="submit_vote" value="'. Lang::txt('PLG_VOTE_RATE') .'" />';
				$html .= '<input type="hidden" name="task" value="article.vote" />';
				$html .= '<input type="hidden" name="hitcount" value="0" />';
				$html .= '<input type="hidden" name="url" value="'.  htmlspecialchars($uri->toString()) .'" />';
				$html .= JHtml::_('form.token');
				$html .= '</div>';
				$html .= '</form>';
			}
		}

		return $html;
	}
}
