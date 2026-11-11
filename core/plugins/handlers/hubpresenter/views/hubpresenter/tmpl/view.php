<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Hubzero\Content\Moderator;
use Hubzero\Facades\App;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

$this->oWidth   = '780';
$this->oHeight  = '460';

$manifest     = $this->items->findFirstWithExtension('json');
$presentation = json_decode($manifest->read());

// Content folder
$content_folder     = dirname($manifest->getAbsolutePath());
$rel_content_folder = substr($content_folder, strlen(PATH_ROOT));
$presentation       = $presentation->presentation;

// Get subs from json file
$subs = (isset($presentation->subtitles)) ? $presentation->subtitles : [];

// Make sure source is full path to assets folder
$subFiles = [];
foreach ($subs as $k => $subtitle) {
    if (!strpos($subtitle->source, DS)) {
        $subtitle->source = $rel_content_folder . DS . $subtitle->source;
    }

    $subFiles[] = $subtitle->source;
}

// Add any local subtitles too
// findAllWithExtension() returns File objects, whose string form is the
// class name, so the file name has to be asked for
foreach ($this->items->findAllWithExtension(['srt', 'SRT']) as $k => $subtitle) {
    $info     = pathinfo($subtitle->getName());
    $name     = str_replace('-auto', '', $info['filename']);
    $autoplay = (strstr($info['filename'], '-auto')) ? 1 : 0;
    $source   = $rel_content_folder . DS . $subtitle->getName();

    // Add each subtitle
    $subtitle           = new stdClass();
    $subtitle->type     = 'SRT';
    $subtitle->name     = ucfirst($name);
    $subtitle->source   = $source;
    $subtitle->autoplay = $autoplay;

    // Make sure we don't already have this file
    if (!in_array($subtitle->source, $subFiles)) {
        $subs[] = $subtitle;
    }
}

// Add the HUBpresenter stylesheet and scripts
$this->css('hubpresenter');

$this->js('hubpresenter');
$this->js('hubpresenter.plugins');

$this->js('jquery.colpick.js', 'system');
$this->css('jquery.colpick.css', 'system');


// Include media tracking
if (isset($this->entityId) && isset($this->entityType)) {
    // Get tracking for this user for this resource
    $tracking = Components\System\Models\Mediatracking::oneByUserAndObject(
        $this->entityId,
        $this->entityType,
        User::get('id'),
    );

    // Check to see if we already have a time query param
    $hasTime = (Request::getString('time', '') != '') ? true : false;

    // Do we want to redirect user with time added to url
    if (
        is_object($tracking)
        && !$hasTime
        && $tracking->current_position > 0
        && $tracking->current_position != $tracking->object_duration
        && !Request::getInt('no_html', 0)
    ) {
        $redirect = Request::current(true);

        // do we have tmpl=component in url?
        $delimeter = (strpos($redirect, '?') === false) ? '?' : '&';
        if (Request::getCmd('tmpl', '') == 'component') {
            $redirect .= $delimeter . 'tmpl=component';
        }

        $delimeter = (strpos($redirect, '?') === false) ? '?' : '&';

        // Append current position to redirect
        $redirect .= $delimeter . 'time=' . gmdate("H:i:s", $tracking->current_position);

        // Redirect
        App::redirect(Route::url($redirect, false), '', '', false);
    }
}
?>

