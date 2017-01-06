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

// No direct access.
defined('_HZEXEC_') or die;

/**
 * Vote plugin.
 */
class plgContentVote extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

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
			$starImageOn  = Html::asset('image', 'system/rating_star.png', NULL, NULL, true);
			$starImageOff = Html::asset('image', 'system/rating_star_blank.png', NULL, NULL, true);

			for ($i=0; $i < $rating; $i++)
			{
				$img .= $starImageOn;
			}
			for ($i=$rating; $i < 5; $i++)
			{
				$img .= $starImageOff;
			}
			$html .= '<span class="content_rating">';
			$html .= Lang::txt('PLG_VOTE_USER_RATING', $img, $rating_count);
			$html .= "</span>\n<br />\n";

			if ($view == 'article' && $row->state == 1)
			{
				$uri = Hubzero\Utility\Uri::getInstance();
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
				$html .= Html::input('token');
				$html .= '</div>';
				$html .= '</form>';
			}
		}

		return $html;
	}
}
