{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 --}}

@php
use Hubzero\Facades\Route;

$__view->css()
    ->css('jquery.fancybox.css', 'system')
    ->js();

$imageUrl = Route::url(
    'index.php?option=com_publications&id=' . $publication->id
    . '&v=' . $publication->version_number
) . '/Image:master';

$elements = $publication->_curationModel->getElements(1);
$attModel = new \Components\Publications\Models\Attachments($database);
@endphp

<div class="launcher-image">
    <div class="imager" style="background-image: url('{{ $imageUrl }}');"> </div>
</div>
<section id="launcher" class="main section launcher bg-gradient-to-r from-primary to-primary/80 text-primary-content">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6">
        <div class="lg:col-span-3">
            <div class="launcher-inside-wrap">
                {!! \Components\Publications\Helpers\Html::showSubInfo($publication) !!}
                <h3>{{ \Hubzero\Utility\Str::truncate(stripslashes($publication->title), 150) }}</h3>
                {{-- Authors --}}
                @if ($publication->params->get('show_authors') && $publication->_authors)
                    <div id="authorslist">
                        {!! \Components\Publications\Helpers\Html::showContributors($publication->_authors, true, false) !!}
                    </div>
                @endif
                {{-- Abstract --}}
                @if ($publication->abstract)
                    <p class="ataglance text-primary-content/80">{{ \Hubzero\Utility\Str::truncate(stripslashes($publication->abstract), 250) }}</p>
                @endif
            </div>
        </div>
        <div class="lg:col-span-2 launch-wrap">
            {{-- Launch button --}}
            @if ($elements)
                @php
                    $element = $elements[0];
                @endphp
                {!! $attModel->drawLauncher($element->manifest->params->type, $publication, $element, $elements, $publication->access('view-all')) !!}
            @endif
            <div class="version-info">
                {!! \Components\Publications\Helpers\Html::showVersionInfo($publication) !!}
                {!! \Components\Publications\Helpers\Html::showLicense($publication, 'play') !!}
            </div>
        </div>
        <div class="lg:col-span-1">
            <div class="meta">
                @if ($publication->state == 1 && $publication->main == 1)
                    @include('view::_metadata', [
                        'option'          => $option,
                        'publication'     => $publication,
                        'config'          => $config,
                        'version'         => $version,
                        'sections'        => $sections,
                        'cats'            => $cats,
                        'params'          => $publication->params,
                        'lastPubRelease'  => $lastPubRelease,
                        'launcherLayout'  => true,
                    ])
                @endif
            </div>
        </div>
    </div>
</section>
<div class="launcher-notes">
    @if ($contributable)
        {!! \Components\Publications\Helpers\Html::showAccessMessage($publication) !!}
    @endif
</div>