<div id="presenter-container" class="width-container">
    <div id="presenter-header">
        <div id="title"></div>
    </div><!-- /#header -->

    <div id="presenter-content">
        <div id="presenter-left">
            <div id="slides">
                <ul class="no-js">
                    <?php $counter = 0; ?>
                    <?php foreach ($presentation->slides as $slide) : ?>
                        <li id="slide_<?= $counter ?>" title="<?= $this->escape($slide->title) ?>"
                            time="<?= $this->escape($slide->time) ?>">
                            <?php if ($slide->type == 'Image') : ?>
                                <img src="<?= with(new Moderator($content_folder . DS . $slide->media))->getUrl() ?>"
                                    alt="<?= $this->escape($slide->title) ?>" />
                            <?php else : ?>
                                <video class="slidevideo">
                                    <?php foreach ($slide->media as $source) : ?>
                                        <?php
                                        $sourceUrl = with(new Moderator($content_folder . DS . $source->source))
                                            ->getUrl();
                                        ?>
                                        <source src="<?= $sourceUrl ?>" />
                                    <?php endforeach; ?>
                                    <?php
                                    $linkUrl = with(new Moderator($content_folder . DS . $slide->media[0]->source))
                                        ->getUrl();
                                    ?>
                                    <a href="<?= $linkUrl ?>" class="flowplayer_slide" id="flowplayer_slide_<?= $counter ?>"></a>
                                </video>
                                <?php if (isset($slide->media[3])) : ?>
                                    <?php
                                    $sourceUrl = with(new Moderator($content_folder . DS . $slide->media[3]->source))
                                        ->getUrl();
                                    ?>
                                <img src="<?= $sourceUrl ?>" alt="<?= $this->escape($slide->title) ?>" class="imagereplacement" />
                                <?php endif; ?>
                            <?php endif; ?>
                        </li>
                        <?php $counter++; ?>
                    <?php endforeach; ?>
                </ul>
            </div><!-- /#slides -->

            <div id="control-box" class="no-controls" data-theme="dark">
                <div id="progress-bar"></div>
                <div id="control-buttons">
                    <div id="control-buttons-left" class="cf">
                        <a id="previous" class="control" href="javascript:void(0);"
                            title="Previous Slide"><?= Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_PREV') ?></a>
                        <a id="play-pause" class="control" href="javascript:void(0);"
                            title="Play Presentation"><?= Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_PAUSE') ?></a>
                        <a id="next" class="control" href="javascript:void(0);"
                            title="Next Slide"><?= Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_NEXT') ?></a>
                        <div id="media-progress"></div>
                    </div>
                    <div id="control-buttons-right" class="cf">
                        <a id="subtitle" class="control" href="javascript:void(0);">
                            <?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTIONS_TRANSCRIPT'); ?>
                            <div class="control-container subtitle-controls">
                                <h3><?= Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTIONS_TRANSCRIPT') ?></h3>
                                <div class="grid">
                                    <div class="col span4 label">
                                        <?php
                                        $labelText = Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTIONS');
                                        ?>
                                        <label for="subtitle-selector"><?= $labelText ?>:</label>
                                    </div>
                                    <div class="col span8 omega input">
                                        <select id="subtitle-selector">
                                            <option value="">
                                                <?= Lang::txt(
                                                    'PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTIONS_TRANSCRIPT_OFF',
                                                ) ?>
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid">
                                    <div class="col span4 label">
                                        <?php
                                        $labelText = Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_TRANSCRIPT');
                                        ?>
                                        <label for="transcript-selector"><?= $labelText ?>:</label>
                                    </div>
                                    <div class="col span8 omega input">
                                        <select class="transcript-selector">
                                            <option value="">
                                                <?= Lang::txt(
                                                    'PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTIONS_TRANSCRIPT_OFF',
                                                ) ?>
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <?php
                                $optionsToggleText = Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTIONS');
                                ?>
                                <span class="options-toggle"><?= $optionsToggleText ?></span>
                                <div class="subtitle-settings hide">
                                    <div class="grid">
                                        <div class="col span6 label">
                                            <?php
                                            $labelText = Lang::txt(
                                                'PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_FONT',
                                            );
                                            ?>
                                            <label for="font-selector"><?= $labelText ?>:</label>
                                        </div>
                                        <div class="col span6 omega input">
                                            <select id="font-selector">
                                                <option value="Arial" selected>
                                                    <?= Lang::txt(
                                                        'PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_FONT_ARIAL',
                                                    ) ?>
                                                </option>
                                                <option value="Times New Roman">
                                                    <?= Lang::txt(
                                                        'PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_FONT_TIMES',
                                                    ) ?>
                                                </option>
                                                <option value="Tahoma">
                                                    <?= Lang::txt(
                                                        'PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_FONT_TAHOMA',
                                                    ) ?>
                                                </option>
                                                <?php
                                                $optionText = Lang::txt(
                                                    'PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_FONT_TREBUCHET',
                                                );
                                                ?>
                                                <option value="Trebuchet MS"><?= $optionText ?></option>
                                                <option value="Verdana">
                                                    <?= Lang::txt(
                                                        'PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_FONT_VERDANA',
                                                    ) ?>
                                                </option>
                                                <option value="Courier New">
                                                    <?= Lang::txt(
                                                        'PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_FONT_COURIER',
                                                    ) ?>
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="grid">
                                        <div class="col span6 label">
                                            <?php
                                            $labelText = Lang::txt(
                                                'PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_FONT_SIZE',
                                            );
                                            ?>
                                            <label for="font-size-selector"><?= $labelText ?>:</label>
                                        </div>
                                        <div class="col span6 omega input">
                                            <select id="font-size-selector">
                                                <?php
                                                $optionText = Lang::txt(
                                                    'PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_FONT_SIZE_SMALL',
                                                );
                                                ?>
                                                <option value="12"><?= $optionText ?></option>
                                                <?php
                                                $optionText = Lang::txt(
                                                    'PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_FONT_SIZE_MEDIUM',
                                                );
                                                ?>
                                                <option value="18" selected><?= $optionText ?></option>
                                                <?php
                                                $optionText = Lang::txt(
                                                    'PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_FONT_SIZE_LARGE',
                                                );
                                                ?>
                                                <option value="24"><?= $optionText ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="grid">
                                        <div class="col span6 label">
                                            <?php
                                            $labelText = Lang::txt(
                                                'PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_FONT_COLOR',
                                            );
                                            ?>
                                            <label for="font-color"><?= $labelText ?>:</label>
                                        </div>
                                        <div class="col span6 omega input">
                                            <div id="font-color" data-color="FFF"></div>
                                        </div>
                                    </div>
                                    <div class="grid">
                                        <div class="col span6 label">
                                            <?php
                                            $labelText = Lang::txt(
                                                'PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_BACKGROUND',
                                            );
                                            ?>
                                            <label for="background-color"><?= $labelText ?>:</label>
                                        </div>
                                        <div class="col span6 omega input">
                                            <div id="background-color" data-color="000"></div>
                                        </div>
                                    </div>
                                    <div class="grid">
                                        <div class="col span12 omega subtitle-settings-preview-container">
                                            <div class="subtitle-settings-preview">
                                                <div class="test">
                                                    <?= Lang::txt(
                                                        'PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_EXAMPLE',
                                                    ) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="actions">
                                        <?php
                                        $buttonText = Lang::txt(
                                            'PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_SAVE',
                                        );
                                        ?>
                                        <button class="btn btn-info btn-secondary icon-save"
                                            id="subtitle-settings-save"><?= $buttonText ?></button>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <a id="volume" class="control " href="javascript:void(0);">
                            <?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_VOLUME'); ?>
                            <div class="control-container volume-controls">
                                <div id="volume-bar"></div>
                            </div>
                        </a>
                        <a id="settings" class="control" href="javascript:void(0);"
                            title="Adjust Settings for Playback">
                            <?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_SETTINGS'); ?>
                            <div class="control-container settings-controls">
                                <h3><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_SETTINGS'); ?></h3>
                                <div class="grid">
                                    <div class="col span6 label">
                                        <?php
                                        $labelText = Lang::txt(
                                            'PLG_HANDLERS_HUBPRESENTER_CONTROL_SETTINGS_PLAYBACK_RATE',
                                        );
                                        ?>
                                        <label for="speed"><?= $labelText ?>:</label>
                                    </div>
                                    <div class="col span6 omega input">
                                        <select id="speed">
                                            <option value=".25">
                                                <?= Lang::txt(
                                                    'PLG_HANDLERS_HUBPRESENTER_CONTROL_SETTINGS_PLAYBACK_RATE_025',
                                                ) ?>
                                            </option>
                                            <option value=".5">
                                                <?= Lang::txt(
                                                    'PLG_HANDLERS_HUBPRESENTER_CONTROL_SETTINGS_PLAYBACK_RATE_05',
                                                ) ?>
                                            </option>
                                            <option selected value="1">
                                                <?= Lang::txt(
                                                    'PLG_HANDLERS_HUBPRESENTER_CONTROL_SETTINGS_PLAYBACK_RATE_NORMAL',
                                                ) ?>
                                            </option>
                                            <option value="1.25">
                                                <?= Lang::txt(
                                                    'PLG_HANDLERS_HUBPRESENTER_CONTROL_SETTINGS_PLAYBACK_RATE_125',
                                                ) ?>
                                            </option>
                                            <option value="1.5">
                                                <?= Lang::txt(
                                                    'PLG_HANDLERS_HUBPRESENTER_CONTROL_SETTINGS_PLAYBACK_RATE_15',
                                                ) ?>
                                            </option>
                                            <option value="2">
                                                <?= Lang::txt(
                                                    'PLG_HANDLERS_HUBPRESENTER_CONTROL_SETTINGS_PLAYBACK_RATE_2',
                                                ) ?>
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <!-- <div class="grid">
                                    <div class="col span6 label">
                                        <label for="theme">Player Theme:</label>
                                    </div>
                                    <div class="col span6 omega input">
                                        <select id="theme">
                                            <option value="dark">Dark (default)</option>
                                        </select>
                                    </div>
                                </div> -->
                            </div>
                        </a>
                        <a id="link" class="control" href="javascript:void(0);"
                            title="<?= Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_LINK_THIS_SPOT') ?>">
                            <?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_LINK'); ?>
                            <div class="control-container link-controls">
                                <?php
                                $headingText = Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_LINK_TO_VIDEO');
                                $spanText = Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_LINK_TO_VIDEO_AT_POSITION');
                                ?>
                                <h3><?= $headingText ?> <span><?= $spanText ?></span></h3>
                                <div class="grid">
                                    <div class="col span12 omega">
                                        <input type="text" value="ss" />
                                        <?php
                                        $hintText = Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_LINK_HINT');
                                        ?>
                                        <span class="hint"><?= $hintText ?></span>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <?php
                        $titleText = Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_SWITCH_VIDEO_SLIDES');
                        ?>
                        <a id="switch" class="control" href="javascript:void(0);"
                            title="<?= $titleText ?>"><?= Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_SWITCH') ?></a>
                    </div>
                </div>
            </div><!-- /#control-box -->
        </div><!-- /#left -->
        <?php $cls = (isset($presentation->videoPosition)
                        && $presentation->videoPosition == "left"
                        && strtolower($presentation->type) == 'video') ? "move-left" : ""; ?>
        <div id="presenter-right">
            <div id="media" class="<?php echo $cls; ?>">
                <?php if (strtolower($presentation->type) == 'video') : ?>
                    <video id="player" preload="auto" controls="controls"
                        data-mediaid="<?= (isset($this->entityId)) ? $this->entityId : 0 ?>"
                        data-mediatype="<?= (isset($this->entityType)) ? $this->entityType : '' ?>">
                        <?php foreach ($presentation->media as $source) : ?>
                            <?php
                                $type = '';
                            switch (strtolower($source->type)) {
                                case 'm4v':
                                case 'mp4':
                                    $type = 'video/mp4;';
                                    break;
                                case 'ogv':
                                    $type = 'video/ogg;';
                                    break;
                                case 'webm':
                                    $type = 'video/webm;';
                                    break;
                            }
                            ?>
                            <source src="<?= with(new Moderator($content_folder . DS . $source->source))->getUrl() ?>"
                                type="<?= $type ?>">
                        <?php endforeach; ?>
                        <?php
                        $linkUrl = with(new Moderator($content_folder . DS . $presentation->media[0]->source))
                            ->getUrl();
                        ?>
                        <a href="<?= $linkUrl ?>" id="flowplayer"></a>

                        <?php if (isset($subs) && count($subs) > 0) : ?>
                            <?php foreach ($subs as $sub) : ?>
                                <div
                                    data-autoplay="<?php echo (int) $sub->autoplay; ?>"
                                    data-type="subtitle"
                                    data-lang="<?php echo $this->escape($sub->name); ?>"
                                    data-src="<?= $this->escape($sub->source) ?>?v=<?= (is_file($sub->source) ? filemtime($sub->source) : 0) ?>"></div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </video>
                <?php else : ?>
                    <audio id="player" preload="auto" controls="controls">
                        <?php foreach ($presentation->media as $source) : ?>
                            <source
                                src="<?= with(new Moderator($content_folder . DS . $source->source))->getUrl() ?>" />
                        <?php endforeach; ?>
                        <?php
                        $linkUrl = with(new Moderator($content_folder . DS . $presentation->media[0]->source))
                            ->getUrl();
                        $duration = isset($presentation->duration) && $presentation->duration
                            ? $this->escape($presentation->duration)
                            : '';
                        ?>
                        <a href="<?= $linkUrl ?>" id="flowplayer" duration="<?= $duration ?>"></a>
                    </audio>
                <?php endif; ?>
                <div id="video-subtitles"></div>
            </div>
            <div id="list">
                <ul id="list_items">
                    <?php
                    $num = 0;
                    $counter = 0;
                    $last_slide_id = 0;
                    ?>
                    <?php foreach ($presentation->slides as $slide) : ?>
                        <?php if ((int) $slide->slide != $last_slide_id) : ?>
                            <li id="list_<?php echo $counter; ?>">
                                <?php
                                    // Use thumb if possible
                                    $thumb = '';
                                if (
                                    isset($slide->thumb)
                                    && $slide->thumb
                                    && file_exists($content_folder . DS . $slide->thumb)
                                ) {
                                    $thumb = $content_folder . DS . $slide->thumb;
                                } elseif (
                                    !is_array($slide->media) && file_exists($content_folder . DS . $slide->media)
                                ) {
                                    $thumb = $content_folder . DS . $slide->media;
                                }
                                ?>
                                <img src="<?= with(new Moderator($thumb))->getUrl() ?>"
                                    alt="<?= $this->escape($slide->title) ?>" />
                                <span>
                                    <?php
                                        $num++;
                                        $max = 30;
                                        $elipsis = '&hellip;';
                                        echo ($num) . '. ';
                                        echo $this->escape(substr($slide->title, 0, $max));

                                    if (strlen($slide->title) > $max) {
                                        echo $elipsis;
                                    }
                                    ?>
                                </span>
                                <span class="time"><?php echo $this->escape($slide->time); ?></span>
                                <div id="list-slider-<?php echo $counter; ?>" class="list-slider"></div>
                                <div class="list-progress">00:00/00:00</div>
                            </li>
                        <?php endif; ?>
                        <?php
                            $last_slide_id = $slide->slide;
                            $counter++;
                        ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div><!-- /#right -->
    </div><!-- /#content -->
    <div id="transcript-container">
        <div id="transcript-toolbar">
            <div id="transcript-select"></div>
            <input type="text" id="transcript-search" placeholder="Search Transcript..." />
            <a href="javascript:void(0);" id="font-bigger"></a>
            <a href="javascript:void(0);" id="font-smaller"></a>
        </div>
        <div id="transcripts"></div>
    </div>
</div>