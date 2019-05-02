<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

if ($this->page->param('mode', 'wiki') == 'knol' && !$this->page->param('hide_authors', 0))
{
	$author = $this->escape(stripslashes($this->page->creator->get('name', Lang::txt('COM_WIKI_UNKNOWN'))));

	$auths = array();
	$auths[] = (in_array($this->page->creator->get('access'), User::getAuthorisedViewLevels()) ? '<a href="' . Route::url($this->page->creator->link()) . '">' . $author . '</a>' : $author);

	foreach ($this->page->authors()->rows() as $auth)
	{
		if ($auth->get('user_id') == $this->page->get('created_by'))
		{
			continue;
		}

		$name = $this->escape(stripslashes($auth->user->get('name')));
		$name = (in_array($auth->user->get('access'), User::getAuthorisedViewLevels()) ? '<a href="' . Route::url($auth->user->link()) . '">' . $name . '</a>' : $name);

		$auths[] = $name;
	}
	?>
	<p class="topic-authors"><?php echo Lang::txt('COM_WIKI_BY_AUTHORS', implode(', ', $auths)); ?></p>
	<?php
}
