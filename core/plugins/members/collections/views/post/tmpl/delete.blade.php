@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

$base = $member->link() . '&active=' . $name;

$identifier = $post->item()->get('title');
if (!$identifier) {
    $identifier = $post->item()->description('clean');
    if (!$identifier) {
        $identifier = '#' . $post->item()->get('id');
    }
}

$__view->css();
@endphp

@if ($__view->getError())
    <p class="error">{{ $__view->getError() }}</p>
@endif

<form action="{{ Route::url($base . '&task=post/' . $post->get('id') . '/delete') }}"
    method="post"
    id="hubForm"
    class="full">
    <fieldset>
        <legend>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE_HEADER') }}</legend>

        <p class="warning">
            {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE_WARNING', e(stripslashes($identifier))) }}
        </p>

        <div class="form-group form-check">
            <label for="confirmdel" class="form-check-label">
                <input type="checkbox" class="option form-check-input" name="confirmdel" id="confirmdel" value="1" />
                {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE_CONFIRM') }}
            </label>
        </div>
    </fieldset>

    <input type="hidden" name="id" value="{{ $member->get('id') }}" />
    <input type="hidden" name="process" value="1" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="active" value="{{ $name }}" />
    <input type="hidden" name="action" value="delete" />
    <input type="hidden" name="post" value="{{ e($post->get('id')) }}" />
    <input type="hidden" name="no_html" value="{{ $no_html }}" />

    {!! Html::input('token') !!}

    <p class="submit">
        <input type="submit" class="btn btn-danger" value="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE') }}" />
        @if (!$no_html)
            <a class="btn btn-secondary"
                href="{{ Route::url($base . '&task=' . $collection->get('alias')) }}">
                {{ Lang::txt('Cancel') }}
            </a>
        @endif
    </p>
</form>
