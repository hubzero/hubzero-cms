{{--
 * Wiki page — edit/create form
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Html;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;
    use Hubzero\Facades\Event;

    $tags = $page->tags('string');

    if ($page->exists()) {
        $lid = $page->get('id');
    } else {
        $lid = Request::getInt('lid', (time() . rand(0, 10000)), 'post');
        $lid = '-' . substr($lid, -8);
    }

    $macros = \Components\Wiki\Models\Page::oneByPath('Help:WikiMacros', 'site', 0);
    $macros->set('scope', $book->get('scope'))
        ->set('scope_id', $book->get('scope_id'));

    $formatting = \Components\Wiki\Models\Page::oneByPath('Help:WikiFormatting', 'site', 0);
    $formatting->set('scope', $book->get('scope'))
        ->set('scope_id', $book->get('scope_id'));

    $authors = [];
    foreach ($page->authors()->rows() as $auth) {
        $authors[] = $auth->user->get('username');
    }
    $authors = implode(', ', $authors);
@endphp

<x-page-container :title="e($page->title)" bodyClass="edit-form">
    @if(!$sub)
        @slot('sidebar')
            {!! $__view->view('_wikimenu')
                ->set('option', $option)
                ->set('controller', $controller)
                ->set('page', $page)
                ->set('task', $task)
                ->set('sub', $sub)
                ->loadTemplate() !!}
        @endslot
    @endif

    {!! $__view->view('_authors')
        ->set('page', $page)
        ->loadTemplate() !!}

    {!! $__view->view('_submenu')
        ->set('option', $option)
        ->set('controller', $controller)
        ->set('page', $page)
        ->set('task', $task)
        ->set('sub', $sub)
        ->loadTemplate() !!}

    {{-- Access warnings --}}
    @if($page->exists() && !$page->access('modify'))
        @if($page->param('allow_changes') == 1)
            <div role="alert" class="alert alert-warning mb-4">
                <span>{{ Lang::txt('COM_WIKI_WARNING_NOT_AUTH_EDITOR_SUGGESTED') }}</span>
            </div>
        @else
            <div role="alert" class="alert alert-warning mb-4">
                <span>{{ Lang::txt('COM_WIKI_WARNING_NOT_AUTH_EDITOR') }}</span>
            </div>
        @endif
    @endif

    @if($page->isLocked() && !$page->access('manage'))
        <div role="alert" class="alert alert-warning mb-4">
            <span>{{ Lang::txt('COM_WIKI_WARNING_NOT_AUTH_EDITOR') }}</span>
        </div>
    @endif

    @if($__view->getError())
        <div role="alert" class="alert alert-error mb-4">
            <span>{{ $__view->getError() }}</span>
        </div>
    @endif

    {{-- Preview --}}
    @if($preview)
        <div class="mb-6">
            <div role="alert" class="alert alert-warning mb-4">
                <span>{{ Lang::txt('COM_WIKI_WARNING_PREVIEW_ONLY') }}</span>
            </div>
            <div class="prose max-w-none p-4 border border-base-300 rounded-lg bg-base-100">
                {!! $revision->get('pagehtml') !!}
            </div>
        </div>
    @endif

    <form action="{{ Route::url($page->link(), false) }}"
          method="post" id="hubForm" class="space-y-6">

        {{-- Page section --}}
        <x-form-section :heading="Lang::txt('COM_WIKI_FIELDSET_PAGE')">
            @if($page->exists() && $page->access('edit'))
                @php $renameUrl = Route::url($page->link('rename'), false); @endphp
                <p class="text-sm text-base-content/60 mb-3">
                    {!! Lang::txt('COM_WIKI_WARNING_TO_CHANGE_PAGENAME', $renameUrl) !!}
                </p>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-field name="page[parent]" inputId="parent"
                              :label="Lang::txt('COM_WIKI_FIELD_PARENT')">
                    <select name="page[parent]" id="parent"
                            class="select select-bordered w-full">
                        <option value="0">{{ Lang::txt('COM_WIKI_NONE') }}</option>
                        @if($tree)
                            @foreach($tree as $item)
                                @if($page->get('id') == $item->get('id'))
                                    @continue
                                @endif
                                <option value="{{ $item->get('id') }}"
                                    {{ $page->get('parent') == $item->get('id') ? 'selected' : '' }}>
                                    {{ e(stripslashes($item->get('pagename'))) }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </x-form-field>

                @php
                    $hi = [];
                    $tplate = strtolower(Request::getString('tplate', ''));
                @endphp
                <x-form-field name="tplate" inputId="templates"
                              :label="Lang::txt('COM_WIKI_FIELD_TEMPLATE')">
                    <select name="tplate" id="templates"
                            class="select select-bordered w-full">
                        <option value="tc">
                            {{ Lang::txt('COM_WIKI_FIELD_TEMPLATE_SELECT') }}
                        </option>
                        @foreach($book->templates()->rows() as $template)
                            @php
                                $tmpltags = $template->tags('string');
                                if ($tplate == strtolower($template->get('pagename'))) {
                                    $tags = $tmpltags;
                                }
                                $isSelected = ($tplate == strtolower($template->get('pagename'))
                                    || $tplate == 't' . $template->get('id'));
                                if ($isSelected && !$page->exists()) {
                                    $revision->set('pagetext',
                                        stripslashes($template->version->get('pagetext'))
                                    );
                                }
                                $tid = $template->get('id');
                                $tptxt = e(stripslashes($template->version->get('pagetext')));
                                $hi[] = '<input type="hidden" name="t' . $tid
                                    . '" id="t' . $tid . '" value="' . $tptxt . '" />'
                                    . "\n" . '<input type="hidden" name="t' . $tid
                                    . '_tags" id="t' . $tid . '_tags" value="'
                                    . e(stripslashes($tmpltags)) . '" />';
                            @endphp
                            <option value="t{{ $tid }}"
                                {{ $isSelected ? 'selected' : '' }}>
                                {{ e(stripslashes($template->get('title'))) }}
                            </option>
                        @endforeach
                    </select>
                    {!! implode("\n", $hi) !!}
                </x-form-field>
            </div>

            <x-form-field name="page[title]" inputId="title"
                          :label="Lang::txt('COM_WIKI_FIELD_TITLE')" required>
                <input type="text" name="page[title]" id="title"
                       class="input input-bordered w-full"
                       value="{{ e($page->get('title')) }}" />
            </x-form-field>

            <x-form-field name="revision[pagetext]" inputId="pagetext"
                          :label="Lang::txt('COM_WIKI_FIELD_PAGETEXT')" required>
                {!! \Components\Wiki\Helpers\Editor::getInstance()->display(
                    'revision[pagetext]',
                    'pagetext',
                    $revision->get('pagetext'),
                    'textarea textarea-bordered w-full',
                    '35',
                    '40'
                ) !!}
            </x-form-field>

            {{-- File manager --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    @php
                        $baseUrl = rtrim(Request::base(true), '/');
                        $uploadAction = $baseUrl
                            . '/index.php?option=com_wiki&no_html=1'
                            . '&controller=media&task=upload&listdir=' . $lid;
                        $listAction = $baseUrl
                            . '/index.php?option=com_wiki&no_html=1'
                            . '&controller=media&task=list&listdir=' . $lid;
                        $filerSrc = $baseUrl
                            . '/index.php?option=com_wiki&tmpl=component'
                            . '&controller=media&scope=' . $page->get('scope')
                            . '&pagename=' . $page->get('pagename')
                            . '&listdir=' . $lid;
                    @endphp
                    <div id="file-manager"
                         data-instructions="{{ Lang::txt('COM_WIKI_CLICK_OR_DROP_FILE') }}"
                         data-action="{{ $uploadAction }}"
                         data-list="{{ $listAction }}">
                        <iframe name="filer" id="filer"
                                src="{{ $filerSrc }}"
                                class="w-full h-48 border border-base-300 rounded"></iframe>
                    </div>
                    <div id="file-uploader-list"></div>
                </div>
                <div>
                    @if($macros)
                        @php
                            $imgMacroUrl = Route::url($macros->link() . '#image', false);
                            $fileMacroUrl = Route::url($macros->link() . '#file', false);
                        @endphp
                        <p class="text-sm text-base-content/60">
                            {!! Lang::txt('COM_WIKI_IMAGE_MACRO_HINT', $imgMacroUrl) !!}
                        </p>
                        <p class="text-sm text-base-content/60 mt-2">
                            {!! Lang::txt('COM_WIKI_FILE_MACRO_HINT', $fileMacroUrl) !!}
                        </p>
                    @endif
                </div>
            </div>
        </x-form-section>

        {{-- Access section --}}
        @php
            $canEdit = !$page->exists()
                || $page->get('created_by') == User::get('id')
                || $page->access('manage');
        @endphp

        @if($canEdit)
            <x-form-section :heading="Lang::txt('COM_WIKI_FIELDSET_ACCESS')">
                @if($page->access('edit'))
                    @php
                        $mode = $page->param('mode', 'wiki');
                        $cls = 'params-knol';
                        if ($mode && $mode != 'knol') {
                            $cls .= ' hidden';
                        }
                    @endphp

                    @if($canEdit)
                        <x-form-field name="params[mode]" inputId="params_mode"
                                      :label="Lang::txt('COM_WIKI_FIELD_MODE')" required>
                            <select name="params[mode]" id="params_mode"
                                    class="select select-bordered w-full">
                                <option value="knol"
                                    {{ $mode == 'knol' ? 'selected' : '' }}>
                                    {{ Lang::txt('COM_WIKI_FIELD_MODE_KNOL') }}
                                </option>
                                <option value="wiki"
                                    {{ $mode == 'wiki' ? 'selected' : '' }}>
                                    {{ Lang::txt('COM_WIKI_FIELD_MODE_WIKI') }}
                                </option>
                                @if($page->access('admin'))
                                    <option value="static"
                                        {{ $mode == 'static' ? 'selected' : '' }}>
                                        {{ Lang::txt('COM_WIKI_FIELD_MODE_STATIC') }}
                                    </option>
                                @endif
                            </select>
                        </x-form-field>
                    @else
                        <input type="hidden" name="params[mode]" id="params_mode"
                               value="{{ $mode }}" />
                    @endif

                    <x-form-field name="authors" inputId="params_authors"
                                  :label="Lang::txt('COM_WIKI_FIELD_AUTHORS')">
                        @php
                            $mc = Event::trigger(
                                'hubzero.onGetMultiEntry',
                                [['members', 'authors', 'params_authors', '', $authors]]
                            );
                        @endphp
                        @if(count($mc) > 0)
                            {!! $mc[0] !!}
                        @else
                            <input type="text" name="authors" id="params_authors"
                                   class="input input-bordered w-full"
                                   value="{{ e($authors) }}" />
                        @endif
                    </x-form-field>

                    <x-form-field name="params[hide_authors]"
                                  inputId="params_hide_authors"
                                  :label="Lang::txt('COM_WIKI_FIELD_HIDE_AUTHORS')"
                                  type="checkbox">
                        <input type="checkbox" name="params[hide_authors]"
                               id="params_hide_authors" class="checkbox"
                               value="1"
                               {{ $page->param('hide_authors') == 1 ? 'checked' : '' }} />
                    </x-form-field>

                    <x-form-field name="params[allow_changes]"
                                  inputId="params_allow_changes"
                                  :label="Lang::txt('COM_WIKI_FIELD_ALLOW_CHANGES')"
                                  type="checkbox">
                        <input type="checkbox" name="params[allow_changes]"
                               id="params_allow_changes" class="checkbox"
                               value="1"
                               {{ $page->param('allow_changes') == 1 ? 'checked' : '' }} />
                    </x-form-field>

                    <x-form-field name="params[allow_comments]"
                                  inputId="params_allow_comments"
                                  :label="Lang::txt('COM_WIKI_FIELD_ALLOW_COMMENTS')"
                                  type="checkbox">
                        <input type="checkbox" name="params[allow_comments]"
                               id="params_allow_comments" class="checkbox"
                               value="1"
                               {{ $page->param('allow_comments') == 1 ? 'checked' : '' }} />
                    </x-form-field>
                @else
                    <input type="hidden" name="params[mode]"
                           value="{{ e($page->param('mode', 'wiki')) }}" />
                    <input type="hidden" name="params[hide_authors]"
                           value="{{ e($page->param('hide_authors', 1)) }}" />
                    <input type="hidden" name="params[allow_changes]"
                           value="{{ e($page->param('allow_changes', 1)) }}" />
                    <input type="hidden" name="params[allow_comments]"
                           value="{{ e($page->param('allow_comments', 1)) }}" />
                    <input type="hidden" name="authors" id="params_authors"
                           value="{{ e($authors) }}" />
                @endif

                @if($page->access('manage'))
                    <x-form-field name="page[protected]" inputId="protected"
                                  :label="Lang::txt('COM_WIKI_FIELD_STATE')"
                                  type="checkbox">
                        <input type="checkbox" name="page[protected]"
                               id="protected" class="checkbox"
                               value="1"
                               {{ $page->isLocked() ? 'checked' : '' }} />
                    </x-form-field>
                @endif
            </x-form-section>
        @else
            <input type="hidden" name="page[protected]"
                   value="{{ e($page->get('protected', 0)) }}" />
            <input type="hidden" name="params[mode]"
                   value="{{ e($page->param('mode', 'wiki')) }}" />
            <input type="hidden" name="params[hide_authors]"
                   value="{{ e($page->param('hide_authors', 1)) }}" />
            <input type="hidden" name="params[allow_changes]"
                   value="{{ e($page->param('allow_changes', 1)) }}" />
            <input type="hidden" name="params[allow_comments]"
                   value="{{ e($page->param('allow_comments', 1)) }}" />
            <input type="hidden" name="authors" id="params_authors"
                   value="{{ e($authors) }}" />
        @endif

        {{-- Metadata section --}}
        @if($page->access('edit'))
            <x-form-section :heading="Lang::txt('COM_WIKI_FIELDSET_METADATA')">
                <p class="text-sm text-base-content/60 mb-3">
                    {{ Lang::txt('COM_WIKI_FIELD_TAGS_EXPLANATION') }}
                </p>

                <x-form-field name="tags" inputId="actags"
                              :label="Lang::txt('COM_WIKI_FIELD_TAGS')"
                              :hint="Lang::txt('COM_WIKI_FIELD_TAGS_HINT')">
                    @php
                        $tf = Event::trigger(
                            'hubzero.onGetMultiEntry',
                            [['tags', 'tags', 'actags', '', e($tags)]]
                        );
                    @endphp
                    @if(count($tf) > 0)
                        {!! $tf[0] !!}
                    @else
                        <input type="text" name="tags"
                               class="input input-bordered w-full"
                               value="{{ e($tags) }}" />
                    @endif
                </x-form-field>
        @else
                <input type="hidden" name="tags" id="actags"
                       value="{{ e($tags) }}" />
        @endif

                <x-form-field name="revision[summary]" inputId="field-summary"
                              :label="Lang::txt('COM_WIKI_FIELD_EDIT_SUMMARY')"
                              :hint="Lang::txt('COM_WIKI_FIELD_EDIT_SUMMARY_HINT')">
                    <input type="text" name="revision[summary]" id="field-summary"
                           class="input input-bordered w-full"
                           value="{{ e($revision->get('summary')) }}" />
                </x-form-field>

                <input type="hidden" name="revision[minor_edit]" value="1" />
            </x-form-section>

        <div class="form-actions">
            <button type="submit" name="preview" class="btn">
                {{ Lang::txt('COM_WIKI_PREVIEW') }}
            </button>
            <button type="submit" name="submit" class="btn btn-primary">
                {{ Lang::txt('COM_WIKI_SUBMIT') }}
            </button>
        </div>

        {{-- Hidden fields --}}
        <input type="hidden" name="lid" value="{{ $lid }}" />
        <input type="hidden" name="pagename"
               value="{{ e($page->get('pagename')) }}" />
        <input type="hidden" name="page[id]"
               value="{{ e($page->get('id')) }}" />
        <input type="hidden" name="page[access]"
               value="{{ e($page->get('access', 1)) }}" />
        <input type="hidden" name="page[state]"
               value="{{ e($page->get('state', 1)) }}" />
        <input type="hidden" name="page[scope]"
               value="{{ e($page->get('scope', 'site')) }}" />
        <input type="hidden" name="page[scope_id]"
               value="{{ e($page->get('scope_id', 0)) }}" />
        <input type="hidden" name="revision[id]"
               value="{{ e($revision->get('id')) }}" />
        <input type="hidden" name="revision[page_id]"
               value="{{ e($page->get('id')) }}" />
        <input type="hidden" name="revision[version]"
               value="{{ e($revision->get('version')) }}" />
        <input type="hidden" name="revision[created_by]"
               value="{{ e($revision->get('created_by')) }}" />
        <input type="hidden" name="revision[created]"
               value="{{ e($revision->get('created')) }}" />

        @foreach($page->adapter()->routing('save') as $name => $val)
            <input type="hidden" name="{{ e($name) }}" value="{{ e($val) }}" />
        @endforeach
        {!! Html::input('token') !!}
    </form>
</x-page-container>
