{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\App;
use Hubzero\Facades\Component;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;
use Hubzero\Facades\Event;

$__view->css()
     ->js();

$txt  = '';
$mode = strtolower(Request::getWord('mode', ''));

if ($mode != 'preview') {
    switch ($model->published) {
        case 1:
            $txt .= '';
            break;
        case 2:
            $txt .= '<span>[' . Lang::txt('COM_RESOURCES_DRAFT_EXTERNAL') . ']</span> ';
            break;
        case 3:
            $txt .= '<span>[' . Lang::txt('COM_RESOURCES_PENDING') . ']</span> ';
            break;
        case 4:
            $txt .= '<span>[' . Lang::txt('COM_RESOURCES_DELETED') . ']</span> ';
            break;
        case 5:
            $txt .= '<span>[' . Lang::txt('COM_RESOURCES_DRAFT_INTERNAL') . ']</span> ';
            break;
        case 0:
            $txt .= '<span>[' . Lang::txt('COM_RESOURCES_UNPUBLISHED') . ']</span> ';
            break;
    }
}
@endphp

<x-page-container class="main section upperpane {{ $model->params->get('pageclass_sfx', '') }}">
    <div class="subject">
        <div class="grid overviewcontainer">
            <div class="col span8">
                <header id="content-header">
                    <h2>
                        {!! $txt !!}{{ e(stripslashes($model->title)) }}
                        @if ($model->params->get('access-edit-resource'))
                            @php
                            $hrefUrl = Route::url(
                                'index.php?option=com_resources&task=draft&step=1&id='
                                . $model->id
                            );
                            @endphp
                            <a class="icon-edit edit btn" href="{{ $hrefUrl }}">
                                {{ Lang::txt('COM_RESOURCES_EDIT') }}
                            </a>
                        @endif
                    </h2>
                    <input type="hidden" name="rid" id="rid" value="{{ $model->id }}" />
                </header>

                @if ($model->params->get('show_authors', 1))
                    <div id="authorslist">
                        @include('view::_contributors', [
                            'option' => $option,
                            'contributors' => $model->contributors('!submitter'),
                        ])
                    </div>
                @endif
            </div>

            <div class="col span4 omega launcharea">
                @if (!$model->access('view-all'))
                    @php
                    $ghtml = array();
                    foreach ($model->groups as $allowedgroup) {
                        $ghtml[] = '<a href="' . Route::url('index.php?option=com_groups&cn=' . $allowedgroup) .
                        '">' . $allowedgroup . '</a>';
                    }
                    @endphp
                    <div role="alert" class="alert alert-warning">
                        @if (User::isGuest())
                            {!! Lang::txt(
                                'COM_RESOURCES_ERROR_MUST_BE_LOGGED_IN',
                                base64_encode(Request::path())
                            ) !!}
                        @elseif ($__view->get('group_owner'))
                            {!! Lang::txt('COM_RESOURCES_ERROR_MUST_BE_PART_OF_GROUP') . ' '
                                . implode(', ', $ghtml) !!}
                        @else
                            {{ Lang::txt('COM_RESOURCES_ALERTNOTAUTH') }}
                        @endif
                    </div>
                @else
                    @php
                    // Get summary usage data
                    $startdate = new DateTime('midnight first day of this month');
                    $enddate   = new DateTime('midnight first day of next month');
                    $db = App::get('db');
                    $sql  = "SELECT truncate(sum(walltime)/60/60,3) as totalhours FROM `sessionlog`";
                    $sql .= " WHERE start > " . $db->quote($startdate->format('Y-m-d H:i:s'));
                    $sql .= " AND start < " . $db->quote($enddate->format('Y-m-d H:i:s'));
                    $db->setQuery($sql);
                    $totalhours = $db->loadResult();

                    $params = Component::params('com_tools');
                    $maxhours = $params->get('windows_monthly_max_hours', '100');
                    @endphp

                    @if (floatval($totalhours) < floatval($maxhours))
                        @php
                        $lurl = Route::url('index.php?option=' . $option .
                            '&task=plugin&trigger=invoke&appid=' . $model->path);
                        @endphp
                        <a class="btn btn-primary"
                           href="{{ $lurl }}"
                           data-launch-url="{{ str_replace('&amp;', '&', $lurl) }}">{{ Lang::txt('COM_RESOURCES_LAUNCH_TOOL') }}</a>
                        @if ($tab != 'play')
                            {!! \Components\Resources\Helpers\Html::license($model->params->get('license', '')) !!}
                        @endif
                        <div role="alert" class="alert alert-info">
                            Read the <a href="{{ Route::url($model->link() . '&active=windowstools') }}">setup/instructions</a>.
                        </div>
                    @else
                        @php
                        $html = \Components\Resources\Helpers\Html::primaryButton(
                            '',
                            '',
                            Lang::txt('COM_RESOURCES_LAUNCH_TOOL')
                        );
                        @endphp
                        {!! $html !!}
                        AppStream tool usage over limit. Please contact the system administrator.
                        <br/>{{ $totalhours }}/{{ $maxhours }}
                    @endif
                @endif
            </div>
        </div>

        @include('view::_canonical', [
            'option' => $option,
            'model' => $model,
        ])
    </div>

    @slot('sidebar')
        @if ($model->params->get('show_metadata', 1))
            @include('view::_metadata', [
                'option' => $option,
                'sections' => $sections,
                'model' => $model,
            ])
        @endif
    @endslot
</x-page-container>

@if ($model->access('view'))
    <section class="main section {{ $model->params->get('pageclass_sfx', '') }}">
        <div class="section-inner hz-layout-with-aside">
            <div class="subject tabbed">
                @include('view::_tabs', [
                    'option' => $option,
                    'cats' => $cats,
                    'resource' => $model,
                    'active' => $tab,
                ])

                @include('view::_sections', [
                    'option' => $option,
                    'sections' => $sections,
                    'resource' => $model,
                    'active' => $tab,
                ])
            </div>
            <aside class="aside extracontent">
                @php
                $out = Event::trigger('resources.onResourcesSub', array($model, $option, 1));
                if (count($out) > 0) {
                    foreach ($out as $ou) {
                        if (isset($ou['html'])) {
                            echo $ou['html'];
                        }
                    }
                }
                if ($tab == 'about') {
                    echo \Hubzero\Module\Helper::renderModules('extracontent');
                }
                @endphp
            </aside>
        </div>
    </section>
@endif
