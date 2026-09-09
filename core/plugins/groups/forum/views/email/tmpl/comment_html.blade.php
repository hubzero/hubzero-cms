{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$base = rtrim(Request::base(), '/');
$sef  = Route::url($thread->link());
$link = $base . '/' . trim($sef, '/');

$bgcolor = '#f1f1f1';
$bdcolor = '#e1e1e1';
@endphp

@if ($delimiter)
    @if (Component::params('com_groups')->get('email_comment_processing'))
    <!-- Start Header Spacer -->
    <table class="tbl-delimiter"
        width="100%"
        cellpadding="0"
        cellspacing="0"
        border="0"
        style="border: 1px dashed #b5c6b5;">
        <tbody>
            <tr>
                <td height="30" style="border-collapse: collapse; color: #9bac9b;">
                    <div style="height: 0px; overflow: hidden; color: #fff; visibility: hidden;">
                        {{ $delimiter }}
                    </div>
                    <div style="text-align: center; font-size: 90%; display: block; padding: 1em;">
                        {{ Lang::txt('PLG_GROUPS_FORUM_EMAIL_REPLY_ABOVE') }}
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    @endif

    <!-- End Header Spacer -->

    <!-- Start Spacer -->
    <table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tbody>
            <tr>
                <td height="30"></td>
            </tr>
        </tbody>
    </table>
    <!-- End Spacer -->
@endif

    <!-- Start Header -->
    <table class="tbl-header" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tbody>
            <tr>
                <td width="10%" align="left" valign="bottom" nowrap="nowrap" class="sitename">
                    {{ Config::get('sitename') }}
                </td>
                <td width="80%" align="left" valign="bottom" class="tagline mobilehide">
                    <span class="home">
                        <a href="{{ Request::base() }}">{{ Request::base() }}</a>
                    </span>
                    <br />
                    <span class="description">{{ Config::get('MetaDesc') }}</span>
                </td>
                <td width="10%" align="right" valign="bottom" nowrap="nowrap" class="component">
                    {{ Lang::txt('COM_GROUPS') }}
                </td>
            </tr>
        </tbody>
    </table>
    <!-- End Header -->

    <!-- Start Spacer -->
    <table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tbody>
            <tr>
                <td height="30"></td>
            </tr>
        </tbody>
    </table>
    <!-- End Spacer -->

    @php
    $tableStyle = 'border-collapse: collapse;'
        . ' border: 1px solid ' . $bdcolor . ';'
        . ' background: ' . $bgcolor . ';'
        . ' font-size: 0.9em;'
        . ' line-height: 1.6em;'
        . ' background-image: -webkit-gradient('
        . 'linear, 0 0, 100% 100%,'
        . ' color-stop(.25, rgba(255, 255, 255, .075)),'
        . ' color-stop(.25, transparent),'
        . ' color-stop(.5, transparent),'
        . ' color-stop(.5, rgba(255, 255, 255, .075)),'
        . ' color-stop(.75, rgba(255, 255, 255, .075)),'
        . ' color-stop(.75, transparent),'
        . ' to(transparent));'
        . ' background-image: -webkit-linear-gradient('
        . '-45deg, rgba(255, 255, 255, .075) 25%,'
        . ' transparent 25%, transparent 50%,'
        . ' rgba(255, 255, 255, .075) 50%,'
        . ' rgba(255, 255, 255, .075) 75%,'
        . ' transparent 75%, transparent);'
        . ' background-image: -moz-linear-gradient('
        . '-45deg, rgba(255, 255, 255, .075) 25%,'
        . ' transparent 25%, transparent 50%,'
        . ' rgba(255, 255, 255, .075) 50%,'
        . ' rgba(255, 255, 255, .075) 75%,'
        . ' transparent 75%, transparent);'
        . ' background-image: -ms-linear-gradient('
        . '-45deg, rgba(255, 255, 255, .075) 25%,'
        . ' transparent 25%, transparent 50%,'
        . ' rgba(255, 255, 255, .075) 50%,'
        . ' rgba(255, 255, 255, .075) 75%,'
        . ' transparent 75%, transparent);'
        . ' background-image: -o-linear-gradient('
        . '-45deg, rgba(255, 255, 255, .075) 25%,'
        . ' transparent 25%, transparent 50%,'
        . ' rgba(255, 255, 255, .075) 50%,'
        . ' rgba(255, 255, 255, .075) 75%,'
        . ' transparent 75%, transparent);'
        . ' background-image: linear-gradient('
        . '-45deg, rgba(255, 255, 255, .075) 25%,'
        . ' transparent 25%, transparent 50%,'
        . ' rgba(255, 255, 255, .075) 50%,'
        . ' rgba(255, 255, 255, .075) 75%,'
        . ' transparent 75%, transparent);'
        . ' -webkit-background-size: 30px 30px;'
        . ' -moz-background-size: 30px 30px;'
        . ' background-size: 30px 30px;';
    @endphp
    <table id="ticket-info"
        width="100%"
        cellpadding="0"
        cellspacing="0"
        border="0"
        style="{{ $tableStyle }}">
        <tbody>
            <tr>
                <td width="100%" style="padding: 8px;">
                    <div class="mobilehide"
                        id="ticket-number"
                        style="float: left; font-weight: bold; text-align: center; padding: 10px;"
                        align="center">
                        @if ($group->get('logo'))
                            @php
                            $logoSrc = rtrim(Request::root(), '/') . '/' . ltrim($group->getLogo(), '/');
                            @endphp
                            <img src="{{ $logoSrc }}"
                                width="100"
                                alt="{{ e($group->get('description')) }}" />
                        @else
                            <div style="width: 1.2em; font-size: 4em; padding: 20px;">&#8220;</div>
                        @endif
                    </div>
                    <table style="border-collapse: collapse; font-size: 0.9em;"
                        cellpadding="0"
                        cellspacing="0"
                        border="0">
                        <tbody>
                            <tr>
                                <th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;"
                                    align="right">{{ Lang::txt('PLG_GROUPS_FORUM_DETAILS_THREAD') }}:</th>
                                <td style="text-align: left; padding: 0 0.5em;"
                                    align="left">{{ e($thread->get('title')) }}</td>
                            </tr>
                            <tr>
                                <th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;"
                                    align="right">{{ Lang::txt('PLG_GROUPS_FORUM_DETAILS_CREATED') }}:</th>
                                <td style="text-align: left; padding: 0 0.5em;"
                                    align="left">{{ Lang::txt('PLG_GROUPS_FORUM_CREATED', $thread->created('time'), $thread->created('date')) }}</td>
                            </tr>
                            <tr>
                                <th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;"
                                    align="right">{{ Lang::txt('PLG_GROUPS_FORUM_DETAILS_GROUP') }}:</th>
                                <td style="text-align: left; padding: 0 0.5em;"
                                    align="left">{{ e($group->get('description')) }}</td>
                            </tr>
                            <tr>
                                <th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;"
                                    align="right">{{ Lang::txt('PLG_GROUPS_FORUM_DETAILS_SECTION') }}:</th>
                                <td style="text-align: left; padding: 0 0.5em;"
                                    align="left">{{ e($section->get('title')) }}</td>
                            </tr>
                            <tr>
                                <th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;"
                                    align="right">{{ Lang::txt('PLG_GROUPS_FORUM_DETAILS_CATEGORY') }}:</th>
                                <td style="text-align: left; padding: 0 0.5em;"
                                    align="left">{{ e($category->get('title')) }}</td>
                            </tr>
                            <tr>
                                <th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;"
                                    align="right">{{ Lang::txt('PLG_GROUPS_FORUM_DETAILS_LINK') }}:</th>
                                <td style="text-align: left; padding: 0 0.5em;"
                                    align="left"><a href="{{ $link }}">{{ $link }}</a></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <table width="100%"
        id="ticket-comments"
        style="border-collapse: collapse; margin: 2em 0 0 0; padding: 0"
        cellpadding="0"
        cellspacing="0"
        border="0">
        <tbody>
            @php
            $posterName = (!$post->get('anonymous'))
                ? $post->creator->get('name')
                : Lang::txt('JANONYMOUS');
            $postCreated = Lang::txt(
                'PLG_GROUPS_FORUM_CREATED',
                $post->created('time'),
                $post->created('date')
            );
            @endphp
            <tr>
                <th style="text-align: left;" align="left">
                    {{ $posterName }}
                </th>
                <th class="timestamp"
                    style="color: #999; text-align: right;"
                    align="right">
                    <span class="mobilehide">
                        {{ $postCreated }}
                    </span>
                </th>
            </tr>
            <tr>
                <td colspan="2" style="padding: 0 2em;">
                    <div style="line-height: 1.6em; margin: 0; padding: 0; text-align: left;">
                        {!! $post->comment !!}
                    </div>
                    @php
                    $attachments = $post->attachments()
                        ->whereIn('state', [\Components\Forum\Models\Post::STATE_PUBLISHED])
                        ->rows();
                    @endphp
                    @if ($attachments->count() > 0)
                        <div class="comment-attachments" style="margin: 2em 0 0 0; padding: 0; text-align: left;">
                            @foreach ($attachments as $attachment)
                                @php
                                if (!trim($attachment->get('description'))) {
                                    $attachment->set('description', $attachment->get('filename'));
                                }
                                $attachUrl = $base . '/' . trim(Route::url($thread->link()), '/')
                                    . '/' . $attachment->get('post_id')
                                    . '/' . $attachment->get('filename');
                                $attachClass = $attachment->isImage() ? 'img' : 'file';
                                @endphp
                                <p class="attachment" style="margin: 0.5em 0; padding: 0; text-align: left;">
                                    <a class="{{ $attachClass }}"
                                        data-filename="{{ $attachment->get('filename') }}"
                                        href="{{ $attachUrl }}">{{ $attachment->get('description') }}</a>
                                </p>
                            @endforeach
                        </div>
                    @endif
                </td>
            </tr>
            @if ($unsubscribe)
                <tr>
                    <td colspan="2" style="padding: 2em 0 0 0; font-size: 0.9em;">
                        {{ Lang::txt('PLG_GROUPS_FORUM_EMAIL_UNSUBSCRIBE') }}:
                        <br />
                        <a href="{{ $__view->get('unsubscribe') }}">
                            {{ $__view->get('unsubscribe') }}
                        </a>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Start Spacer -->
    <table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tbody>
            <tr>
                <td height="30"></td>
            </tr>
        </tbody>
    </table>
    <!-- End Spacer -->
