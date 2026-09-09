{{--
  mod_findresources -- resource search with $tags and $categories

  Variables: $tags, $categories, $params

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<div{!! ($params->get('cssId')) ? ' id="' . e($params->get('cssId')) . '"' : '' !!}>

  {{-- Search form --}}
  <form action="{{ Route::url('index.php?option=com_search') }}" method="get">
    <fieldset class="fieldset">
      <label class="label" for="rsearchword">
        {{ Lang::txt('MOD_FINDRESOURCES_SEARCH_LABEL') }}
      </label>
      <div class="join w-full">
        <input type="text" name="terms" id="rsearchword" value=""
               class="input input-bordered join-item flex-1"
               placeholder="{{ Lang::txt('MOD_FINDRESOURCES_SEARCH_LABEL') }}" />
        <input type="hidden" name="domain" value="resources" />
        <button type="submit" class="btn btn-primary join-item">
          {{ Lang::txt('MOD_FINDRESOURCES_SEARCH') }}
        </button>
      </div>
    </fieldset>
  </form>

  {{-- Popular $tags --}}
  @if (count($tags) > 0)
    <div class="mt-4">
      <span class="font-medium text-sm">{{ Lang::txt('MOD_FINDRESOURCES_POPULAR_TAGS') }}</span>
      <div class="flex flex-wrap gap-1 mt-2">
        @foreach ($tags as $tag)
          <a href="{{ Route::url('index.php?option=com_tags&tag=' . $tag->tag) }}"
             class="badge badge-outline">
            {{ stripslashes($tag->raw_tag) }}
          </a>
        @endforeach
        <a href="{{ Route::url('index.php?option=com_tags') }}"
           class="badge badge-primary">
          {{ Lang::txt('MOD_FINDRESOURCES_MORE_TAGS') }}
        </a>
      </div>
    </div>
  @else
    <p class="mt-4 text-sm opacity-70">{{ Lang::txt('MOD_FINDRESOURCES_NO_TAGS') }}</p>
  @endif

  {{-- Categories --}}
  @if (count($categories) > 0)
    <div class="mt-4 text-sm">
      @foreach ($categories as $i => $category)
        @php
          $normalized = preg_replace("/[^a-zA-Z0-9]/", '', strtolower($category->type));
          $typeUrl = Route::url('index.php?option=com_resources&type=' . $normalized);
        @endphp
        <a href="{{ $typeUrl }}" class="link link-hover">{{
            stripslashes($category->type)
        }}</a>{{ ($i == count($categories) - 1) ? '...' : ', ' }}
      @endforeach
      <a href="{{ Route::url('index.php?option=com_resources') }}"
         class="link link-primary font-medium">
        {{ Lang::txt('MOD_FINDRESOURCES_ALL_CATEGORIES') }}
      </a>
    </div>
  @endif

  {{-- Upload $content CTA --}}
  <div class="mt-4 p-4 bg-base-200 rounded-box flex items-center justify-between">
    <h4 class="font-semibold">{{ Lang::txt('MOD_FINDRESOURCES_UPLOAD_CONTENT') }}</h4>
    <a href="{{ Route::url('index.php?option=com_resources&task=new') }}"
       class="btn btn-sm btn-primary">
      {{ Lang::txt('MOD_FINDRESOURCES_GET_STARTED') }}
    </a>
  </div>

</div>
