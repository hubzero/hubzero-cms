<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// add needed css
$this->css('hubpresenter.css');
?>

<div id="hubpresenter-error">
	<div id="title">Oops, We Encountered an Error.</div>
	<p>Use the error messages below to try and resolve the issue. If you are still unable to fix the problem report your problem to the system administrator by entering a <a href="/support/ticket/new">support ticket.</a></p>
	<ol>
		<?php foreach ($this->errors as $error) : ?>
			<li><?php echo $error; ?></li>
		<?php endforeach; ?>
	</ol> 
</div>