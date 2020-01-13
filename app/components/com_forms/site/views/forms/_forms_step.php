<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$prereq = $this->prereq;
$userId = $this->userId;

$completed = $prereq->acceptedFor($userId);
$checkedStatus = $completed ? 'checked' : '';
?>

<li>
	<span>
		<input type="checkbox" disabled <?php echo $checkedStatus; ?>>
	</span>
	|
	<span>
		<?php
			$this->view('_prerequisite_link')
				->set('prereq', $prereq)
				->display();
		?>
	</span>
</li>
