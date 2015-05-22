<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.Highlight
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_BASE') or die;

/**
 * System plugin to highlight terms.
 *
 * @package     Joomla.Plugin
 * @subpackage  System.Highlight
 * @since       2.5
 */
class PlgSystemHighlight extends \Hubzero\Plugin\Plugin
{
	/**
	 * Method to catch the onAfterDispatch event.
	 *
	 * This is where we setup the click-through content highlighting for.
	 * The highlighting is done with JavaScript so we just
	 * need to check a few parameters and the JHtml behavior will do the rest.
	 *
	 * @return  boolean  True on success
	 *
	 * @since   2.5
	 */
	public function onAfterDispatch()
	{
		// Check that we are in the site application.
		if (App::isAdmin())
		{
			return true;
		}

		// Set the variables
		$extension = Request::getCmd('option', '');

		// Check if the highlighter is enabled.
		if (!Component::params($extension)->get('highlight_terms', 1))
		{
			return true;
		}

		// Check if the highlighter should be activated in this environment.
		if (Document::getType() !== 'html' || Request::getCmd('tmpl', '') === 'component')
		{
			return true;
		}

		// Get the terms to highlight from the request.
		$terms = Request::getVar('highlight', null, 'base64');
		$terms = $terms ? json_decode(base64_decode($terms)) : null;

		// Check the terms.
		if (empty($terms))
		{
			return true;
		}

		// Clean the terms array
		$filter = JFilterInput::getInstance();

		$cleanTerms = array();
		foreach ($terms as $term)
		{
			$cleanTerms[] = htmlspecialchars($filter->clean($term, 'string'));
		}

		// Activate the highlighter.
		Html::behavior('highlighter', $cleanTerms);

		// Adjust the component buffer.
		$buf = Document::getBuffer('component');
		$buf = '<br id="highlighter-start" />' . $buf . '<br id="highlighter-end" />';
		Document::setBuffer($buf, 'component');

		return true;
	}
}
