{{--
  Popular FAQ module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<div{!! $cssId ? ' id="' . $cssId . '"' : '' !!}{!! $cssClass ? ' class="' . $cssClass . '"' : '' !!}>
  @if ($rows->count() > 0)
    <ul class="list bg-base-100 rounded-box">
      @foreach ($rows as $row)
        <li class="list-row p-3">
          <a href="{{ Route::url($row->link()) }}" class="link link-hover">
            {{ stripslashes($row->get('title')) }}
          </a>
        </li>
      @endforeach
    </ul>
  @else
    <p class="text-base-content/60">{{ Lang::txt('MOD_POPULARFAQ_NO_ARTICLES_FOUND') }}</p>
  @endif
</div>
