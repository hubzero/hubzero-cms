{{--
  Tag cloud — renders tags as daisyUI badge-outline links.

  Variables (from Cloud::render):
    $tags    — Iterable of Tag model objects
    $config  — Component params (Registry)

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Route;
@endphp

@if($tags->count())
  <ol class="tags">
    @foreach($tags as $tag)
      <li>
        <a class="badge badge-soft badge-primary"
           href="{{ Route::url('index.php?option=com_tags&tag=' . $tag->get('tag'), false) }}"
           rel="tag">
          {{ $tag->get('raw_tag') }}
        </a>
      </li>
    @endforeach
  </ol>
@endif
