{{--
 * Wiki special page — all pages listing with alphabetical index
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

    Pathway::append(
        Lang::txt('COM_WIKI_SPECIAL_ALL_PAGES'),
        $page->link()
    );

    $dir = strtoupper(Request::getString('dir', 'ASC'));
    if (!in_array($dir, ['ASC', 'DESC'])) {
        $dir = 'ASC';
    }

    $filters = ['state' => \Components\Wiki\Models\Page::STATE_PUBLISHED];

    $namespace = urldecode(Request::getString('namespace', ''));
    if ($namespace) {
        $filters['namespace'] = $namespace;
    }

    $rows = $book->pages($filters)
        ->order('title', $dir)
        ->ordered()
        ->rows();

    $namespaces = \Components\Wiki\Models\Page::all()
        ->select('namespace')
        ->whereEquals('state', \Components\Wiki\Models\Page::STATE_PUBLISHED)
        ->whereEquals('scope', $book->get('scope'))
        ->whereEquals('scope_id', $book->get('scope_id'))
        ->group('namespace')
        ->order('namespace', 'asc')
        ->rows();
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

    <form method="get" action="{{ Route::url($page->link(), false) }}">
        <fieldset class="flex flex-wrap items-end gap-3 mb-6">
            <legend class="sr-only">{{ Lang::txt('COM_WIKI_FILTER_LIST') }}</legend>

            <label class="form-control" for="field-namespace">
                <div class="label py-0">
                    <span class="label-text text-xs">
                        {{ Lang::txt('COM_WIKI_FIELD_NAMESPACE') }}
                    </span>
                </div>
                <select name="namespace" id="field-namespace"
                        class="select select-bordered select-sm w-48">
                    <option value=""
                        {{ $namespace == '' ? 'selected' : '' }}>
                        {{ Lang::txt('COM_WIKI_ALL') }}
                    </option>
                    @foreach($namespaces as $space)
                        @if(trim($space->get('namespace')))
                            <option value="{{ $space->get('namespace') }}"
                                {{ $namespace == $space->get('namespace') ? 'selected' : '' }}>
                                {{ e($space->get('namespace')) }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </label>

            <button type="submit" class="btn btn-sm btn-primary mt-auto">
                {{ Lang::txt('COM_WIKI_GO') }}
            </button>
        </fieldset>

        @if($rows->count())
            @php
                $data = [];
                foreach ($rows as $row) {
                    $data[] = $row;
                }
                $colCount = min(3, count($data));
                $columns = $colCount > 0
                    ? array_chunk($data, ceil(count($data) / $colCount), true)
                    : [];
                $index = '';
            @endphp

            <div class="card bg-base-100 border border-base-300 mb-6">
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-2">
                        @foreach($columns as $colIdx => $column)
                            <div>
                                @php $k = 0; @endphp
                                @foreach($column as $row)
                                    @php
                                        $firstLetter = strtoupper(
                                            substr($row->title ?? '', 0, 1)
                                        );
                                    @endphp
                                    @if($firstLetter != $index)
                                        @php $index = $firstLetter; @endphp
                                        @if($k != 0)
                                            </ul>
                                        @endif
                                        <h3 class="text-base font-bold mt-3 mb-1 border-b border-base-300 pb-1">
                                            {{ $index }}
                                        </h3>
                                        <ul class="space-y-0.5 text-sm">
                                    @elseif($k == 0)
                                        <h3 class="text-base font-bold mt-3 mb-1 border-b border-base-300 pb-1">
                                            {{ Lang::txt('COM_WIKI_INDEX_CONTINUED', $index) }}
                                        </h3>
                                        <ul class="space-y-0.5 text-sm">
                                    @endif
                                    <li>
                                        <a class="link link-hover"
                                           href="{{ Route::url($row->link(), false) }}">
                                            {{ e(stripslashes($row->title ?? '')) }}
                                        </a>
                                    </li>
                                    @php $k++; @endphp
                                @endforeach
                                @if($k > 0)
                                    </ul>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        @php
            $specials = array_filter($book->special(), function($sp) use ($page) {
                return $sp != strtolower($page->stripNamespace());
            });
        @endphp
        @if(count($specials))
            <h3 class="text-base font-semibold mb-3">
                {{ Lang::txt('COM_WIKI_SPECIAL_PAGES') }}
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                @foreach($specials as $sp)
                    @php
                        $spUrl = Route::url(
                            $page->link('base') . '&pagename=Special:' . ucfirst($sp),
                            false
                        );
                    @endphp
                    <a class="link link-hover text-sm"
                       href="{{ $spUrl }}">
                        {{ ucfirst(e(stripslashes($sp ?? ''))) }}
                    </a>
                @endforeach
            </div>
        @endif
    </form>
</x-page-container>
