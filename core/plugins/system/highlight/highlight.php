<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * System plugin to highlight terms.
 */
class PlgSystemHighlight extends \Hubzero\Plugin\Plugin
{
	/**
	 * Method to catch the onAfterDispatch event.
	 *
	 * This is where we setup the click-through content highlighting for.
	 * The highlighting is done with JavaScript so we just
	 * need to check a few parameters and the Html behavior will do the rest.
	 *
	 * @return  boolean  True on success
	 */
	public function onAfterDispatch()
	{
		// Check that we are in the site application.
		if (!App::isSite())
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
		$terms = Request::getString('highlight');
		$terms = $terms ? json_decode(base64_decode($terms)) : null;

		// Check the terms.
		if (empty($terms))
		{
			return true;
		}

		// Clean the terms array
		$cleanTerms = array();
		foreach ($terms as $term)
		{
			$cleanTerms[] = htmlspecialchars(Hubzero\Utility\Sanitize::clean($term));
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
