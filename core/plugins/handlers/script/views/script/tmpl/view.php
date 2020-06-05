<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('script.css');
$this->js('highlighter/shCore.js')
     ->js('highlighter/shBrush' . ucfirst($this->ext) . '.js')
     ->js('script.js');
?>
<div class="file-preview script">
	<div class="file-preview-code">
		<pre name="code" class="<?php echo $this->ext; ?>:nocontrols"><?php
			$contents = trim($this->file->read());
			$contents = str_replace(array('<', '>'), array('&lt;', '&gt;'), $contents);

			echo $contents;
			?></pre>
	</div>
</div>