{{--
 * Wiki special — longest pages sorted by byte count
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Config;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Pathway;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;

    Pathway::append(
        Lang::txt('COM_WIKI_SPECIAL_LONG_PAGES'),
        $page->link('base') . '&pagename=Special:LongPages'
    );

    $limit = Request::getInt('limit', Config::get('list_limit'));
    $start = Request::getInt('limitstart', 0);

    $filters = array('state' => array(0, 1));

    $pagesTable    = \Components\Wiki\Models\Page::blank()->getTableName();
    $versionsTable = \Components\Wiki\Models\Version::blank()->getTableName();

    $rows = $book->pages($filters)
        ->select($pagesTable . '.*')
        ->select($versionsTable . '.created_by')
        ->select($versionsTable . '.length')
        ->join($versionsTable, $versionsTable . '.id', $pagesTable . '.version_id')
        ->order('length', 'desc')
        ->ordered()
        ->paginated()
        ->rows();

    $shortPagesUrl = Route::url(
        $page->link('base') . '&pagename=Special:ShortPages',
        false
    );
@endphp

<x-page-container :title="Lang::txt('COM_WIKI_SPECIAL_LONG_PAGES')">
    <form method="get" action="{{ Route::url($page->link('base') . '&pagename=Special:LongPages', false) }}">
        <p class="mb-4">
            {!! Lang::txt('COM_WIKI_SPECIAL_LONG_PAGES_ABOUT', $shortPagesUrl) !!}
        </p>

        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th scope="col">
                            {{ Lang::txt('COM_WIKI_COL_DATE') }}
                        </th>
                        <th scope="col">
                            {{ Lang::txt('COM_WIKI_COL_TITLE') }}
                        </th>
                        <th scope="col">
                            {{ Lang::txt('COM_WIKI_COL_CREATOR') }}
                        </th>
                        <th scope="col">
                            {{ Lang::txt('COM_WIKI_COL_LENGTH') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @if($rows->count())
                        @foreach($rows as $row)
                            @php
                                $creatorName = $row->creator->get('name', Lang::txt('COM_WIKI_UNKNOWN')) ?? '';
                                $escapedName = e(stripslashes($creatorName));
                                $nameHtml = $escapedName;
                                if (in_array($row->creator->get('access'), User::getAuthorisedViewLevels())) {
                                    $creatorUrl = Route::url($row->creator->link(), false);
                                    $nameHtml = '<a href="' . $creatorUrl . '">' . $escapedName . '</a>';
                                }
                            @endphp
                            <tr>
                                <td>
                                    <time datetime="{{ $row->get('created') }}">
                                        {{ $row->get('created') }}
                                    </time>
                                </td>
                                <td>
                                    <a href="{{ Route::url($row->link(), false) }}">
                                        {{ e(stripslashes($row->title ?? '')) }}
                                    </a>
                                </td>
                                <td>
                                    {!! $nameHtml !!}
                                </td>
                                <td>
                                    {{ Lang::txt('COM_WIKI_HISTORY_BYTES', number_format($row->get('length'))) }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4">
                                {{ Lang::txt('COM_WIKI_NONE') }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @php
            $pageNav = $rows->pagination;
            $pageNav->setAdditionalUrlParam('scope', $page->get('path'));
            $pageNav->setAdditionalUrlParam('pagename', $page->get('pagename'));
        @endphp
        {!! $pageNav !!}
    </form>
</x-page-container>
