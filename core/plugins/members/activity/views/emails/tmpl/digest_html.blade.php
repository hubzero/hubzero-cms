{{--
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
--}}

@php
$base = rtrim(Request::base(), '/');
$sef  = Route::url($member->link());
$link = $base . '/' . trim($sef, '/');
$memberLabel = $member->get('name') . ' (' . $member->get('username') . ')';
@endphp

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
                    {{ Lang::txt('COM_MEMBERS') }}
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

    <!-- Start Header -->
    <table class="tbl-message" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tbody>
            <tr>
                <td align="left"
                    valign="bottom"
                    style="border-collapse: collapse; color: #666; line-height: 1; padding: 5px; text-align: center;">
                    {!! Lang::txt('PLG_MEMBERS_ACTIVITY_EMAIL_MEMBERS_EXPLANATION', $link, $memberLabel) !!}
                </td>
            </tr>
        </tbody>
    </table>
    <!-- End Header -->

    <!-- Start Spacer -->
    <table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tbody>
            <tr>
                <td height="20"></td>
            </tr>
        </tbody>
    </table>
    <!-- End Spacer -->

@foreach ($rows as $row)
    @php
        $border  = '#e1e1e1';
        $bground = '#f1f1f1';

        if ($row->log->get('action') == 'deleted') {
            $border  = '#e9e1bc';
            $bground = '#fbf1be';
        }
        if ($row->log->get('action') == 'created') {
            $border  = '#c8e3c2';
            $bground = '#eafbe6';
        }

        $tableStyle = 'border-collapse: collapse;'
            . ' border: 1px solid ' . $border . ';'
            . ' background: ' . $bground . ';'
            . ' font-size: 0.9em; line-height: 1.6em;'
            . ' background-image: -webkit-gradient(linear, 0 0, 100% 100%,'
            . ' color-stop(.25, rgba(255, 255, 255, .075)), color-stop(.25, transparent),'
            . ' color-stop(.5, transparent), color-stop(.5, rgba(255, 255, 255, .075)),'
            . ' color-stop(.75, rgba(255, 255, 255, .075)), color-stop(.75, transparent),'
            . ' to(transparent));'
            . ' background-image: linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%,'
            . ' transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%,'
            . ' transparent 75%, transparent);'
            . ' background-size: 30px 30px;';

        $name = Lang::txt('JANONYMOUS');

        if (!$row->log->get('anonymous')) {
            $creator = User::getInstance($row->log->get('created_by'));
            $name = e(stripslashes($creator->get(
                'name',
                Lang::txt('PLG_MEMBERS_ACTIVITY_UNKNOWN')
            )));

            if (in_array($creator->get('access'), User::getAuthorisedViewLevels())) {
                $name = '<a href="'
                    . $base
                    . '/'
                    . trim(Route::url($creator->link()), '/')
                    . '">'
                    . $name
                    . '</a>';
            }
        }

        $dt = Date::of($row->get('created'));
        $ct = Date::of('now');
        $lapsed = $ct->toUnix() - $dt->toUnix();

        if ($lapsed < 30) {
            $timestamp = Lang::txt('PLG_MEMBERS_ACTIVITY_JUST_NOW');
        } elseif ($lapsed > 86400 && $ct->format('Y') != $dt->format('Y')) {
            $timestamp = $dt->toLocal('M j, Y');
        } elseif ($lapsed > 86400) {
            $timestamp = $dt->toLocal('M j') . ' @ ' . $dt->toLocal('g:i a');
        } else {
            $timestamp = $dt->relative();
        }
    @endphp

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="{{ $tableStyle }}">
        <tbody>
            <tr>
                <td width="15%" style="padding: 8px; font-size: 1.2em; font-weight: bold; text-align: right; vertical-align: middle; padding: 8px 30px;" valign="middle" align="center">
                    {{ $row->log->get('action') }}
                </td>
                <td style="font-weight: normal; padding: 8px; text-align: left;" align="left">
                    <span class="activity-actor">
                        {!! $name !!}
                    </span><br />
                    <span class="activity-event">
                        {!! str_replace('href="/', 'href="' . $base . '/', $row->log->get('description')) !!}
                    </span>
                </td>
                <td width="25%" style="padding: 8px; font-size: 1.2em; text-align: right; vertical-align: middle; padding: 8px 30px;" valign="top" align="right">
                    <span class="activity-time"><time datetime="{{ $row->get('created') }}">{{ $timestamp }}</time></span>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Start Spacer -->
    <table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tbody>
            <tr>
                <td height="10"></td>
            </tr>
        </tbody>
    </table>
    <!-- End Spacer -->
@endforeach

    <!-- Start Spacer -->
    <table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tbody>
            <tr>
                <td height="20"></td>
            </tr>
        </tbody>
    </table>
    <!-- End Spacer -->
