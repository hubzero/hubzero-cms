<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('video.css')
     ->js('video.js')
     ->js('hubpresenter.plugins.js')
     ->css('jquery.colpick.css', 'system')
     ->js('jquery.colpick', 'system');

//base url for the resource
$base = PATH_APP . DS . trim($this->config->get('uploadpath'), DS);
$base = substr($base, strlen(PATH_ROOT));

//presentation manifest
$presentation = $this->manifest->presentation;

//determine height and width
$width  = (isset($presentation->width) && $presentation->width != 0) ? $presentation->width . 'px' : 'auto';
$height = (isset($presentation->height) && $presentation->height != 0) ? $presentation->height . 'px' : 'auto';

$this->css('
#video-flowplayer {
	width: ' . $width . ';
	height: ' . $height . ';
}
');
?>

<section class="main section video-page">
	<div class="video-page-header">
		<h2><?php echo $this->escape(isset($this->parent) ? $this->parent->title : $this->resource->title); ?></h2>
		<?php if (isset($this->parent)) : ?>
			<p class="video-page-back"><a href="<?php echo Route::url('index.php?option=com_resources&id=' . $this->parent->id); ?>">&larr; Back to resource</a></p>
		<?php endif; ?>
	</div>

<div class="video-layout">
<div class="video-player-col">
<div id="video-container" class="paused">
	<div id="video-stage">
	<div id="play-state-flash" aria-hidden="true">
		<svg class="flash-play" viewBox="0 0 48 48" fill="none"><path d="M16 10v28l22-14z" fill="#fff"/></svg>
		<svg class="flash-pause" viewBox="0 0 48 48" fill="none"><rect x="12" y="10" width="8" height="28" rx="2" fill="#fff"/><rect x="28" y="10" width="8" height="28" rx="2" fill="#fff"/></svg>
	</div>
	<?php if (count($presentation->media) > 0) : ?>
		<video webkit-playsinline playsinline controls id="video-player" aria-label="<?php echo $this->escape(isset($this->parent) ? $this->parent->title : $this->resource->title); ?>" data-mediaid="<?php echo $this->resource->id; ?>">
			<?php foreach ($presentation->media as $video) : ?>
				<?php
					switch ($video->type)
					{
						case 'ogg':
						case 'ogv':
							$type = "video/ogg;";
							break;
						case 'webm':
							$type = "video/webm;";
							break;
						case 'mp4':
						case 'm4v':
						default:
							$type = "video/mp4;";
							break;
					}

					//video source
					$source = $video->source;

					if (preg_match('/^(.*?)(\/+)(app\/+site\/+resources)(\/+)([12]\d\d\d)(\/+)(0\d|1[012])(\/+)(\d{5})\/*(.*)$/m', $source, $matches))
					{
						$url = '/resources/' . $matches[9] . '/download/' . $matches[10];
					}
					else
					{
						$url = $source;
					}
				?>
				<source src="<?php echo $url; ?>" type="<?php echo $type; ?>" />
			<?php endforeach; ?>

			<a href="<?php echo $url; ?>"
				id="video-flowplayer"
				data-mediaid="<?php echo $this->resource->id; ?>"
				aria-label="<?php echo Lang::txt('COM_RESOURCES_DOWNLOAD_VIDEO'); ?>"></a>

			<?php if (count($presentation->subtitles) > 0) : ?>
				<?php foreach ($presentation->subtitles as $subtitle) : ?>
					<?php
						//get file modified time
						$source = $subtitle->source;
						$auto   = $subtitle->autoplay;

						//if were playing local files
						$modified = '123456789';
						if (substr($subtitle->source, 0, 4) != 'http')
						{
							// Strip PATH_ROOT if the manifest stored an absolute path
							if (strpos($source, PATH_ROOT) === 0)
							{
								$source = substr($source, strlen(PATH_ROOT));
							}
							else
							{
								$source = $base . $source;
							}
							if (file_exists(PATH_ROOT . $source))
							{
								$modified = filemtime(PATH_ROOT . $source);
							}
						}
					?>
					<?php if (preg_match('/\.vtt$/i', $source)) : ?>
					<?php // VTT: use native <track> — browser renders captions with
					      // user-preferred styles from OS accessibility settings. ?>
					<track kind="captions" src="<?php echo $source; ?>?v=<?php echo $modified; ?>" srclang="<?php echo isset($subtitle->lang) ? $subtitle->lang : 'en'; ?>" label="<?php echo $subtitle->name; ?>"<?php echo ($auto) ? ' default' : ''; ?> />
					<?php else : ?>
					<?php // SRT: use custom JS overlay (browsers don't support native SRT) ?>
					<div aria-hidden="true"
						data-autoplay="<?php echo $auto; ?>"
						data-type="subtitle"
						data-lang="<?php echo $subtitle->name; ?>"
						data-src="<?php echo $source ?>?v=<?php echo $modified; ?>"></div>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</video>
	<?php endif; ?>

	<div id="control-box" class="no-controls" data-theme="dark" role="toolbar" aria-label="Video controls">
		<div id="progress-bar"></div>
		<div id="control-buttons">
			<div id="control-buttons-left">
				<a id="play-pause" class="control" href="javascript:void(0);" role="button" aria-label="Play video" aria-pressed="false">
					<svg class="icon-pause" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect x="6" y="5" width="4" height="14" rx="1.5" fill="currentColor"/>
						<rect x="14" y="5" width="4" height="14" rx="1.5" fill="currentColor"/>
					</svg>
					<svg class="icon-play" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none">
						<path d="M7 5L19 12L7 19V5Z" fill="currentColor"/>
					</svg>
				</a>
				<div id="media-progress"></div>
				<div id="volume-group">
					<button id="volume-toggle" type="button" aria-label="Mute">
						<svg class="icon-vol-high" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M3 9v6h4l5 5V4L7 9H3z" fill="currentColor"/>
							<path d="M16.5 12A4.5 4.5 0 0 0 14 7.97v8.05A4.5 4.5 0 0 0 16.5 12z" fill="currentColor"/>
							<path d="M14 3.23v2.06A7 7 0 0 1 19 12a7 7 0 0 1-5 6.71v2.06A9 9 0 0 0 21 12 9 9 0 0 0 14 3.23z" fill="currentColor"/>
						</svg>
						<svg class="icon-vol-medium" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none">
							<path d="M3 9v6h4l5 5V4L7 9H3z" fill="currentColor"/>
							<path d="M16.5 12A4.5 4.5 0 0 0 14 7.97v8.05A4.5 4.5 0 0 0 16.5 12z" fill="currentColor"/>
						</svg>
						<svg class="icon-vol-low" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none">
							<path d="M3 9v6h4l5 5V4L7 9H3z" fill="currentColor"/>
							<path d="M18.5 12a6.5 6.5 0 0 0-1-3.35v6.7A6.5 6.5 0 0 0 18.5 12z" fill="currentColor" opacity=".4"/>
						</svg>
						<svg class="icon-vol-mute" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none">
							<path d="M3 9v6h4l5 5V4L7 9H3z" fill="currentColor"/>
							<line x1="17" y1="9" x2="23" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
							<line x1="23" y1="9" x2="17" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
						</svg>
					</button>
					<label for="volume-bar" class="sr-only">Volume</label>
					<div id="volume-bar"></div>
				</div>
			</div>
			<div id="control-buttons-right">
				<a id="subtitle" class="control" href="javascript:void(0);" aria-pressed="false" aria-label="Captions and Transcript settings" aria-haspopup="true" aria-expanded="false">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect x="2" y="5" width="20" height="14" rx="2.5" stroke="currentColor" stroke-width="1.75"/>
						<text x="5" y="16.5" font-family="sans-serif" font-size="9" font-weight="700" fill="currentColor" text-anchor="start" dominant-baseline="auto">CC</text>
					</svg>
					<div class="control-container subtitle-controls">
						<h3>Captions/Transcript</h3>
						<div class="grid">
							<div class="col span4 label">
								<label for="subtitle-selector">Captions:</label>
							</div>
							<div class="col span8 omega input">
								<select id="subtitle-selector">
									<option value="">None/Off</option>
								</select>
							</div>
						</div>
						<div class="grid">
							<div class="col span4 label">
								<label for="transcript-selector">Transcript:</label>
							</div>
							<div class="col span8 omega input">
								<select id="transcript-selector" class="transcript-selector">
									<option value="">None/Off</option>
								</select>
							</div>
						</div>
						<button type="button" class="options-toggle" aria-expanded="false" aria-controls="subtitle-options">Options</button>
						<div class="subtitle-settings hide" id="subtitle-options">
							<div class="grid">
								<div class="col span6 label">
									<label for="font-selector">Font:</label>
								</div>
								<div class="col span6 omega input">
									<select id="font-selector">
										<option value="Arial" selected>Arial</option>
										<option value="Times New Roman">Times New Roman</option>
										<option value="Tahoma">Tahoma</option>
										<option value="Trebuchet MS">Trebuchet MS</option>
										<option value="Verdana">Verdana</option>
										<option value="Courier New">Courier New</option>
									</select>
								</div>
							</div>
							<div class="grid">
								<div class="col span6 label">
									<label for="font-size-selector">Font Size:</label>
								</div>
								<div class="col span6 omega input">
									<select id="font-size-selector">
										<option value="12">Small</option>
										<option value="18" selected>Medium</option>
										<option value="24">Large</option>
									</select>
								</div>
							</div>
							<div class="grid">
								<div class="col span6 label">
									<span id="font-color-label">Font Color:</span>
								</div>
								<div class="col span6 omega input">
									<div id="font-color" data-color="FFF" tabindex="0" aria-labelledby="font-color-label" aria-label="Font color picker"></div>
								</div>
							</div>
							<div class="grid">
								<div class="col span6 label">
									<span id="background-color-label">Background:</span>
								</div>
								<div class="col span6 omega input">
									<div id="background-color" data-color="000" tabindex="0" aria-labelledby="background-color-label" aria-label="Background color picker"></div>
								</div>
							</div>
							<div class="grid">
								<div class="col span12 omega subtitle-settings-preview-container">
									<div class="subtitle-settings-preview">
										<div class="test">This is an Example</div>
									</div>
								</div>
							</div>
							<div class="actions">
								<button class="btn btn-info btn-secondary icon-save" id="subtitle-settings-save">Save</button>
							</div>
						</div>
					</div>
				</a>
				<a id="transcript-toggle" class="control disabled" href="javascript:void(0);" role="button" aria-label="Transcript" aria-disabled="true">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M4 5h16M4 9h10M4 13h12M4 17h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
					</svg>
				</a>
				<a id="settings" class="control" href="javascript:void(0);" aria-label="Playback settings" aria-haspopup="true" aria-expanded="false">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" fill="currentColor"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M19.4 13a7.6 7.6 0 0 0 .1-1c0-.34-.03-.67-.08-1l2.16-1.68a.5.5 0 0 0 .12-.64l-2.05-3.55a.5.5 0 0 0-.61-.22l-2.55 1.03a7.45 7.45 0 0 0-1.72-1l-.38-2.72A.49.49 0 0 0 14 2h-4a.49.49 0 0 0-.49.42L9.13 5.14a7.45 7.45 0 0 0-1.72 1L4.86 5.11a.49.49 0 0 0-.61.22L2.2 8.88a.48.48 0 0 0 .12.64L4.48 11c-.05.33-.08.66-.08 1s.03.67.08 1L2.32 14.68a.5.5 0 0 0-.12.64l2.05 3.55c.12.22.38.3.61.22l2.55-1.03c.53.39 1.1.72 1.72 1l.38 2.72c.06.28.28.42.49.42h4c.22 0 .43-.14.49-.42l.38-2.72a7.45 7.45 0 0 0 1.72-1l2.55 1.03c.23.08.49 0 .61-.22l2.05-3.55a.5.5 0 0 0-.12-.64L19.4 13z" fill="currentColor" opacity=".7"/>
					</svg>
					<div class="control-container settings-controls">
						<h3>Settings</h3>
						<div class="grid">
							<div class="label"><label for="speed">Speed:</label></div>
							<div class="input">
								<select id="speed">
									<option value=".25">.25×</option>
									<option value=".5">.5×</option>
									<option selected value="1">Normal</option>
									<option value="1.25">1.25×</option>
									<option value="1.5">1.5×</option>
									<option value="2">2×</option>
								</select>
							</div>
						</div>
					</div>
				</a>
				<a id="link" class="control" href="javascript:void(0);" aria-label="Share link at current time" aria-haspopup="true" aria-expanded="false">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					<div class="control-container link-controls">
						<h3>Share at current time</h3>
						<div class="link-input-wrap">
							<span id="timestamp-link" class="share-url" aria-label="Link at current position"></span>
							<button type="button" id="copy-link" aria-label="Copy link">Copy</button>
						</div>
					</div>
				</a>
				<a id="theatre" class="control" href="javascript:void(0);" role="button" aria-label="Theatre mode">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect x="2" y="5" width="20" height="14" rx="1" stroke="currentColor" stroke-width="2"/>
					</svg>
				</a>
				<a id="fullscreen" class="control" href="javascript:void(0);" role="button" aria-label="Fullscreen">
					<svg class="icon-expand" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					<svg class="icon-shrink" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none">
						<path d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</a>
			</div>
		</div>
	</div><!-- /#control-box -->
	</div><!-- /#video-stage -->

	<div id="video-subtitles"></div>
