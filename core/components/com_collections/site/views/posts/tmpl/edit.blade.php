@php
$item = $entry->item();

if (!$entry->exists()) {
    $entry->set('original', 1);
}

$base = 'index.php?option=' . $option . '&controller=' . $controller;
$isNew = !$item->get('id');
$isOriginal = (bool) $entry->get('original');

$dir = $item->get('id') ?: 'tmp' . time();

$saveUrl = Route::url($base . '&task=save', false);
$cancelUrl = $item->get('id')
    ? Route::url($base . '&task=' . $collection->get('alias'), false)
    : '';
@endphp

<x-page-container :title="Lang::txt('COM_COLLECTIONS')">

    @if ($__view->getError())
        <div role="alert" class="alert alert-error mb-4">
            <span>{{ $__view->getError() }}</span>
        </div>
    @endif

    <form action="{{ $saveUrl }}"
          method="post"
          id="hubForm"
          enctype="multipart/form-data">

        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-lg mb-4">
                    {{ $isNew
                        ? Lang::txt('COM_COLLECTIONS_NEW_POST')
                        : Lang::txt('COM_COLLECTIONS_EDIT_POST') }}
                </h2>

                {{-- File upload and link add (original posts only) --}}
                @if ($isOriginal)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            @php
                            $uploaderAction = '/index.php?option=com_collections'
                                . '&no_html=1&controller=media&task=upload';
                            $uploaderList = '/index.php?option=com_collections'
                                . '&no_html=1&controller=media&task=list&dir=';
                            @endphp
                            <div id="ajax-uploader"
                                 data-action="{{ $uploaderAction }}"
                                 data-list="{{ $uploaderList }}">
                                <noscript>
                                    <label class="label" for="upload">
                                        <span class="label-text">
                                            {{ Lang::txt('COM_COLLECTIONS_FILE') }}
                                        </span>
                                    </label>
                                    <input type="file" name="upload" id="upload"
                                           class="file-input file-input-bordered w-full" />
                                </noscript>
                            </div>
                        </div>
                        <div>
                            @php
                            $adderAction = '/index.php?option=com_collections'
                                . '&no_html=1&controller=media&task=create&dir=';
                            $adderList = '/index.php?option=com_collections'
                                . '&no_html=1&controller=media&task=list&dir=';
                            @endphp
                            <div id="link-adder"
                                 data-action="{{ $adderAction }}"
                                 data-list="{{ $adderList }}">
                                <noscript>
                                    <label class="label" for="add-link">
                                        <span class="label-text">
                                            {{ Lang::txt('COM_COLLECTIONS_ADD_A_LINK') }}
                                        </span>
                                    </label>
                                    <input type="text" name="assets[-1][filename]"
                                           id="add-link"
                                           class="input input-bordered w-full"
                                           value="http://" />
                                    <input type="hidden" name="assets[-1][id]" value="0" />
                                    <input type="hidden" name="assets[-1][type]" value="link" />
                                </noscript>
                            </div>
                        </div>
                    </div>

                    {{-- Asset list --}}
                    <div id="ajax-uploader-list" class="mb-4">
                        @php $assets = $item->assets(); @endphp
                        @if ($assets->total() > 0)
                            @foreach ($assets as $i => $asset)
                                <div class="flex items-center gap-3 py-2 border-b border-base-300">
                                    <span class="flex-1 min-w-0">
                                        @if ($asset->get('type') == 'link')
                                            <input type="text"
                                                   name="assets[{{ $i }}][filename]"
                                                   class="input input-bordered input-sm w-full"
                                                   value="{{ e(stripslashes($asset->get('filename'))) }}"
                                                   placeholder="http://" />
                                        @else
                                            {{ e(stripslashes($asset->get('filename'))) }}
                                            <input type="hidden"
                                                   name="assets[{{ $i }}][filename]"
                                                   value="{{ e(stripslashes($asset->get('filename'))) }}" />
                                        @endif
                                    </span>
                                    <input type="hidden"
                                           name="assets[{{ $i }}][type]"
                                           value="{{ e(stripslashes($asset->get('type'))) }}" />
                                    <input type="hidden"
                                           name="assets[{{ $i }}][id]"
                                           value="{{ e($asset->get('id')) }}" />
                                    @php
                                    $removeUrl = Route::url(
                                        $base . '&post=' . $entry->get('id')
                                        . '&task=edit&remove=' . $asset->get('id'),
                                        false
                                    );
                                    @endphp
                                    <a class="btn btn-ghost btn-xs text-error"
                                       href="{{ $removeUrl }}"
                                       title="{{ Lang::txt('COM_COLLECTIONS_DELETE_ASSET') }}">
                                        {{ Lang::txt('COM_COLLECTIONS_DELETE') }}
                                    </a>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    {{-- Title --}}
                    <div class="form-control mb-4">
                        <label class="label" for="field-title">
                            <span class="label-text">
                                {{ Lang::txt('COM_COLLECTIONS_FIELD_TITLE') }}
                            </span>
                        </label>
                        <input type="text" name="fields[title]" id="field-title"
                               class="input input-bordered w-full"
                               value="{{ e(stripslashes($item->get('title'))) }}" />
                    </div>
                @endif

                {{-- Description --}}
                <div class="form-control mb-4">
                    <label class="label" for="field_description">
                        <span class="label-text">
                            {{ Lang::txt('COM_COLLECTIONS_FIELD_DESCRIPTION') }}
                        </span>
                    </label>
                    @if ($isOriginal)
                        {!! $__view->editor(
                            'fields[description]',
                            e(stripslashes($item->description('raw'))),
                            35, 5, 'field_description',
                            ['class' => 'minimal no-footer']
                        ) !!}
                    @else
                        {!! $__view->editor(
                            'post[description]',
                            e(stripslashes($entry->description('raw'))),
                            35, 5, 'field_description',
                            ['class' => 'minimal no-footer']
                        ) !!}
                    @endif
                </div>

                @if ($task == 'save' && !$item->get('description'))
                    <div role="alert" class="alert alert-error mb-4">
                        <span>{{ Lang::txt(strtoupper($option) . '_ERROR_PROVIDE_CONTENT') }}</span>
                    </div>
                @endif

                <input type="hidden" name="fields[type]" value="file" />

                {{-- Collection select and tags --}}
                <div class="grid grid-cols-1 @if($isOriginal) md:grid-cols-2 @endif gap-4 mb-4">
                    <div class="form-control">
                        @if ($collections->total() > 0)
                            <label class="label" for="post-collection_id">
                                <span class="label-text">
                                    {{ Lang::txt('COM_COLLECTIONS_SELECT_COLLECTION') }}
                                </span>
                            </label>
                            <select name="post[collection_id]" id="post-collection_id"
                                    class="select select-bordered w-full">
                                @foreach ($collections as $coll)
                                    <option value="{{ e($coll->get('id')) }}"
                                            @if ($collection->get('id') == $coll->get('id'))
                                                selected
                                            @endif>
                                        {{ e(stripslashes($coll->get('title'))) }}
                                    </option>
                                @endforeach
                            </select>
                            <label class="label">
                                <span class="label-text-alt">
                                    {{ Lang::txt('COM_COLLECTIONS_SELECT_COLLECTION_HINT') }}
                                </span>
                            </label>
                        @else
                            <label class="label" for="post-collection_title">
                                <span class="label-text">
                                    {{ Lang::txt('COM_COLLECTIONS_CREATE_COLLECTION') }}
                                </span>
                            </label>
                            <input type="text" name="collection_title"
                                   id="post-collection_title"
                                   class="input input-bordered w-full" />
                            <label class="label">
                                <span class="label-text-alt">
                                    {{ Lang::txt('COM_COLLECTIONS_CREATE_COLLECTION_HINT') }}
                                </span>
                            </label>
                        @endif
                    </div>

                    @if ($isOriginal)
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">
                                    {{ Lang::txt(strtoupper($option) . '_FIELD_TAGS') }}
                                </span>
                            </label>
                            {!! $__view->autocompleter(
                                'tags', 'tags', e($item->tags('string'))
                            ) !!}
                            <label class="label">
                                <span class="label-text-alt">
                                    {{ Lang::txt(strtoupper($option) . '_FIELD_TAGS_HINT') }}
                                </span>
                            </label>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Hidden fields --}}
        <input type="hidden" name="fields[id]" id="field-id"
               value="{{ $item->get('id') }}" />
        <input type="hidden" name="fields[created]"
               value="{{ $item->get('created') }}" />
        <input type="hidden" name="fields[created_by]"
               value="{{ $item->get('created_by') }}" />
        <input type="hidden" name="fields[dir]" id="field-dir"
               value="{{ $dir }}" />
        <input type="hidden" name="post[id]"
               value="{{ $entry->get('id') }}" />
        <input type="hidden" name="post[item_id]" id="post-item_id"
               value="{{ $entry->get('item_id') }}" />
        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="controller" value="{{ $controller }}" />
        <input type="hidden" name="task" value="save" />
        {!! Html::input('token') !!}

        {{-- Form actions --}}
        <div class="flex items-center justify-end gap-3 mt-6">
            @if ($cancelUrl)
                <a class="btn btn-ghost" href="{{ $cancelUrl }}">
                    {{ Lang::txt('JCANCEL') }}
                </a>
            @endif
            <button class="btn btn-primary" type="submit">
                {{ Lang::txt(strtoupper($option) . '_SAVE') }}
            </button>
        </div>
    </form>

</x-page-container>
