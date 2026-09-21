<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$item = $this->row->item();

$content = $this->row->description('parsed');
$content = ($content ?: $item->description('parsed'));

$linkUrl = stripslashes((string) $item->get('url'));

// This template renders far more than user-typed links: Item::type() maps every
// type outside publication|collection|deleted|image|file|text|link to 'link', so
// collected blog/article/course/forum/kb/resource/wiki/wish rows land here too.
// Every one of those stores a SITE-RELATIVE url -- from Route::url(), which also
// entity-encodes it, or a bare index.php link (forum), or the collecting
// request's REQUEST_URI (article). Decode first so the escape at the sink is the
// only one, then reject by scheme rather than by requiring http(s) -- requiring
// it would blank the href of every internally collected item.
$linkUrl = html_entity_decode($linkUrl, ENT_QUOTES, 'UTF-8');

// Control characters and spaces are stripped for the test only: "ja\nvascript:"
// is a scheme to the browser.
$probe = preg_replace('/[\x00-\x20]+/', '', $linkUrl);

if (preg_match('#^/[/\\\\]#', $probe)
 || (preg_match('#^[a-z][a-z0-9+.\-]*:#i', $probe) && !preg_match('#^https?://#i', $probe)))
{
	// protocol-relative //evil.com (or /\evil.com, which browsers read the same
	// way), or any scheme that is not http(s) -- blob:, filesystem:, view-source:
	$linkUrl = '';
}
?>
		<h4>
			<a href="<?php echo $this->escape($linkUrl); ?>" rel="external nofollow noreferrer">
				<?php echo $this->escape(stripslashes($item->get('title', $item->get('url')))); ?>
			</a>
		</h4>
<?php if ($content): ?>
		<div class="description">
			<?php echo $content; ?>
		</div>
<?php endif;
