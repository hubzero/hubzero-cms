{{--
  Syndicate (RSS $feed link) module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<a href="{{ $link }}" class="inline-flex items-center gap-1 link link-hover{{ $moduleclass_sfx }}">
  {!! Html::asset('icon', 'feed') !!}
  @if ($params->get('display_text', 1))
    <span>
      @if (str_replace(' ', '', $text) != '')
        {{ $text }}
      @else
        {{ Lang::txt('MOD_SYNDICATE_DEFAULT_FEED_ENTRIES') }}
      @endif
    </span>
  @endif
</a>
