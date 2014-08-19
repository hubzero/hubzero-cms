<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Menu items
JToolBarHelper::title(JText::_('COM_SYSTEM_APC_VERSION'), 'config.png');
?>

<?php
	$this->view('_submenu')->display();
?>

<form action="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="item-form">
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