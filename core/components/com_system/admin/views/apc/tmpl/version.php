<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
		$ctxt = stream_context_create( array( 'http' => array( 'proxy' => PROXY, 'request_fulluri' => true ) ) );
		$rss = @file_get_contents("http://pecl.php.net/feeds/pkg_apc.rss", false, $ctxt);
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
		next($match[2]);
next($match[2]);

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