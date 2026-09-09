{{--
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

$__view->css()->js();
@endphp

<div class="mb-4">
  <a class="btn btn-ghost btn-sm" href="{{ Route::url($member->link() . '&active=blog') }}">
    {{ Lang::txt('PLG_MEMBERS_BLOG_ARCHIVE') }}
  </a>
</div>

@if ($__view->getError())
  <div class="alert alert-error mb-4">{{ $__view->getError() }}</div>
@endif

<form action="{{ Route::url($member->link() . '&active=blog&task=savesettings') }}"
      method="post"
      id="hubForm"
      class="space-y-6">

  <fieldset>
    <legend class="text-lg font-semibold">{{ Lang::txt('PLG_MEMBERS_BLOG_SETTINGS_POSTS') }}</legend>
    <p class="text-sm text-base-content/70 mt-1">
      {{ Lang::txt('PLG_MEMBERS_BLOG_SETTINGS_POSTS_EXPLANATION') }}
    </p>
  </fieldset>

  <fieldset class="space-y-4">
    <legend class="text-lg font-semibold">{{ Lang::txt('PLG_MEMBERS_BLOG_SETTINGS_FEEDS') }}</legend>

    <div class="form-control w-full max-w-sm">
      <label class="label" for="field-param-feeds_enabled">
        <span class="label-text">{{ Lang::txt('PLG_MEMBERS_BLOG_SETTINGS_ENTRY_FEED') }}</span>
      </label>
      <select class="select select-bordered" name="params[feeds_enabled]" id="field-param-feeds_enabled">
        <option value="0" {{ !$config->get('feeds_enabled', 1) ? 'selected' : '' }}>
          {{ Lang::txt('PLG_MEMBERS_BLOG_SETTINGS_DISABLED') }}
        </option>
        <option value="1" {{ $config->get('feeds_enabled', 1) == 1 ? 'selected' : '' }}>
          {{ Lang::txt('PLG_MEMBERS_BLOG_SETTINGS_ENABLED') }}
        </option>
      </select>
    </div>

    <div class="form-control w-full max-w-sm">
      <label class="label" for="field-params-feed_entries">
        <span class="label-text">{{ Lang::txt('PLG_MEMBERS_BLOG_SETTINGS_FEED_ENTRY_LENGTH') }}</span>
      </label>
      <select class="select select-bordered" name="params[feed_entries]" id="field-params-feed_entries">
        <option value="full" {{ $config->get('feed_entries', 'partial') == 'full' ? 'selected' : '' }}>
          {{ Lang::txt('PLG_MEMBERS_BLOG_SETTINGS_FULL') }}
        </option>
        <option value="partial" {{ $config->get('feed_entries', 'partial') == 'partial' ? 'selected' : '' }}>
          {{ Lang::txt('PLG_MEMBERS_BLOG_SETTINGS_PARTIAL') }}
        </option>
      </select>
    </div>

    <p class="text-sm text-base-content/60">
      {{ Lang::txt('PLG_MEMBERS_BLOG_SETTINGS_FEED_HELP') }}
    </p>
  </fieldset>

  <input type="hidden" name="id" value="{{ $member->get('id') }}" />
  <input type="hidden" name="process" value="1" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="active" value="blog" />
  <input type="hidden" name="action" value="savesettings" />

  {!! Html::input('token') !!}

  <div class="flex gap-2">
    <button type="submit" class="btn btn-primary">
      {{ Lang::txt('PLG_MEMBERS_BLOG_SAVE') }}
    </button>
    <a class="btn btn-ghost" href="{{ Route::url($member->link() . '&active=blog') }}">
      {{ Lang::txt('JCANCEL') }}
    </a>
  </div>
</form>
