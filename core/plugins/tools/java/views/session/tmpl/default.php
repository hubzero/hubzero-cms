<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->js('jquery.editable.min.js', 'system')
     ->js('deployJava.js')
     ->js();

$base = rtrim(Request::base(true), '/');
?>
<p id="troubleshoot" class="help">
	If your application fails to appear within a minute, <a target="_blank" rel="external" href="http://www.java.com/en/download/testjava.jsp">troubleshoot this problem</a>.
</p>

<applet id="<?php echo $this->output->id ?>" class="<?php echo $this->output->class; ?>" code="<?php echo $this->output->code; ?>" archive="<?php echo $this->output->archive; ?>" width="<?php echo $this->output->width; ?>" height="<?php echo $this->output->height; ?>" MAYSCRIPT>
	<param name="name" value="<?php echo $this->output->name; ?>" />
	<?php if (!empty($this->output->host)) { ?>
		<param name="HOST" value="<?php echo $this->output->host; ?>" />
	<?php } ?>
	<param name="PORT" value="<?php echo $this->output->port; ?>" />
	<param name="ENCPASSWORD" value="<?php echo $this->output->encpassword; ?>" />
	<param name="CONNECT" value="<?php echo $this->output->connect; ?>" />
	<param name="View Only" value="<?php echo $this->output->view_only; ?>" />
	<param name="trustAllVncCerts" value="<?php echo $this->output->trust_all_vnc_certs; ?>" />
	<param name="Offer relogin" value="<?php echo $this->output->offer_relogin; ?>" />
	<param name="DisableSSL" value="<?php echo $this->output->disable_ssl; ?>" />
	<param name="permissions" value="<?php echo $this->output->permissions; ?>" />
	<param name="Show Controls" value="<?php echo $this->output->show_controls; ?>" />
	<param name="ENCODING" value="<?php echo $this->output->encoding; ?>" />
	<?php if (!empty($this->output->debug)) { ?>
		<param name="Debug" value="<?php echo $this->output->debug; ?>" />
	<?php } ?>
	<?php if (!empty($this->output->show_local_cursor)) { ?>
		<param name="ShowLocalCursor" value="<?php echo $this->output->show_local_cursor; ?>" />
	<?php } ?>
	<p class="error">
		In order to view an application, you must have Java installed and enabled. (<a href="<?php echo $base; ?>/kb/misc/java">How do I do this?</a>)
	</p>
</applet>

<script type="text/javascript">
	var JavaNotFound = 'It appears that Java is either not installed or not enabled.'<?php
		$options = array();
		if ($plugins = Event::trigger('tools.onToolSessionIdentify'))
		{
			$url = 'index.php?option=com_tools&app=' . $this->app->name . '&task=session&sess=' . $this->app->sess . '&viewer=';
			foreach ($plugins as $plugin)
			{
				if ($plugin->name == 'java')
				{
					continue;
				}

				$options[] = '<a href="' . Route::url($url . $plugin->name) . '">' . $plugin->title . '</a>';
			}
			if (count($options))
			{
				echo " + 'Consider trying an alternate viewer:<br /><br />" . implode('<br />', $options) . "'";
			}
		}
		?>;

	var JavaNotStarted = 'It appears that the Java environment did not start properly. Please make sure that you have Java installed and enabled for your web browser.'<?php
		if (count($options))
		{
			echo " + 'If you do not have Java or problems persist, consider trying an alternate viewer:<br /><br />" . implode('<br />', $options) . "'";
		}
		?>;

	var JavaBug = 'There is a problem caused by the specific version ' +
				'of Java you are using with this browser. You will ' +
				'likely not be able to view tools. There are three ' +
				'things you can try:<br /> ' +
				'1) Restart your browser and disable Javascript ' +
				'before starting a tool the ' +
				'first time and re-enable Javascript once the first ' +
				'tool is running.<br />' +
				'2) Switch to a different version of Java. ' +
				'Version 1.6.0 Update 02 (and earlier) will work ' +
				'but 1.6.0 Update 03 and 04 do not.<br>' +
				'3) Use a browser other than Firefox.<br>' +
				'(<a href="<?php echo Request::base(); ?>/kb/tools/unable_to_connect_error_in_firefox/">More information</a>)  ';

	if (!deployJava.versionCheck("1.6.0+")) {
		HUB.Mw.noJava();
	} else {
		HUB.Mw.startAppletTimeout();
		HUB.Mw.connectingTool();
	}
</script>
