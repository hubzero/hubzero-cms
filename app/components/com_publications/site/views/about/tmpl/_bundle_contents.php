<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>

<?php foreach ($this->contents as $element): ?>
		<?php if ($element['isDirectory']):
			$this->view('_bundle_directory')
				->set('directory', $element)
				->display();
		else:
			$this->view('_bundle_file')
				->set('file', $element)
				->display();
		endif; ?>
<?php endforeach;
