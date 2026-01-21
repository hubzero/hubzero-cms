<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die;

/**
 * Vote plugin.
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 * @phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
 * @phpcs:disable PSR2.Classes.PropertyDeclaration.Underscore
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
     * @param   object   $row      The row object.
     * @param   object   $params   The article params.
     * @param   integer  $page     The 'page' number.
     * @return  void
     */
    public function onContentBeforeDisplay($context, &$row, &$params, $page = 0)
    {
        $html = '';

        if ($params->get('show_vote')) {
            $rating = intval(@$row->rating);
            $rating_count = intval(@$row->rating_count);

            $view = Request::getString('view', '');
            $img = '';

            // look for images in template if available
            $starImageOn  = Html::asset('icon', 'star');
            $starImageOff = Html::asset('icon', 'star-empty');

            for ($i = 0; $i < $rating; $i++) {
                $img .= $starImageOn;
            }
            for ($i = $rating; $i < 5; $i++) {
                $img .= $starImageOff;
            }
            $html .= '<span class="content_rating">';
            $html .= Lang::txt('PLG_VOTE_USER_RATING', $img, $rating_count);
            $html .= "</span>\n<br />\n";

            if ($view == 'article' && $row->state == 1) {
                $uri = Hubzero\Utility\Uri::getInstance();
                $uri->setQuery($uri->getQuery() . '&hitcount=0');

                $action = htmlspecialchars($uri->toString());
                $html .= '<form method="post" action="' . $action . '">';
                $html .= '<div class="content_vote">';
                $html .= Lang::txt('PLG_VOTE_POOR');
                for ($i = 1; $i <= 5; $i++) {
                    $title = Lang::txt('PLG_VOTE_VOTE', $i);
                    $checked = ($i == 5) ? ' checked="checked"' : '';
                    $html .= '<input type="radio" title="' . $title . '"';
                    $html .= ' name="user_rating" value="' . $i . '"' . $checked . ' />';
                }
                $html .= Lang::txt('PLG_VOTE_BEST');
                $rateLabel = Lang::txt('PLG_VOTE_RATE');
                $html .= '&#160;<input class="button" type="submit" name="submit_vote"';
                $html .= ' value="' . $rateLabel . '" />';
                $html .= '<input type="hidden" name="task" value="article.vote" />';
                $html .= '<input type="hidden" name="hitcount" value="0" />';
                $html .= '<input type="hidden" name="url" value="' .  htmlspecialchars($uri->toString()) . '" />';
                $html .= Html::input('token');
                $html .= '</div>';
                $html .= '</form>';
            }
        }

        return $html;
    }
}
