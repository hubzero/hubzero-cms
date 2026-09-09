{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$task = 'post/' . $post_id . '/collect';
if ($collection_id) {
    $task = Request::getString('board', 0) . '/collect';
}
@endphp

@if ($__view->getError())
    <div class="alert alert-error">
        <p>{{ $__view->getError() }}</p>
    </div>
@endif

@php
$formAction = Route::url(
    'index.php?option=' . $option
    . '&cn=' . $group->get('cn')
    . '&active=' . $name
    . '&scope=' . $task
);
@endphp
<form action="{{ $formAction }}"
    method="post"
    id="hubForm"
    class="full">

    <fieldset class="fieldset bg-base-100 border border-base-300 p-4 rounded-box">
        <legend class="fieldset-legend text-lg font-semibold">{{ Lang::txt('Collect') }}</legend>

        <div class="grid grid-cols-1 md:grid-cols-11 gap-4 items-end">
            <div class="md:col-span-5">
                <div class="form-group">
                    <label for="field-collection_id" class="label">
                        <span class="label-text">{{ Lang::txt('Select collection') }}</span>
                    </label>
                    <select name="collection_id" id="field-collection_id" class="select select-bordered w-full">
                        <option value="0">{{ Lang::txt('Select ...') }}</option>
                        <optgroup label="{{ Lang::txt('My collections') }}">
                            @if ($myboards)
                                @foreach ($myboards as $board)
                                    @if ($board->id == $collection_id)
                                        @continue
                                    @endif
                                    <option value="{{ e($board->id) }}">{{ e(stripslashes($board->title)) }}</option>
                                @endforeach
                            @endif
                        </optgroup>
                        @if ($groupboards)
                            @foreach ($groupboards as $optgroup => $boards)
                                <optgroup label="{{ e(stripslashes($optgroup)) }}">
                                    @foreach ($boards as $board)
                                        @if ($board->id == $collection_id)
                                            @continue
                                        @endif
                                        <option value="{{ e($board->id) }}">{{ e(stripslashes($board->title)) }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="md:col-span-1 text-center">
                <p class="font-semibold opacity-60">OR</p>
            </div>
            <div class="md:col-span-5">
                <div class="form-group">
                    <label for="field-collection_title" class="label">
                        <span class="label-text">{{ Lang::txt('Create collection') }}</span>
                    </label>
                    <input type="text"
                        name="collection_title"
                        id="field-collection_title"
                        class="input input-bordered w-full"
                        value="" />
                </div>
            </div>
        </div>

        <div class="form-group mt-4">
            <label for="field_description" class="label">
                <span class="label-text">{{ Lang::txt('Add a description') }}</span>
            </label>
            @php
            echo $__view->editor(
                'description',
                '',
                35,
                5,
                'field_description',
                ['class' => 'form-control minimal no-footer']
            );
            @endphp
        </div>
    </fieldset>

    <input type="hidden" name="post_id" value="{{ $post_id }}" />
    <input type="hidden" name="repost" value="1" />
    <input type="hidden" name="item_id" value="{{ $item_id }}" />
    <input type="hidden" name="no_html" value="{{ $no_html }}" />

    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="cn" value="{{ e($group->get('cn')) }}" />
    <input type="hidden" name="task" value="view" />
    <input type="hidden" name="active" value="{{ e($name) }}" />
    <input type="hidden" name="action" value="collect" />

    {!! Html::input('token') !!}

    <div class="mt-4">
        <button type="submit" class="btn btn-success">
            {{ Lang::txt('PLG_GROUPS_' . strtoupper($name) . '_SAVE') }}
        </button>
    </div>
</form>
