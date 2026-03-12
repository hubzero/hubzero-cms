{{--
  Tags — Admin merge

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Lang;

  $canDo = \Components\Tags\Helpers\Permissions::getActions();
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_TAGS') }}: {{ Lang::txt('COM_TAGS_MERGE') }}"
    icon="tags"
    :canDo="$canDo"
    option="{{ $option }}"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <div role="alert" class="alert alert-warning mb-4">
    {{ Lang::txt('COM_TAGS_MERGED_EXPLANATION') }}
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    {{-- Tags being merged --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_TAGS_MERGING') }}">
        <ul class="list-disc pl-5 space-y-1">
          @foreach ($tags as $mtag)
            <li>
              {{ $mtag->get('raw_tag') }}
              ({{ $mtag->get('tag') }} &mdash; {{ $mtag->objects()->total() }})
            </li>
          @endforeach
        </ul>
    </x-admin-fieldset>

    {{-- Merge target --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_TAGS_MERGE_TO') }}">
        <div class="admin-field">
          <label for="newtag" class="label">
            {{ Lang::txt('COM_TAGS_TAG') }}
          </label>
          @php
            $tf = Event::trigger('hubzero.onGetMultiEntry', [
                ['tags', 'newtag', 'newtag']
            ]);
          @endphp
          @if (count($tf))
            {!! implode("\n", $tf) !!}
          @else
            <input type="text"
                   name="newtag"
                   id="newtag"
                   class="input input-bordered w-full" />
          @endif
        </div>
        <p class="text-sm text-muted-foreground">
          {{ Lang::txt('COM_TAGS_SELECT_TAG') }}
        </p>
    </x-admin-fieldset>
  </div>

  <input type="hidden" name="ids" value="{{ $idstr }}" />
  <input type="hidden" name="step" value="{{ $step }}" />
  <input type="hidden" name="task" value="merge" />
</x-admin-edit>
