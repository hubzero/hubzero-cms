{{--
 * Wiki special — file list with sortable columns and previews
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\App;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Pathway;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;

    Pathway::append(
        Lang::txt('COM_WIKI_SPECIAL_FILE_LIST'),
        $page->link()
    );

    $database = App::get('db');

    $sort = strtolower(Request::getString('sort', 'created'));
    if (!in_array($sort, array('created', 'filename', 'description', 'created_by'))) {
        $sort = 'created';
    }
    $dir = strtoupper(Request::getString('dir', 'DESC'));
    if (!in_array($dir, array('ASC', 'DESC'))) {
        $dir = 'DESC';
    }

    $pages  = \Components\Wiki\Models\Page::blank()->getTableName();
    $attach = \Components\Wiki\Models\Attachment::blank()->getTableName();

    $rows = \Components\Wiki\Models\Attachment::all()
        ->select($attach . '.*')
        ->select($pages . '.pagename')
        ->select($pages . '.path')
        ->select($pages . '.scope')
        ->select($pages . '.scope_id')
        ->join($pages, $pages . '.id', $attach . '.page_id')
        ->whereEquals($pages . '.scope', $book->get('scope'))
        ->whereEquals($pages . '.scope_id', $book->get('scope_id'))
        ->whereEquals($pages . '.state', '1')
        ->paginated()
        ->rows();

    $altdir = ($dir == 'ASC') ? 'DESC' : 'ASC';
@endphp

<x-page-container :title="Lang::txt('COM_WIKI_SPECIAL_FILE_LIST')">
    <form method="get" action="{{ Route::url($page->link(), false) }}">
        <p class="mb-4">
            {{ Lang::txt('COM_WIKI_SPECIAL_FILE_LIST_ABOUT') }}
        </p>

        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th scope="col">
                            <a class="{{ $sort == 'created' ? 'font-bold' : '' }}"
                               href="{{ Route::url($page->link() . '&sort=created&dir=' . $altdir, false) }}">
                                @if($sort == 'created')
                                    {!! $dir == 'ASC' ? '&uarr;' : '&darr;' !!}
                                @endif
                                {{ Lang::txt('COM_WIKI_COL_DATE') }}
                            </a>
                        </th>
                        <th scope="col">
                            <a class="{{ $sort == 'filename' ? 'font-bold' : '' }}"
                               href="{{ Route::url($page->link() . '&sort=filename&dir=' . $altdir, false) }}">
                                @if($sort == 'filename')
                                    {!! $dir == 'ASC' ? '&uarr;' : '&darr;' !!}
                                @endif
                                {{ Lang::txt('COM_WIKI_COL_NAME') }}
                            </a>
                        </th>
                        <th scope="col">
                            {{ Lang::txt('COM_WIKI_COL_PREVIEW') }}
                        </th>
                        <th scope="col">
                            {{ Lang::txt('COM_WIKI_COL_SIZE') }}
                        </th>
                        <th scope="col">
                            <a class="{{ $sort == 'created_by' ? 'font-bold' : '' }}"
                               href="{{ Route::url($page->link() . '&sort=created_by&dir=' . $altdir, false) }}">
                                @if($sort == 'created_by')
                                    {!! $dir == 'ASC' ? '&uarr;' : '&darr;' !!}
                                @endif
                                {{ Lang::txt('COM_WIKI_COL_UPLOADER') }}
                            </a>
                        </th>
                        <th scope="col">
                            <a class="{{ $sort == 'description' ? 'font-bold' : '' }}"
                               href="{{ Route::url($page->link() . '&sort=description&dir=' . $altdir, false) }}">
                                @if($sort == 'description')
                                    {!! $dir == 'ASC' ? '&uarr;' : '&darr;' !!}
                                @endif
                                {{ Lang::txt('COM_WIKI_COL_DESCRIPTION') }}
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @if($rows->count())
                        @foreach($rows as $row)
                            @php
                                $fsize = Lang::txt('COM_WIKI_UNKNOWN');
                                $filePath = $row->filespace() . DS . $row->get('page_id') . DS . $row->get('filename');
                                if (is_file($filePath)) {
                                    $fsize = \Hubzero\Utility\Number::formatBytes(filesize($filePath));
                                }

                                $creatorName = $row->creator->get('name', Lang::txt('COM_WIKI_UNKNOWN')) ?? '';
                                $escapedName = e(stripslashes($creatorName));
                                $nameHtml = $escapedName;
                                if (in_array($row->creator->get('access'), User::getAuthorisedViewLevels())) {
                                    $creatorUrl = Route::url($row->creator->link(), false);
                                    $nameHtml = '<a href="' . $creatorUrl . '">' . $escapedName . '</a>';
                                }
                                $pathPrefix = $row->get('path') ? $row->get('path') . '/' : '';
                                $filePagename = $pathPrefix . $row->get('pagename') . '/File:' . $row->get('filename');
                                $fileUrl = Route::url($page->link('base') . '&pagename=' . $filePagename, false);
                                $fileAlt = e(stripslashes($row->get('filename') ?? ''));
                            @endphp
                            <tr>
                                <td>
                                    <time datetime="{{ $row->get('created') }}">
                                        {{ $row->get('created') }}
                                    </time>
                                </td>
                                <td>
                                    <a href="{{ $fileUrl }}">
                                        {{ $fileAlt }}
                                    </a>
                                </td>
                                <td>
                                    @if($row->isImage())
                                        <a rel="lightbox" href="{{ $fileUrl }}">
                                            <img src="{{ $fileUrl }}" width="50"
                                                 alt="{{ $fileAlt }}" />
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <span>{{ $fsize }}</span>
                                </td>
                                <td>
                                    {!! $nameHtml !!}
                                </td>
                                <td>
                                    <span>{{ e(stripslashes($row->get('description', '') ?? '')) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6">
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
            $pageNav->setAdditionalUrlParam('sort', $sort);
            $pageNav->setAdditionalUrlParam('dir', $dir);
        @endphp
        {!! $pageNav !!}
    </form>
</x-page-container>
