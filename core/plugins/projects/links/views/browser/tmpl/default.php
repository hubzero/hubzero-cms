<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Build url
$route = $this->model->isProvisioned()
	? 'index.php?option=com_publications&task=submit'
	: 'index.php?option=com_projects&alias=' . $this->model->get('alias');

$attached = isset($this->attachments) && count($this->attachments) > 0 ? $this->attachments[0] : '';
$path 	  = $attached ? $attached->path : '';
$serial   = $attached ? $attached->object_id : '';

?>
<div id="import-link">
	<label>
	<input type="text" name="url" size="40" id="parse-url" placeholder="http:// OR doi:" value="<?php echo $path; ?>" />
	</label>
</div>