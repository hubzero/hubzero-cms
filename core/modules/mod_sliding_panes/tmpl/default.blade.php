{{--
  mod_sliding_panes -- tabbed $content panes

  Variables: $container, $content

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<div id="{{ $container }}">
  @if ($content && count($content))
    {{-- Tab headers --}}
    <div role="tablist" class="tabs tabs-border">
      @foreach ($content as $i => $pane)
        <a role="tab"
           class="tab @if ($i === 0) tab-active @endif"
           id="{{ $container }}-tab-{{ $i }}"
           href="#{{ $container }}-pane-{{ $i }}"
           onclick="document.querySelectorAll('#{{ $container }} [role=tabpanel]').forEach(p=>p.classList.add('hidden'));document.querySelectorAll('#{{ $container }} .tab').forEach(t=>t.classList.remove('tab-active'));document.getElementById('{{ $container }}-pane-{{ $i }}').classList.remove('hidden');this.classList.add('tab-active');return false;">
          {{ $pane->title ?? $pane->alias }}
        </a>
      @endforeach
    </div>

    {{-- Tab panels --}}
    @foreach ($content as $i => $pane)
      <div role="tabpanel"
           class="p-4 @if ($i > 0) hidden @endif"
           id="{{ $container }}-pane-{{ $i }}">
        <div id="{{ $pane->alias }}">
          {!! stripslashes($pane->introtext) !!}
        </div>
      </div>
    @endforeach
  @endif
</div>
