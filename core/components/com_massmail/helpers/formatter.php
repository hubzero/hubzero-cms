<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Massmail\Helpers;

/**
 * Turns the composed (strict text) message into tidy plain-text and HTML
 * bodies so it renders well in both text and webmail clients.
 *
 * The composer runs CKEditor 5 in strict-text mode, so the stored body is
 * essentially paragraphs of text. Both parts carry a {{UNSUBSCRIBE_URL}}
 * token that the sender replaces per-recipient at send time.
 */
class Formatter
{
	const UNSUBSCRIBE_TOKEN = '{{UNSUBSCRIBE_URL}}';

	/**
	 * Convert (light) HTML from the editor into readable plain text.
	 *
	 * @param   string  $html
	 * @return  string
	 */
	public static function toText($html)
	{
		$t = (string) $html;
		$t = preg_replace('/\r\n?/', "\n", $t);
		$t = preg_replace('/<\s*br\s*\/?\s*>/i', "\n", $t);
		$t = preg_replace('/<\/\s*(p|div|h[1-6]|li|tr|blockquote)\s*>/i', "\n\n", $t);
		$t = strip_tags($t);
		$t = html_entity_decode($t, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$t = preg_replace("/[ \t]+\n/", "\n", $t);
		$t = preg_replace("/\n{3,}/", "\n\n", $t);
		return trim($t);
	}

	/**
	 * Build the stored plain-text body: message + footer + unsubscribe token.
	 *
	 * @param   string  $body        raw editor content (may be light HTML)
	 * @param   string  $footerText  configured footer (identity, address)
	 * @return  string
	 */
	public static function buildText($body, $footerText = '')
	{
		$text = self::toText($body);

		$footer = trim((string) $footerText);
		$text .= "\n\n-- \n";
		if ($footer !== '')
		{
			$text .= $footer . "\n\n";
		}
		$text .= "To stop receiving these emails, visit: " . self::UNSUBSCRIBE_TOKEN . "\n";

		return $text;
	}

	/**
	 * Build the stored HTML body: wrapped message + footer + unsubscribe link.
	 *
	 * @param   string  $body        raw editor content (light HTML)
	 * @param   string  $footerText  configured footer
	 * @return  string
	 */
	public static function buildHtml($body, $footerText = '')
	{
		// The editor is strict text; if content arrived as bare text, paragraph it.
		$inner = (string) $body;
		if (strip_tags($inner) === $inner)
		{
			$inner = '<p>' . nl2br(htmlspecialchars($inner, ENT_QUOTES, 'UTF-8')) . '</p>';
		}

		$footer = trim((string) $footerText);
		$footerHtml = '';
		if ($footer !== '')
		{
			$footerHtml .= nl2br(htmlspecialchars($footer, ENT_QUOTES, 'UTF-8')) . '<br><br>';
		}
		$footerHtml .= 'To stop receiving these emails, '
			. '<a href="' . self::UNSUBSCRIBE_TOKEN . '">unsubscribe here</a>.';

		$style = 'font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.5;color:#222222;';
		$html  = '<!DOCTYPE html><html><head>'
			. '<meta charset="utf-8">'
			. '<meta name="viewport" content="width=device-width,initial-scale=1">'
			. '</head><body style="margin:0;padding:0;background:#f4f4f4;">'
			. '<div style="' . $style . 'max-width:640px;margin:0 auto;padding:20px;background:#ffffff;">'
			. $inner
			. '<hr style="border:none;border-top:1px solid #dddddd;margin:24px 0;">'
			. '<div style="font-size:12px;color:#777777;line-height:1.5;">' . $footerHtml . '</div>'
			. '</div></body></html>';

		return $html;
	}
}
