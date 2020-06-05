<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();
?>
<div class="supportingdocs" id="supportingdocs">
	<h3>
		<?php echo Lang::txt('PLG_PUBLICATION_SUPPORTINGDOCS'); ?>
	</h3>

	<?php
	// Get elements in primary and supporting role
	$prime    = $this->publication->_curationModel->getElements(1);
	$second   = $this->publication->_curationModel->getElements(2);
	$elements = array_merge($prime, $second);

	// Get attachment type model
	$database = App::get('db');

	$attModel = new \Components\Publications\Models\Attachments($database);

	if ($elements)
	{
		// Draw list
		$list = $attModel->listItems(
			$elements,
			$this->publication,
			$this->authorized
		);
		echo $list ? $list : '<p class="noresults">' . Lang::txt('PLG_PUBLICATION_SUPPORTINGDOCS_NONE_FOUND') . '</p>';
	}
	else
	{
		?>
		<p class="noresults"><?php echo Lang::txt('PLG_PUBLICATION_SUPPORTINGDOCS_NONE_FOUND'); ?></p>
		<?php
	}
	?>
</div><!-- / .supportingdocs -->
