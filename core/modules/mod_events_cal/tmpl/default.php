<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

if ($this->getError())
{
?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php
}
else
{
	echo $this->content;
}
