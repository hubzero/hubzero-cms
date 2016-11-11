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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
	 * need to check a few parameters and the JHtml behavior will do the rest.
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