</div><!-- /#video-container -->
</div><!-- /.video-player-col -->

<div class="video-transcript-col">
<div id="transcript-container" role="region" aria-label="Transcript">
	<div id="transcript-toolbar" role="toolbar" aria-label="Transcript controls">
		<div id="transcript-select" aria-live="polite"></div>
		<div id="transcript-search-wrap">
			<svg class="transcript-search-icon" aria-hidden="true" focusable="false" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
				<circle cx="8.5" cy="8.5" r="5.5" stroke="currentColor" stroke-width="1.75"/>
				<line x1="12.5" y1="12.5" x2="17" y2="17" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
			</svg>
			<label for="transcript-search" class="sr-only">Search transcript</label>
			<input type="search" id="transcript-search" placeholder="Search transcript…" aria-label="Search transcript" autocomplete="off" />
			<button type="button" id="transcript-search-clear" aria-label="Clear search" style="display:none">
				<svg aria-hidden="true" focusable="false" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<line x1="5" y1="5" x2="15" y2="15" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
					<line x1="15" y1="5" x2="5" y2="15" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
				</svg>
			</button>
		</div>
		<span id="transcript-search-count" aria-live="polite" aria-atomic="true"></span>
		<div id="transcript-font-controls">
			<button type="button" id="font-smaller" aria-label="Decrease transcript font size">
				<svg aria-hidden="true" focusable="false" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<text x="3" y="15" font-family="serif" font-size="11" fill="currentColor">A</text>
					<line x1="13" y1="10" x2="19" y2="10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
				</svg>
			</button>
			<button type="button" id="font-bigger" aria-label="Increase transcript font size">
				<svg aria-hidden="true" focusable="false" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<text x="1" y="16" font-family="serif" font-size="14" fill="currentColor">A</text>
					<line x1="13" y1="10" x2="19" y2="10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
					<line x1="16" y1="7" x2="16" y2="13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
				</svg>
			</button>
		</div>
		<button type="button" id="transcript-close" aria-label="Close transcript">
			<svg aria-hidden="true" focusable="false" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
				<line x1="5" y1="5" x2="15" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
				<line x1="15" y1="5" x2="5" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
			</svg>
		</button>
	</div>
	<div id="transcripts" role="log" aria-label="Transcript text" aria-live="off" tabindex="0"></div>
</div>
</div><!-- /.video-transcript-col -->
</div><!-- /.video-layout -->
</section><!-- /.video-page -->
<?php
Document::setTitle(isset($this->parent) ? $this->parent->title : $this->resource->title);
