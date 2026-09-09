{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
$__view->css();

$config = Component::params('com_resources');

// Build category links
$links = [];
if ($cats) {
    foreach ($cats as $cat) {
        if ($cat['total'] > 0) {
            $a = ($cat['category'] == $active) ? ' class="active"' : '';
            $blob = $cat['category'] ?: '';
            $catUrl = Route::url(
                'index.php?option=' . $option
                . '&cn=' . $group->get('cn')
                . '&active=resources&area='
                . urlencode(stripslashes($blob))
            );
            $catTitle = e(stripslashes($cat['title']));
            $l = "\t" . '<li' . $a . '>'
                . '<a href="' . $catUrl
                . '&limit=' . $limit . '">'
                . $catTitle
                . ' <span class="badge badge-sm">'
                . $cat['total'] . '</span></a>';

            if (isset($cat['_sub']) && is_array($cat['_sub'])) {
                $k = [];
                foreach ($cat['_sub'] as $subcat) {
                    if ($subcat['total'] > 0) {
                        $sa = ($subcat['category'] == $active) ? ' class="active"' : '';
                        $sblob = $subcat['category'] ?: '';
                        $subUrl = Route::url(
                            'index.php?option=' . $option
                            . '&cn=' . $group->get('cn')
                            . '&active=resources&area='
                            . urlencode(stripslashes($sblob))
                        );
                        $subTitle = e(stripslashes($subcat['title']));
                        $k[] = "\t\t\t" . '<li' . $sa . '>'
                            . '<a href="' . $subUrl
                            . '&limit=' . $limit . '">'
                            . $subTitle
                            . ' <span class="badge badge-sm">'
                            . $subcat['total']
                            . '</span></a></li>';
                    }
                }
                if (count($k) > 0) {
                    $l .= "\t\t" . '<ul>' . "\n";
                    $l .= implode("\n", $k);
                    $l .= "\t\t" . '</ul>' . "\n";
                }
            }
            $l .= '</li>';
            $links[] = $l;
        }
    }
}

$cn = $group->get('cn');
$activeArea = urlencode(stripslashes($active));
$baseParams = 'index.php?option=' . $option
    . '&cn=' . $cn
    . '&active=resources&area=' . $activeArea
    . '&sort=' . $sort;
$sortBase = 'index.php?option=' . $option
    . '&cn=' . $cn
    . '&active=resources&area=' . $activeArea;
@endphp

@if ($group->published == 1)
    @php
    $draftUrl = Route::url(
        'index.php?option=com_resources&task=draft&group='
        . $group->get('cn')
    );
    @endphp
    <ul id="page_options" class="mb-4">
        <li>
            <a class="btn btn-primary"
               href="{{ $draftUrl }}">
                {{ Lang::txt('PLG_GROUPS_RESOURCES_START_A_CONTRIBUTION') }}
            </a>
        </li>
    </ul>
@endif

<section class="section">
    @php
    $formUrl = Route::url(
        'index.php?option=' . $option
        . '&cn=' . $group->get('cn')
        . '&active=resources'
    );
    @endphp
    <form method="get" action="{{ $formUrl }}">

        <input type="hidden" name="area" value="{{ e($active) }}" />

        <div class="container">
            <nav class="entries-filters"
                 aria-label="{{ Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS') }}">
                <ul class="entries-menu filter-options flex flex-wrap gap-2 mb-4">
                    @if (count($links) > 0)
                        @php
                        $catFilterUrl = Route::url(
                            'index.php?option=' . $option
                            . '&cn=' . $cn
                            . '&active=resources&area='
                            . urlencode(stripslashes($active))
                            . '&sort=' . $sort
                            . '&access=' . $active
                            . '&limit=' . $limit
                        );
                        @endphp
                        <li class="filter-categories">
                            <a href="{{ $catFilterUrl }}">
                                {{ Lang::txt('PLG_GROUPS_RESOURCES_CATEGORIES') }}
                            </a>
                            <ul>
                                {!! implode("\n", $links) !!}
                            </ul>
                        </li>
                    @endif

                    @php
                    $accessOptions = ['all', 'public', 'protected', 'private', 'shared'];
                    $accessKeys = [
                        'all'       => 'PLG_GROUPS_RESOURCES_ACCESS_ALL',
                        'public'    => 'PLG_GROUPS_RESOURCES_ACCESS_PUBLIC',
                        'protected' => 'PLG_GROUPS_RESOURCES_ACCESS_PROTECTED',
                        'private'   => 'PLG_GROUPS_RESOURCES_ACCESS_PRIVATE',
                        'shared'    => 'PLG_GROUPS_RESOURCES_ACCESS_SHARED',
                    ];
                    @endphp
                    @foreach ($accessOptions as $opt)
                        @php
                        $optClass = ($access == $opt) ? 'active' : '';
                        $optUrl = Route::url(
                            $baseParams . '&access=' . $opt
                            . '&limit=' . $limit
                        );
                        @endphp
                        <li>
                            <a class="{{ $optClass }}"
                                href="{{ $optUrl }}">
                                {{ Lang::txt($accessKeys[$opt]) }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                <ul class="entries-menu flex gap-2 mb-4">
                    {{-- Date sort --}}
                    @php
                    $dateClass = ($sort == 'date')
                        ? 'active ' . ($sortdir == 'desc' ? 'icon-arrow-up' : 'icon-arrow-down')
                        : 'icon-arrow-down';
                    $dateSortdir = ($sort == 'date')
                        ? ($sortdir == 'desc' ? 'asc' : 'desc')
                        : 'asc';
                    $dateUrl = Route::url(
                        $sortBase . '&sort=date'
                        . '&sortdir=' . $dateSortdir
                        . '&access=' . $access
                        . '&limit=' . $limit
                    );
                    @endphp
                    <li>
                        <a class="{{ $dateClass }}"
                           href="{{ $dateUrl }}"
                           title="Sort by newest to oldest">
                            {{ Lang::txt('PLG_GROUPS_RESOURCES_SORT_BY_DATE') }}
                        </a>
                    </li>

                    {{-- Title sort --}}
                    @php
                    $titleClass = ($sort == 'title')
                        ? 'active ' . ($sortdir == 'desc' ? 'icon-arrow-up' : 'icon-arrow-down')
                        : 'icon-arrow-down';
                    $titleSortdir = ($sort == 'title')
                        ? ($sortdir == 'desc' ? 'asc' : 'desc')
                        : 'asc';
                    $titleUrl = Route::url(
                        $sortBase . '&sort=title'
                        . '&sortdir=' . $titleSortdir
                        . '&access=' . $access
                        . '&limit=' . $limit
                    );
                    @endphp
                    <li>
                        <a class="{{ $titleClass }}"
                           href="{{ $titleUrl }}"
                           title="Sort by title">
                            {{ Lang::txt('PLG_GROUPS_RESOURCES_SORT_BY_TITLE') }}
                        </a>
                    </li>

                    {{-- Ranking or Rating sort --}}
                    @if ($config->get('show_ranking'))
                        @php
                        $rankClass = ($sort == 'ranking')
                            ? 'active ' . ($sortdir == 'desc' ? 'icon-arrow-up' : 'icon-arrow-down')
                            : 'icon-arrow-down';
                        $rankSortdir = ($sort == 'ranking')
                            ? ($sortdir == 'desc' ? 'asc' : 'desc')
                            : 'asc';
                        $rankUrl = Route::url(
                            $sortBase . '&sort=ranking'
                            . '&sortdir=' . $rankSortdir
                            . '&access=' . $access
                            . '&limit=' . $limit
                        );
                        @endphp
                        <li>
                            <a class="{{ $rankClass }}"
                               href="{{ $rankUrl }}"
                               title="Sort by popularity">
                                {{ Lang::txt('PLG_GROUPS_RESOURCES_SORT_BY_RANKING') }}
                            </a>
                        </li>
                    @else
                        @php
                        $rateClass = ($sort == 'rating')
                            ? 'active ' . ($sortdir == 'desc' ? 'icon-arrow-up' : 'icon-arrow-down')
                            : 'icon-arrow-down';
                        $rateSortdir = ($sort == 'rating')
                            ? ($sortdir == 'desc' ? 'asc' : 'desc')
                            : 'asc';
                        $rateUrl = Route::url(
                            $sortBase . '&sort=rating'
                            . '&sortdir=' . $rateSortdir
                            . '&access=' . $access
                            . '&limit=' . $limit
                        );
                        @endphp
                        <li>
                            <a class="{{ $rateClass }}"
                               href="{{ $rateUrl }}"
                               title="Sort by popularity">
                                {{ Lang::txt('PLG_GROUPS_RESOURCES_SORT_BY_RATING') }}
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>

            <div class="container-block">
                @php
                $html = '';
                $k = 0;
                foreach ($results as $category) {
                    $amt = count($category);
                    if ($amt > 0) {
                        $html .= '<ol class="resources results">' . "\n";
                        foreach ($category as $row) {
                            $k++;
                            $html .= $__view->view('_item')
                                        ->set('row', $row)
                                        ->set('authorized', $authorized)
                                        ->loadTemplate();
                        }
                        $html .= '</ol>' . "\n";
                    }
                }
                echo $html;

                if (!$k) {
                    echo '<div class="alert alert-warning">'
                        . Lang::txt('PLG_GROUPS_RESOURCES_NONE')
                        . '</div>';
                }
                @endphp
            </div>{{-- / .container-block --}}
            @php
            $pageNav = $__view->pagination(
                $total,
                $limitstart,
                $limit
            );
            $pageNav->setAdditionalUrlParam('cn', $group->get('cn'));
            $pageNav->setAdditionalUrlParam('active', 'resources');
            $pageNav->setAdditionalUrlParam('area', urlencode(stripslashes($active)));
            $pageNav->setAdditionalUrlParam('sort', $sort);
            $pageNav->setAdditionalUrlParam('access', $access);
            echo $pageNav->render();
            @endphp
            <div class="clearfix"></div>
        </div>{{-- / .container --}}
    </form>
</section>
