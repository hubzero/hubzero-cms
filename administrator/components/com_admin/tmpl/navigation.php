<?php
/**
 * @version		$Id: navigation.php 8630 2007-08-30 04:30:51Z robs $
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

?>
<div class="submenu-box">
	<div class="submenu-pad">
		<ul id="submenu" class="information">
			<li>
				<a id="site" class="active">
					<?php echo JText::_( 'System Info' ); ?></a>
			</li>
			<li>
				<a id="phpsettings">
					<?php echo JText::_( 'PHP Settings' ); ?></a>
			</li>
			<li>
				<a id="config">
					<?php echo JText::_( 'Configuration File' ); ?></a>
			</li>
			<li>
				<a id="directory">
					<?php echo JText::_( 'Directory Permissions' ); ?></a>
			</li>
			<li>
				<a id="phpinfo">
					<?php echo JText::_( 'PHP Information' ); ?></a>
			</li>
		</ul>
		<div class="clr"></div>
	</div>
</div>
<div class="clr"></div>