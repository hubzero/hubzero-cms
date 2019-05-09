<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->css('jquery.fancybox.css', 'system')
     ->js();
?>

<div id="abox-content">
<?php

$oWidth = '780';
$oHeight= '480';

// Get some attributes
$attribs    = new \Hubzero\Config\Registry($this->primary->attribs);
$width      = $attribs->get( 'width', '' );
$height     = $attribs->get( 'height', '' );
$attributes = $attribs->get('attributes', '');

$width  = (intval($width) > 0) ? $width : $oWidth;
$height = (intval($height) > 0) ? $height : $oHeight;

// Get mime type
$mTypeParts = explode(';', $this->mimetype);
$cType      = $mTypeParts[0];

if ($attributes)
{
	$a = explode(',', $attributes);
	$bits = array();
	if ($a && is_array($a))
	{
		foreach ($a as $b)
		{
			if (strstr($b, ':'))
			{
				$b = explode(':', $b);
				$bits[] = trim($b[0]) . '="' . trim($b[1]) . '"';
			}
		}
	}
	$attributes = implode(' ', $bits);
}

// Formats that can be previewed via Google viewer
$docs = array(
	'pdf', 'doc', 'docx', 'xls', 'xlsx',
	'ppt', 'pptx', 'pages', 'ai',
	'psd', 'tiff', 'dxf', 'eps', 'ps', 'ttf', 'xps', 'svg'
);

$html5video = array("mp4","m4v","webm","ogv");

$token = '';

if (!User::isGuest())
{
	$session_id = App::get('session')->getId();

	$key = App::hash(@$_SERVER['HTTP_USER_AGENT']);
	$crypter = new \Hubzero\Encryption\Encrypter(
		new \Hubzero\Encryption\Cipher\Simple,
		new \Hubzero\Encryption\Key('simple', $key, $key)
	);

	$token = base64_encode($crypter->encrypt($session_id));
}

$downloadUrl = Route::url('index.php?option=com_publications&id=' . $this->publication->id . '&task=serve&aid=' . $this->aid . '&render=download&token=' . $token);

$viewUrl = Route::url('index.php?option=com_publications&id=' . $this->publication->id . '&task=serve&aid=' . $this->aid . '&render=download&disposition=inline&token=' . $token);

?>
<div class="sample">
	<p><?php echo Lang::txt('COM_PUBLICATIONS_PUBLICATION') . ': <strong>' . $this->publication->title . '</strong>'; ?> <?php if ($this->primary->role != 1) { echo '&nbsp;&nbsp; Supporting Doc: <strong>' . $this->primary->path . '</strong>'; } ?></p>
</div>

<?php
// Image?
if ($this->type == 'image')
{
	echo '<img ' . $attributes . ' src="' . $this->url . '" alt="Image" />'."\n";
}
elseif (in_array(strtolower($this->ext), $docs) && $this->googleView)
{
	// View via Google
	echo '<iframe src="https://docs.google.com/viewer?url=' . urlencode(Request::base() . $downloadUrl) . '&amp;embedded=true#:0.page.0" width="100%" height="500" name="file_resource" frameborder="0" bgcolor="white"></iframe>'."\n";
}
else
// View in html5-browser
{
	?>
	<p class="direct-download">Publication doesn't load in your browser or shows partial file? <a href="<?php echo Request::base() . $downloadUrl; ?>">Download file</a></p>
	<?php if (strtolower($this->ext) == 'wmv') { ?>
		<object type="video/x-ms-wmv"
			  data="<?php echo $this->url; ?>" width="100%" height="<?php echo $height; ?>">
			  <param name="src" value="<?php echo $this->url; ?>" />
			  <param name="autostart" value="true" />
			  <param name="controller" value="true" />
		</object>
	<?php } else { ?>
		<div class="video-container">
			<object width="100%" height="<?php echo $height; ?>">
			<param name="allowfullscreen" value="true" />
			<param name="allowscriptaccess" value="always" />
			<param name="movie" value="<?php echo $this->url; ?>" />
			<param name="scale" value="aspect" />
			<embed src="<?php echo $this->url; ?>" scale="aspect"></embed>
			</object>
		</div>
	<?php } ?>
	<?php
}
?>
</div>
