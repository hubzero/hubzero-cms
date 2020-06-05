<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

/**
 * Content Component HTML Helper
 */
class HtmlIcon
{
	/**
	 * Display a 'create article' icon
	 *
	 * @param   object  $category
	 * @param   object  $params    The article parameters
	 * @return  string  The HTML for the article edit icon.
	 */
	public static function create($category, $params)
	{
		$url = 'index.php?option=com_content&task=article.add&return=' . base64_encode(urlencode(Request::current(true))) . '&a_id=0&catid=' . $category->id;

		$button =  '<a class="icon-add" href="' . Route::url($url) . '">' . Lang::txt('JNEW') . '</a>';

		$output = '<span class="hasTip" title="' . Lang::txt('COM_CONTENT_CREATE_ARTICLE') . '">' . $button . '</span>';
		return $output;
	}

	/**
	 * Display an email icon
	 *
	 * @param   object  $article  The article in question.
	 * @param   object  $params   The article parameters
	 * @param   array   $attribs  Not used??
	 * @return  string  The HTML for the article edit icon.
	 */
	public static function email($article, $params, $attribs = array())
	{
		$path = Component::path('com_mailto') . '/site/helpers/mailto.php';

		$output = '';

		if (file_exists($path))
		{
			require_once $path;

			$base     = Hubzero\Utility\Uri::getInstance()->toString(array('scheme', 'host', 'port'));
			$template = App::get('template')->template;
			$link     = $base . Route::url(Components\Content\Site\Helpers\Route::getArticleRoute($article->slug, $article->catid, $article->language), false);
			$url      = 'index.php?option=com_mailto&tmpl=component&template=' . $template . '&link=' . Components\Mailto\Site\Helpers\Mailto::addLink($link);

			$status = 'width=400,height=350,menubar=yes,resizable=yes';

			$attribs['title']   = Lang::txt('JGLOBAL_EMAIL');
			$attribs['onclick'] = "window.open(this.href,'win2','" . $status . "'); return false;";

			$output = '<a class="icon-email" href="' . Route::url($url) . '" ' . Hubzero\Utility\Arr::toString($attribs) . '>' . Lang::txt('JGLOBAL_EMAIL') . '</a>';
		}

		return $output;
	}

	/**
	 * Display an edit icon for the article.
	 *
	 * This icon will not display in a popup window, nor if the article is trashed.
	 * Edit access checks must be performed in the calling code.
	 *
	 * @param   object  $article  The article in question.
	 * @param   object  $params   The article parameters
	 * @param   array   $attribs  Not used??
	 * @return  string  The HTML for the article edit icon.
	 */
	public static function edit($article, $params, $attribs = array())
	{
		// Initialise variables.
		$userId = User::get('id');

		// Ignore if in a popup window.
		if ($params && $params->get('popup'))
		{
			return;
		}

		// Ignore if the state is negative (trashed).
		if ($article->state < 0)
		{
			return;
		}

		Html::behavior('tooltip');

		// Show checked_out icon if the article is checked out by a different user
		if (property_exists($article, 'checked_out')
		 && property_exists($article, 'checked_out_time')
		 && $article->checked_out > 0
		 && $article->checked_out != User::get('id'))
		{
			$checkoutUser = User::getInstance($article->checked_out);
			$button  = Html::asset('icon', 'lock');
			$date    = Date::of($article->checked_out_time)->toLocal();
			$tooltip = Lang::txt('JLIB_HTML_CHECKED_OUT') . ' :: ' . Lang::txt('COM_CONTENT_CHECKED_OUT_BY', $checkoutUser->name) . ' <br /> ' . $date;

			return '<span class="hasTip" title="' . htmlspecialchars($tooltip, ENT_COMPAT, 'UTF-8') . '">' . $button . '</span>';
		}

		$url = 'index.php?option=com_content&task=article.edit&a_id=' . $article->id . '&return=' . base64_encode(urlencode(Request::current(true)));
		$cls = $article->state ? 'icon-edit edit' : 'icon-edit edit unpublished';
		if (strtotime($article->publish_up) > strtotime(Date::of('now')))
		{
			$cls = 'icon-edit edit unpublished';
		}
		$text = Lang::txt('JGLOBAL_EDIT');

		if ($article->state == 0)
		{
			$overlib = Lang::txt('JUNPUBLISHED');
		}
		else
		{
			$overlib = Lang::txt('JPUBLISHED');
		}

		$date = Date::of($article->created)->toLocal();
		$author = $article->created_by_alias ? $article->created_by_alias : $article->author;

		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
		$overlib .= '&lt;br /&gt;';
		$overlib .= Lang::txt('COM_CONTENT_WRITTEN_BY', htmlspecialchars($author, ENT_COMPAT, 'UTF-8'));

		$button = '<a class="' . $cls . '" href="' . Route::url($url) . '">' . $text . '</a>';

		$output = '<span class="hasTip" title="' . Lang::txt('COM_CONTENT_EDIT_ITEM') . ' :: ' . $overlib . '">' . $button . '</span>';

		return $output;
	}

	/**
	 * Icon for print popup
	 *
	 * @param   object  $article
	 * @param   object  $params
	 * @param   array   $attribs
	 * @return  string
	 */
	public static function print_popup($article, $params, $attribs = array())
	{
		$url  = Components\Content\Site\Helpers\Route::getArticleRoute($article->slug, $article->catid, $article->language);
		$url .= '&tmpl=component&print=1&layout=default&page=' . @ $request->limitstart;

		$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';

		$text = Lang::txt('JGLOBAL_ICON_SEP') . '&#160;' . Lang::txt('JGLOBAL_PRINT') . '&#160;' . Lang::txt('JGLOBAL_ICON_SEP');

		$attribs['title']   = Lang::txt('JGLOBAL_PRINT');
		$attribs['onclick'] = "window.open(this.href,'win2','" . $status . "'); return false;";
		$attribs['rel']     = 'nofollow';

		return '<a href="' . Route::url($url) . '" ' . Hubzero\Utility\Arr::toString($attribs) . '>' . $text . '</a>';
	}

	/**
	 * Icon for printing the page
	 *
	 * @param   object  $article
	 * @param   object  $params
	 * @param   array   $attribs
	 * @return  string
	 */
	public static function print_screen($article, $params, $attribs = array())
	{
		$text = Lang::txt('JGLOBAL_ICON_SEP') . '&#160;' . Lang::txt('JGLOBAL_PRINT') . '&#160;' . Lang::txt('JGLOBAL_ICON_SEP');

		return '<a href="#" onclick="window.print();return false;">' . $text . '</a>';
	}
}
