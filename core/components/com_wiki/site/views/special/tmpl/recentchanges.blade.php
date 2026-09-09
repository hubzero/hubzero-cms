{{--
 * Wiki special page — recent changes listing
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Pathway;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;

    Pathway::append(
        Lang::txt('COM_WIKI_SPECIAL_RECENT_CHANGES'),
        $page->link()
    );

    $dir = strtoupper(Request::getString('dir', 'DESC'));
    if (!in_array($dir, ['ASC', 'DESC'])) {
        $dir = 'DESC';
    }

    $filters = ['state' => [0, 1]];

    if ($space = Request::getString('namespace', '')) {
        $filters['namespace'] = urldecode($space);
    }

    $rows = $book->pages($filters)
        ->including([
            'versions',
            function ($version) {
                $version
                    ->select('id')
                    ->select('page_id')
                    ->select('version')
                    ->select('created_by')
                    ->select('summary');
            }
        ])
        ->order('modified', $dir)
        ->paginated()
        ->rows();

    $altdir  = ($dir == 'ASC') ? 'DESC' : 'ASC';
    $taskKey = ($sub ? 'action' : 'task');
@endphp

<x-page-container :title="e($page->title)">
    @if(!$sub)
        @slot('sidebar')
            {!! $__view->view('_wikimenu', 'pages')
                ->set('option', $option)
                ->set('controller', $controller)
                ->set('page', $page)
                ->set('task', $task)
                ->set('sub', $sub)
                ->loadTemplate() !!}
        @endslot
    @endif

    {!! $__view->view('_submenu', 'pages')
        ->set('option', $option)
        ->set('controller', $controller)
        ->set('page', $page)
        ->set('task', $task)
        ->set('sub', $sub)
        ->loadTemplate() !!}

    <p class="mb-4">
        {{ Lang::txt('COM_WIKI_SPECIAL_RECENT_CHANGES_ABOUT') }}
    </p>

    <form method="get" action="{{ Route::url($page->link(), false) }}">
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th scope="col">
                            {{ Lang::txt('COM_WIKI_COL_DIFF') }}
                        </th>
                        <th scope="col">
                            <a href="{{ Route::url($page->link() . '&sort=modified&dir=' . $altdir, false) }}">
                                {{ Lang::txt('COM_WIKI_COL_DATE') }}
                            </a>
                        </th>
                        <th scope="col">
                            {{ Lang::txt('COM_WIKI_COL_TITLE') }}
                        </th>
                        <th scope="col">
                            {{ Lang::txt('COM_WIKI_COL_CREATOR') }}
                        </th>
                        <th scope="col">
                            {{ Lang::txt('COM_WIKI_COL_EDIT_SUMMARY') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        @php
                            $vCreatorName = $row->version->creator
                                ->get('name', Lang::txt('COM_WIKI_UNKNOWN')) ?? '';
                            $creatorName = e(stripslashes($vCreatorName));
                            $creatorLinked = $creatorName;
                            if (in_array(
                                $row->version->creator->get('access'),
                                User::getAuthorisedViewLevels()
                            )) {
                                $creatorUrl = Route::url(
                                    $row->version->creator->link(),
                                    false
                                );
                                $creatorLinked = '<a class="link link-hover" href="'
                                    . $creatorUrl . '">' . $creatorName . '</a>';
                            }

                            $rowUrl = Route::url($row->link(), false);
                            $histUrl = Route::url(
                                $row->link() . '&' . $taskKey . '=history',
                                false
                            );
                        @endphp
                        <tr>
                            <td>
                                (
                                @if($row->version->get('version') > 1)
                                    @php
                                        $oldId  = $row->version->get('version') - 1;
                                        $diffId = $row->version->get('version');
                                        $compareUrl = Route::url(
                                            $row->link() . '&' . $taskKey . '=compare'
                                            . '&oldid=' . $oldId . '&diff=' . $diffId,
                                            false
                                        );
                                    @endphp
                                    <a class="link link-hover"
                                       href="{{ $compareUrl }}">{{ Lang::txt('COM_WIKI_DIFF') }}</a> |
                                @else
                                    {{ Lang::txt('COM_WIKI_DIFF') }} |
                                @endif
                                <a class="link link-hover"
                                   href="{{ $histUrl }}">{{ Lang::txt('COM_WIKI_HIST') }}</a>
                                )
                            </td>
                            <td>
                                <time datetime="{{ $row->get('modified') }}">
                                    {{ $row->get('modified') }}
                                </time>
                            </td>
                            <td>
                                <a class="link link-hover"
                                   href="{{ $rowUrl }}">
                                    {{ e(stripslashes($row->title ?? '')) }}
                                </a>
                            </td>
                            <td>
                                {!! $creatorLinked !!}
                            </td>
                            <td>
                                <span>{{ e(stripslashes($row->version->get('summary', '') ?? '')) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                {{ Lang::txt('COM_WIKI_NONE') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @php
            $pageNav = $rows->pagination;
            $pageNav->setAdditionalUrlParam('scope', $page->get('scope'));
            $pageNav->setAdditionalUrlParam('pagename', $page->get('pagename'));
        @endphp
        {!! $pageNav !!}
    </form>
</x-page-container>
