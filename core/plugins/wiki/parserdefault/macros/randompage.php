<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Wiki macro class for displaying a random page
 */
class RandomPageMacro extends WikiMacro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return  array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Generates a link to a random page.';
		$txt['html'] = '<p>Generates a link to a random page.</p>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return  string
	 */
	public function render()
	{
		$row = \Components\Wiki\Models\Page::all()
			->whereEquals('scope', $this->domain)
			->whereEquals('scope_id', $this->domain_id)
			->whereEquals('state', \Components\Wiki\Models\Page::STATE_PUBLISHED)
			->order('rand()')
			->row();

		// Build and return the link
		return '<a href="' . Route::url($row->link()) . '">' . $row->title . '</a>';
	}
}
