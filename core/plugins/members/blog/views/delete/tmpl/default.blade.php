{{--
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

$base = $member->link() . '&active=blog';

$__view->css()->js();

$title = e(stripslashes($entry->get('title')));
@endphp

<div class="mb-4">
  <a class="btn btn-ghost btn-sm" href="{{ Route::url($base) }}">
    {{ Lang::txt('PLG_MEMBERS_BLOG_ARCHIVE') }}
  </a>
</div>

@if ($__view->getError())
  <div class="alert alert-error mb-4">{{ $__view->getError() }}</div>
@endif

<form action="{{ Route::url($base . '&task=delete&entry=' . $entry->get('id')) }}"
      method="post"
      id="hubForm">

  <div class="card bg-base-100 shadow-sm">
    <div class="card-body">
      <h3 class="card-title">{{ Lang::txt('PLG_MEMBERS_BLOG_DELETE_HEADER') }}</h3>

      <div class="alert alert-warning mt-4">
        {{ Lang::txt('PLG_MEMBERS_BLOG_DELETE_WARNING', $title) }}
      </div>

      <label class="label cursor-pointer justify-start gap-2 mt-4">
        <input type="checkbox" class="checkbox" name="confirmdel" id="confirmdel" value="1" />
        <span class="label-text">{{ Lang::txt('PLG_MEMBERS_BLOG_DELETE_CONFIRM') }}</span>
      </label>

      @if ($authorized)
        <div class="mt-4">
          <a class="btn btn-primary btn-sm" href="{{ Route::url($base . '&task=new') }}">
            {{ Lang::txt('PLG_MEMBERS_BLOG_NEW_ENTRY') }}
          </a>
        </div>
      @endif
    </div>
  </div>

  <input type="hidden" name="id" value="{{ $entry->get('created_by') }}" />
  <input type="hidden" name="process" value="1" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="active" value="blog" />
  <input type="hidden" name="task" value="view" />
  <input type="hidden" name="action" value="delete" />
  <input type="hidden" name="entry" value="{{ $entry->get('id') }}" />

  {!! Html::input('token') !!}

  <div class="flex gap-2 mt-4">
    <button type="submit" class="btn btn-error">
      {{ Lang::txt('PLG_MEMBERS_BLOG_DELETE') }}
    </button>
    <a class="btn btn-ghost" href="{{ Route::url($entry->link()) }}">
      {{ Lang::txt('JCANCEL') }}
    </a>
  </div>
</form>
