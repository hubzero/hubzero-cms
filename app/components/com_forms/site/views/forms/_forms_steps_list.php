<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$prereqs = $this->prereqs
	->order('order', 'asc')
	->rows();
$userId = User::get('id');
?>

<ol>
	<?php
		foreach ($prereqs as $prereq):

			$this->view('_forms_step')
				->set('prereq', $prereq)
				->set('userId', $userId)
				->display();

		endforeach;
	?>
</ol>
