{{--
 * Wiki special — pages linking to a given page
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Config;
    use Hubzero\Facades\Date;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Pathway;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;

    Pathway::append(
        Lang::txt('COM_WIKI_SPECIAL_LINKS'),
        $page->link()
    );

    $sort = strtolower(Request::getString('sort', 'title'));
    if (!in_array($sort, array('timestamp', 'title'))) {
        $sort = 'timestamp';
    }

    $dir = strtoupper(Request::getString('dir', 'DESC'));
    if (!in_array($dir, array('ASC', 'DESC'))) {
        $dir = 'DESC';
    }

    $altdir = ($dir == 'ASC') ? 'DESC' : 'ASC';

    $limit = Request::getInt('limit', Config::get('list_limit'));
    $start = Request::getInt('limitstart', 0);

    $linkedPage = $book->pages()
        ->whereEquals('pagename', Request::getString('page', ''))
        ->whereEquals('path', Request::getString('scope', ''))
        ->row();

    if ($v = Request::getInt('version', 0)) {
        $revision = $linkedPage->versions()
            ->whereEquals('id', $v)
            ->row();
    } else {
        $revision = $linkedPage->version();
    }

    $permalink = rtrim(Request::base(), '/') . '/'
        . ltrim(Route::url($linkedPage->link() . '&version=' . $revision->get('version'), false), '/');

    $l = \Components\Wiki\Models\Link::blank()->getTableName();
    $p = \Components\Wiki\Models\Page::blank()->getTableName();

    $rows = \Components\Wiki\Models\Page::all()
        ->select($p . '.*')
        ->select($l . '.timestamp')
        ->join($l, $l . '.scope_id', $p . '.id', 'inner')
        ->whereEquals($p . '.scope', $linkedPage->get('scope'))
        ->whereEquals($p . '.scope_id', $linkedPage->get('scope_id'))
        ->whereEquals($p . '.state', \Components\Wiki\Models\Page::STATE_PUBLISHED)
        ->whereEquals($l . '.scope', 'internal')
        ->whereEquals($l . '.scope_id', $linkedPage->get('id'))
        ->order($sort, $dir)
        ->paginated()
        ->rows();

    $pageLink = '<a href="' . Route::url($linkedPage->link(), false) . '">'
        . e($linkedPage->get('title')) . '</a>';
@endphp

<x-page-container :title="Lang::txt('COM_WIKI_SPECIAL_LINKS')">
    <form method="get" action="{{ Route::url($page->link(), false) }}">
        <p class="mb-4">
            {!! Lang::txt('The following pages link to %s', $pageLink) !!}
        </p>

        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th scope="col">
                            <a class="{{ $sort == 'timestamp' ? 'font-bold' : '' }}"
                               href="{{ Route::url($page->link() . '&sort=timestamp&dir=' . $altdir, false) }}">
                                @if($sort == 'timestamp')
                                    {!! $dir == 'ASC' ? '&uarr;' : '&darr;' !!}
                                @endif
                                {{ Lang::txt('COM_WIKI_COL_DATE') }}
                            </a>
                        </th>
                        <th scope="col">
                            <a class="{{ $sort == 'title' ? 'font-bold' : '' }}"
                               href="{{ Route::url($page->link() . '&sort=title&dir=' . $altdir, false) }}">
                                @if($sort == 'title')
                                    {!! $dir == 'ASC' ? '&uarr;' : '&darr;' !!}
                                @endif
                                {{ Lang::txt('COM_WIKI_COL_TITLE') }}
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @if($rows->count())
                        @foreach($rows as $row)
                            <tr>
                                <td>
                                    <time datetime="{{ e($row->get('timestamp')) }}">
                                        {{ e(Date::of($row->get('timestamp'))->toLocal()) }}
                                    </time>
                                </td>
                                <td>
                                    <a href="{{ Route::url($row->link(), false) }}">
                                        {{ e(stripslashes($row->get('title', '') ?? '')) }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="2">
                                {{ Lang::txt('COM_WIKI_NONE') }}
                            </td>
                        </tr>
                    @endif
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
