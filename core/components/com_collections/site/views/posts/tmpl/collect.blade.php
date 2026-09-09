@php
$formAction = Route::url(
    'index.php?option=' . $option
    . '&controller=' . $controller
    . '&task=collect&post=' . $post_id,
    false
);
@endphp

@if ($__view->getError())
    <div role="alert" class="alert alert-error mb-4">
        <span>{{ $__view->getError() }}</span>
    </div>
@endif

<form action="{{ $formAction }}" method="post" id="hubForm">
    <fieldset class="fieldset bg-base-100 border border-base-300 p-6 rounded-box">
        <legend class="fieldset-legend">
            {{ Lang::txt('COM_COLLECTIONS_COLLECT') }}
        </legend>

        <div class="grid grid-cols-1 md:grid-cols-[1fr_auto_1fr] gap-4 items-end">
            {{-- Select existing collection --}}
            <div class="form-control">
                <label class="label" for="field-collection_id">
                    <span class="label-text">
                        {{ Lang::txt('COM_COLLECTIONS_COLLECTION') }}
                    </span>
                </label>
                <select name="collection_id" id="field-collection_id"
                        class="select select-bordered w-full">
                    <option value="0">
                        {{ Lang::txt('COM_COLLECTIONS_SELECT_COLLECTION') }}
                    </option>
                    <optgroup label="{{ Lang::txt('COM_COLLECTIONS_MY_COLLECTIONS') }}">
                        @if ($myboards)
                            @foreach ($myboards as $board)
                                @if ($board->id == $collection_id)
                                    @continue
                                @endif
                                <option value="{{ e($board->id) }}">
                                    {{ e(stripslashes($board->title)) }}
                                </option>
                            @endforeach
                        @endif
                    </optgroup>
                    @if ($groupboards)
                        @foreach ($groupboards as $optgroup => $boards)
                            <optgroup label="{{ e(stripslashes($optgroup)) }}">
                                @foreach ($boards as $board)
                                    <option value="{{ e($board->id) }}">
                                        {{ e(stripslashes($board->title)) }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    @endif
                </select>
            </div>

            {{-- Or --}}
            <div class="text-center text-base-content/60 font-medium py-2">
                {{ Lang::txt('COM_COLLECTIONS_OR') }}
            </div>

            {{-- Create new collection --}}
            <div class="form-control">
                <label class="label" for="field-collection_title">
                    <span class="label-text">
                        {{ Lang::txt('COM_COLLECTIONS_CREATE_COLLECTION') }}
                    </span>
                </label>
                <input type="text" name="collection_title"
                       id="field-collection_title"
                       class="input input-bordered w-full" />
            </div>
        </div>

        {{-- Description --}}
        <div class="form-control mt-4">
            <label class="label" for="field_description">
                <span class="label-text">
                    {{ Lang::txt('COM_COLLECTIONS_ADD_DESCRIPTION') }}
                </span>
            </label>
            {!! $__view->editor(
                'description', '', 35, 5, 'field_description',
                ['class' => 'minimal no-footer']
            ) !!}
        </div>
    </fieldset>

    <input type="hidden" name="post_id" value="{{ $post_id }}" />
    <input type="hidden" name="repost" value="1" />
    <input type="hidden" name="item_id" value="{{ $item_id }}" />
    <input type="hidden" name="no_html" value="{{ $no_html }}" />
    <input type="hidden" name="id" value="{{ User::get('id') }}" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="task" value="collect" />
    {!! Html::input('token') !!}

    <div class="flex justify-end mt-4">
        <button class="btn btn-primary" type="submit">
            {{ Lang::txt(strtoupper($option) . '_SAVE') }}
        </button>
    </div>
</form>
