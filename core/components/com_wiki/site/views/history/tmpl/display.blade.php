{{--
 * Wiki history — revision history table with comparison
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Date;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;

    $revisions = $page->versions()
        ->where('approved', '!=', \Components\Wiki\Models\Version::STATE_DELETED)
        ->order('id', 'desc')
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

    @if(count($parents))
        <p class="text-sm breadcrumbs mb-2">
            @foreach($parents as $parent)
                <a class="link link-hover"
                   href="{{ Route::url($parent->link(), false) }}">{{ $parent->title }}</a>
                <span class="mx-1">/</span>
            @endforeach
        </p>
    @endif

    @if(!$page->isStatic())
        {!! $__view->view('_authors', 'pages')
            ->set('page', $page)
            ->loadTemplate() !!}
    @endif

    @if($__view->getError())
        <div role="alert" class="alert alert-error mb-4">
            <span>{{ $__view->getError() }}</span>
        </div>
    @endif

    {!! $__view->view('_submenu', 'pages')
        ->set('option', $option)
        ->set('controller', $controller)
        ->set('page', $page)
        ->set('task', $task)
        ->set('sub', $sub)
        ->loadTemplate() !!}

    @php
        $histHelpUrl = Route::url(
            $page->link('base') . '&pagename=Help:PageHistory',
            false
        );
    @endphp
    <p class="text-sm text-base-content/60 mb-4">
        {!! Lang::txt('COM_WIKI_HISTORY_EXPLANATION', $histHelpUrl) !!}
    </p>

    <form action="{{ Route::url($page->link('compare'), false) }}" method="post">
        @php
            $createdTime = '<time datetime="' . $page->get('created') . '">'
                . Date::of($page->get('created'))->toSql(true) . '</time>';
            $modifiedTime = '<time datetime="' . $page->get('modified') . '">'
                . Date::of($page->get('modified'))->toSql(true) . '</time>';
        @endphp
        <div role="alert" class="alert alert-info mb-4">
            <span>{!! Lang::txt(
                'COM_WIKI_HISTORY_SUMMARY',
                count($revisions),
                $createdTime,
                $modifiedTime
            ) !!}</span>
        </div>

        <div class="mb-4">
            <button type="submit" class="btn btn-sm btn-primary">
                {{ Lang::txt('COM_WIKI_HISTORY_COMPARE') }}
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-sm">
                <caption class="sr-only">
                    {{ Lang::txt('COM_WIKI_HISTORY_TBL_SUMMARY') }}
                </caption>
                <thead>
                    <tr>
                        <th colspan="2">{{ Lang::txt('COM_WIKI_HISTORY_COL_COMPARE') }}</th>
                        <th>{{ Lang::txt('COM_WIKI_HISTORY_COL_WHEN') }}</th>
                        <th>{{ Lang::txt('COM_WIKI_HISTORY_COL_MADE_BY') }}</th>
                        <th>{{ Lang::txt('COM_WIKI_HISTORY_COL_LENGTH') }}</th>
                        <th>{{ Lang::txt('COM_WIKI_HISTORY_COL_STATUS') }}</th>
                        <th></th>
                        @php
                            $canManage = ($page->isLocked() && $page->access('manage'))
                                || (!$page->isLocked() && $page->access('delete'));
                        @endphp
                        @if($canManage)
                            <th></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 0;
                        $comparefirst = true;
                        $total = $revisions->count();
                        $lengths = [];
                        foreach ($revisions as $revision) {
                            $lengths[] = $revision->get('length');
                        }
                    @endphp
                    @foreach($revisions as $revision)
                        @php
                            $i++;
                            $xname = $revision->creator->get('name',
                                Lang::txt('COM_WIKI_AUTHOR_UNKNOWN'));
                            $summary = ($revision->get('summary') && trim($revision->get('summary')))
                                ? $revision->get('summary')
                                : Lang::txt('COM_WIKI_REVISION_NO_SUMMARY');
                            $isApproved = $revision->get('approved') == 1;
                            $prvLength = $lengths[$i] ?? 0;
                            $diff = $revision->get('length') - $prvLength;
                            $revUrl = Route::url(
                                $page->link('', 'version=' . $revision->get('version')),
                                false
                            );
                            $revRawUrl = Route::url(
                                $page->link('', 'version=' . $revision->get('version') . '&format=raw'),
                                false
                            );
                            $approveUrl = Route::url(
                                $page->link('approve', 'oldid=' . $revision->get('id')),
                                false
                            );
                            $setCurrentUrl = Route::url(
                                $page->link('setcurrentrevision', 'version_id=' . $revision->get('id')),
                                false
                            );
                            $deleteRevUrl = Route::url(
                                $page->link('deleterevision', 'oldid=' . $revision->get('id')),
                                false
                            );
                        @endphp
                        <tr>
                            @if($page->get('version_id') == $revision->get('id'))
                                <td></td>
                                <td>
                                    <input type="radio" name="diff"
                                           class="radio radio-sm"
                                           value="{{ $revision->get('version') }}"
                                           checked />
                                </td>
                            @else
                                <td>
                                    <input type="radio" name="oldid"
                                           class="radio radio-sm"
                                           value="{{ $revision->get('version') }}"
                                           {{ $comparefirst ? 'checked' : '' }} />
                                    @php if ($comparefirst) $comparefirst = false; @endphp
                                </td>
                                <td></td>
                            @endif
                            <td>
                                <a href="{{ $revUrl }}"
                                   title="{{ e(Lang::txt('COM_WIKI_REVISION_SUMMARY') . ' :: ' . $summary) }}">
                                    <time datetime="{{ $revision->get('created') }}">
                                        {{ e(Date::of($revision->get('created'))->toLocal('Y-m-d h:i:s')) }}
                                    </time>
                                </a>
                                <a class="link link-hover text-xs ml-1"
                                   href="{{ $revRawUrl }}"
                                   title="{{ Lang::txt('COM_WIKI_HISTORY_MARKUP_TITLE') }}">
                                    {{ Lang::txt('COM_WIKI_HISTORY_MARKUP') }}
                                </a>
                            </td>
                            <td>{{ e($xname) }}</td>
                            <td>
                                {{ Lang::txt('COM_WIKI_HISTORY_BYTES', number_format($revision->get('length'))) }}
                                @php
                                    $diffCls = $diff > 0 ? 'text-success' : ($diff == 0 ? '' : 'text-error');
                                    $diffVal = $diff > 0 ? '+' . number_format($diff) : number_format($diff);
                                @endphp
                                (<span class="{{ $diffCls }}">{{ $diffVal }}</span>)
                            </td>
                            <td>
                                @if($isApproved)
                                    <span class="badge badge-success badge-sm">approved</span>
                                @else
                                    <span class="badge badge-warning badge-sm">suggested</span>
                                @endif
                                @if(!$isApproved && $page->access('manage'))
                                    <br />
                                    <a class="link text-xs"
                                       href="{{ $approveUrl }}">
                                        {{ Lang::txt('COM_WIKI_ACTION_APPROVED') }}
                                    </a>
                                @endif
                            </td>
                            @if($canManage)
                                <td>
                                    @if($page->get('version_id') == $revision->get('id'))
                                        <span class="text-xs text-base-content/50">
                                            (Current Version)
                                        </span>
                                    @else
                                        <a class="btn btn-xs btn-ghost"
                                           href="{{ $setCurrentUrl }}">
                                            {{ Lang::txt('COM_WIKI_HISTORY_SET_CURRENT') }}
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-xs btn-ghost text-error"
                                       href="{{ $deleteRevUrl }}"
                                       title="{{ Lang::txt('COM_WIKI_REVISION_DELETE') }}">
                                        {{ Lang::txt('JACTION_DELETE') }}
                                    </a>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-sm btn-primary">
                {{ Lang::txt('COM_WIKI_HISTORY_COMPARE') }}
            </button>
        </div>

        <input type="hidden" name="pagename"
               value="{{ e($page->pagename) }}" />
        <input type="hidden" name="pageid"
               value="{{ e($page->get('id')) }}" />

        @foreach($page->adapter()->routing('compare') as $name => $val)
            <input type="hidden" name="{{ e($name) }}" value="{{ e($val) }}" />
        @endforeach
    </form>
</x-page-container>
