@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

$task = 'post/' . $post_id . '/collect';
if ($collection_id) {
    $task = Request::getString('board', 0) . '/collect';
}

$__view->css();
@endphp

@if ($__view->getError())
    <p class="error">{{ $__view->getError() }}</p>
@endif

<form action="{{ Route::url($member->link() . '&active=' . $name . '&task=' . $task) }}"
    method="post"
    id="hubForm"
    class="full">
    <fieldset>
        <legend>{{ Lang::txt('Collect') }}</legend>

        <div class="grid">
            <div class="col span5">
                <div class="form-group">
                    <label for="field-collection_id">
                        {{ Lang::txt('Select collection') }}
                        <select name="collection_id" id="field-collection_id" class="form-control">
                            <option value="0">{{ Lang::txt('Select ...') }}</option>
                            <optgroup label="{{ Lang::txt('My collections') }}">
                                @if ($myboards)
                                    @foreach ($myboards as $board)
                                        @if ($board->id != $collection_id)
                                            <option value="{{ e($board->id) }}">
                                                {{ e(stripslashes($board->title)) }}
                                            </option>
                                        @endif
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
                    </label>
                </div>
            </div>
            <div class="col span2">
                <p class="or">OR</p>
            </div>
            <div class="col span5 omega">
                <div class="form-group">
                    <label for="field-collection_title">
                        {{ Lang::txt('Create collection') }}
                        <input type="text" name="collection_title" id="field-collection_title" class="form-control" />
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="field_description">
                {{ Lang::txt('Add a description') }}
                {!! $__view->editor(
                    'description',
                    '',
                    35,
                    5,
                    'field_description',
                    ['class' => 'form-control minimal no-footer']
                ) !!}
            </label>
        </div>
    </fieldset>

    <input type="hidden" name="post_id" value="{{ $post_id }}" />
    <input type="hidden" name="repost" value="1" />
    <input type="hidden" name="item_id" value="{{ $item_id }}" />
    <input type="hidden" name="no_html" value="{{ $no_html }}" />
    <input type="hidden" name="id" value="{{ $member->get('id') }}" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="active" value="{{ $name }}" />
    <input type="hidden" name="action" value="collect" />

    {!! Html::input('token') !!}

    <p class="submit">
        <input class="btn" type="submit" value="{{ Lang::txt('PLG_MEMBERS_' . strtoupper($name) . '_SAVE') }}" />
    </p>
</form>
