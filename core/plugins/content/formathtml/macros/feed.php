<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formathtml\Macros;

use Plugins\Content\Formathtml\Macro;

/**
 * Macro class for displaying a Youtube video
 */
class Feed extends Macro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Embeds a RSS Feed into the Page';
		$txt['html'] = '<p>Embeds a RSS feed into the page.</p>
						<p>Examples:</p>
						<ul>
							<li><code>[[Feed(http://rss.cnn.com/rss/cnn_topstories.rss)]]</code></li>
							<li><code>[[Feed(http://rss.cnn.com/rss/cnn_topstories.rss, 3)]] - show 3 feed items</code></li>
							<li><code>[[Feed(http://rss.cnn.com/rss/cnn_topstories.rss, class=cnn_feed)]] - feed with class "cnn_feed"</code></li>
						</ul>';

		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return  string
	 */
	public function render()
	{
		// Get the args passed in
		$args = explode(',', $this->args);

		// Get feed url
		$url = $this->_getFeedUrl($args);

		if (!$url)
		{
			return '';
		}

		// Get feed details
		$limit = $this->_getFeedLimit($args, 5);
		$class = $this->_getFeedClass($args);

		// Fetch the feed ourselves so every redirect hop is re-checked against the
		// SSRF guard and the connection is pinned to the checked address, then hand
		// the parser the content: its own fetcher follows redirects unchecked
		$content = false;
		$addr    = $this->isPublicHttpUrl($url);
		// This runs while a page is rendering, so cap the whole chase rather than
		// letting a slow redirect chain cost every viewer 6 x the per-hop timeout
		$deadline = microtime(true) + 15;
		for ($hop = 0; $addr && $hop <= 5; $hop++)
		{
			if (microtime(true) > $deadline)
			{
				$content = false;
				break;
			}
			$ch = curl_init($url);
			curl_setopt_array($ch, array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FOLLOWLOCATION => false,
				CURLOPT_ENCODING       => '',
				CURLOPT_USERAGENT      => 'HUBzero',
				CURLOPT_CONNECTTIMEOUT => 5,
				CURLOPT_TIMEOUT        => 10,
				CURLOPT_PROTOCOLS      => CURLPROTO_HTTP | CURLPROTO_HTTPS,
			));
			if ($addr !== true)
			{
				$rh = trim((string) parse_url($url, PHP_URL_HOST), '[]');
				$rp = (int) parse_url($url, PHP_URL_PORT);
				if (!$rp)
				{
					$rp = (strtolower((string) parse_url($url, PHP_URL_SCHEME)) == 'https') ? 443 : 80;
				}
				$pin = (strpos($addr, ':') !== false) ? '[' . $addr . ']' : $addr;
				// An address literal needs no pin (there is no name to re-point), and
				// an IPv6 literal host would make a malformed RESOLVE entry
				if (!filter_var($rh, FILTER_VALIDATE_IP))
				{
					curl_setopt($ch, CURLOPT_RESOLVE, array($rh . ':' . $rp . ':' . $pin));
				}
			}
			$content  = curl_exec($ch);
			$code     = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$redirect = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
			curl_close($ch);
			if ($code < 300 || $code >= 400 || !$redirect)
			{
				break;
			}
			// Follow the redirect only if its target also passes the guard
			$url     = $redirect;
			$addr    = $this->isPublicHttpUrl($url);
			$content = false;
		}
		if ($content === false || $content === '')
		{
			return '';
		}

		// Get feed
		$feed = \App::get('feed.parser');
		$feed->set_raw_data($content);
		$feed->init();

		// Var to hold html
		$html = '<div class="feed ' . $class . '">';

		// Display title
		$title = $feed->get_title();
		$link  = $feed->get_permalink();
		if ($title)
		{
			$html .= '<h3><a rel="external" href="' . $link . '">' . $title . '</a></h3>';
		}

		// Display description
		$desc = $feed->get_description();
		if ($desc)
		{
			$html .= '<p>' . $desc . '</p>';
		}

		// Add each item
		foreach ($feed->get_items(0, $limit) as $item)
		{
			$html .= $this->_renderItem($item);
		}

		// Close feed
		$html .= '</div>';

		return $html;
	}

	/**
	 * Render an individual item
	 *
	 * @param   object  $item  Feed Item
	 * @return  string
	 */
	private function _renderItem($item)
	{
		$html  = '<div class="item">';
		$html .= '<h4>' . $item->get_title() . '</h4>';
		$html .= '<p>' . $item->get_description() . '</p>';
		$html .= '<a rel="external" href="' . $item->get_permalink() . '">Read More &rsaquo;</a>';
		$html .= '</div>';
		return $html;
	}

	/**
	 * Pull Feed url from args passed in
	 *
	 * @param   array  $args  Macro Arguments
	 * @return  mixed
	 */
	private function _getFeedUrl(&$args)
	{
		foreach ($args as $k => $arg)
		{
			if (filter_var($arg, FILTER_VALIDATE_URL) && $this->isPublicHttpUrl($arg))
			{
				$url = $arg;
				unset($args[$k]);
				return $url;
			}
		}
		return null;
	}

	/**
	 * Only allow http(s) feeds whose host does not resolve to a private,
	 * loopback, link-local or reserved address (SSRF guard).
	 *
	 * @param   string   $url
	 * @return  boolean
	 */
	private function isPublicHttpUrl($url)
	{
		$parts = parse_url($url);
		if (!$parts || empty($parts['scheme']) || empty($parts['host']))
		{
			return false;
		}
		if (!in_array(strtolower($parts['scheme']), array('http', 'https'), true))
		{
			return false;
		}
		$host = trim($parts['host'], '[]');

		// The hub's own feeds are always allowed (its name may resolve privately
		// behind NAT). Compare against the CONFIGURED site address, not the
		// request's Host header, which the caller controls.
		// live_site is optional and ships empty, so this branch never fired on a
		// default install -- and it exists precisely for a hub behind NAT, whose
		// own name resolves into private space and is refused below. Fall back to
		// the request root's host, which is derived from the configured site URL
		// when there is one. Config first, so a caller-supplied Host header
		// cannot nominate the exception on a hub that has set live_site.
		$own = (string) parse_url((string) \Config::get('live_site'), PHP_URL_HOST);

		if ($own === '')
		{
			$own = (string) parse_url((string) \Request::root(), PHP_URL_HOST);
		}

		if ($own !== '' && strcasecmp($host, $own) === 0)
		{
			return true;
		}
		$ips = array();
		if (filter_var($host, FILTER_VALIDATE_IP))
		{
			$ips[] = $host;
		}
		else
		{
			$records = @dns_get_record($host, DNS_A | DNS_AAAA);
			if ($records)
			{
				foreach ($records as $r)
				{
					if (!empty($r['ip']))   $ips[] = $r['ip'];
					if (!empty($r['ipv6'])) $ips[] = $r['ipv6'];
				}
			}
			if (!$ips && ($resolved = @gethostbynamel($host)))
			{
				$ips = $resolved;
			}
		}
		if (!$ips)
		{
			return false;
		}
		foreach ($ips as $ip)
		{
			// NO_PRIV_RANGE|NO_RES_RANGE does not cover ::ffff:0:0/96, so an
			// internal v4 address written as IPv4-mapped IPv6 passes it -- and
			// curl connects to the v4 address it maps to. Unwrap to the embedded
			// v4 form first and classify that. Verified: ::ffff:169.254.169.254
			// and ::ffff:127.0.0.1 both pass the raw filter.
			//
			// 100::/64 (discard-only) and 64:ff9b::/96 (NAT64) are refused here
			// too: both are translation prefixes that carry an embedded target.
			$packed = @inet_pton($ip);

			if ($packed !== false && strlen($packed) === 16)
			{
				if (substr($packed, 0, 12) === "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff\xff")
				{
					$ip = inet_ntop(substr($packed, 12));
				}
				else if (substr($packed, 0, 8) === "\x01\x00\x00\x00\x00\x00\x00\x00"
					  || substr($packed, 0, 12) === "\x00\x64\xff\x9b\x00\x00\x00\x00\x00\x00\x00\x00")
				{
					return false;
				}
			}

			if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE))
			{
				return false;
			}
		}
		// The first checked address, so the fetch can be pinned to it (truthy)
		return $ips[0];
	}

	/**
	 * Get feed item limit
	 *
	 * @param   array    $args     Macro Arguments
	 * @param   integer  $default  Default return value
	 * @return  mixed
	 */
	private function _getFeedLimit(&$args, $default = 5)
	{
		foreach ($args as $k => $arg)
		{
			if (is_numeric($arg) && $arg > 0 && $arg < 50)
			{
				$limit = $arg;
				unset($args[$k]);
				return $limit;
			}
		}

		// If we didnt find one return default
		return $default;
	}

	/**
	 * Get feed class
	 *
	 * @param   array  $args  Macro Arguments
	 * @return  mixed
	 */
	private function _getFeedClass(&$args)
	{
		foreach ($args as $k => $arg)
		{
			if (preg_match('/class=([\w-]*)/', $arg, $matches))
			{
				$class = (isset($matches[1])) ? $matches[1] : '';
				unset($args[$k]);
				return $class;
			}
		}

		return null;
	}
}
