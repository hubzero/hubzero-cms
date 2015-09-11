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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// Menu items
Toolbar::title(Lang::txt('COM_SYSTEM_APC_VERSION'), 'config.png');

$this->css('apc.css');
?>

<?php
	$this->view('_submenu')->display();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="information">
		<h2>APC Version Information</h2>
<?php
	if (defined('PROXY'))
	{
		$ctxt = stream_context_create( array( 'http' => array( 'proxy' => PROXY, 'request_fulluri' => True ) ) );
		$rss = @file_get_contents("http://pecl.php.net/feeds/pkg_apc.rss", False, $ctxt);
	}
	else
	{
		$rss = @file_get_contents("http://pecl.php.net/feeds/pkg_apc.rss");
	}

	if (!$rss)
	{
		echo '<p class="error">Unable to fetch version information.</p>';
	}
	else
	{
		$apcversion = phpversion('apc');

		preg_match('!<title>APC ([0-9.]+)</title>!', $rss, $match);
		//echo '<tr class="tr-0 center"><td>';
		if (version_compare($apcversion, $match[1], '>='))
		{
			echo '<p class="message">You are running the latest version of APC ('.$apcversion.')</p>';
			$i = 3;
		}
		else
		{
			echo '<p class="warning">You are running an older version of APC ('.$apcversion.'),
				newer version '.$match[1].' is available at <a href="http://pecl.php.net/package/APC/'.$match[1].'">
				http://pecl.php.net/package/APC/'.$match[1].'</a>
				</p>';
			$i = -1;
		}

		echo '<div class="change-log">';
		echo '<h3>Change Log:</h3>';
		echo '<div class="change-log-contents">';

		preg_match_all('!<(title|description)>([^<]+)</\\1>!', $rss, $match);
		next($match[2]); next($match[2]);

		while (list(,$v) = each($match[2]))
		{
			list(,$ver) = explode(' ', $v, 2);
			if ($i < 0 && version_compare($apcversion, $ver, '>='))
			{
				break;
			}
			else if (!$i--)
			{
				break;
			}
			echo "<b><a href=\"http://pecl.php.net/package/APC/$ver\">".htmlspecialchars($v)."</a></b><br /><blockquote>";
			echo nl2br(htmlspecialchars(current($match[2])))."</blockquote>";
			next($match[2]);
		}
		echo '</div>';
		echo '</div>';
	}
?>
	</div>
</form>