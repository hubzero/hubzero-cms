<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$error = isset($this->error) && $this->error ? $this->error : $this->getError();

if ($error || $this->msg) { ?>
<div id="status-msg" class="status-msg">
	<?php if ($error) { echo '<p class="witherror">' . $error.'</p>';
} elseif ($this->msg) { echo '<p>' . $this->msg . '</p>'; } ?>
</div>
<?php }