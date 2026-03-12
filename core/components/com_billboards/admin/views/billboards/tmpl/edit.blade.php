{{--
  Billboard — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;
  use Components\Billboards\Models\Collection;
  use Components\Billboards\Models\Billboard;

  $text = $row->id ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  Toolbar::title(Lang::txt('COM_BILLBOARDS_MANAGER') . ': ' . $text, 'billboards');
  Toolbar::save();
  Toolbar::cancel();
  Toolbar::spacer();
  Toolbar::help('billboard');


  $__view->js();

  $collections = Collection::all()->rows();
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Main content column --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_BILLBOARDS_CONTENT') }}">

      <div class="admin-field">
        <label for="billboardname" class="label">
          {{ Lang::txt('COM_BILLBOARDS_FIELD_NAME') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="billboard[name]"
               id="billboardname"
               class="input input-bordered w-full required"
               required
               value="{{ $row->get('name', '') }}" />
      </div>

      <div class="admin-field">
        <label for="billboardcollection" class="label">
          {{ Lang::txt('COM_BILLBOARDS_FIELD_COLLECTION') }}
        </label>
        <select name="billboard[collection_id]" id="billboardcollection"
                class="select select-bordered w-full">
          @if($collections->count() > 0)
            @foreach($collections as $collection)
              <option value="{{ $collection->id }}"
                      @selected($collection->id == $row->collection_id)>
                {{ $collection->name }}
              </option>
            @endforeach
          @else
            <option value="0">{{ Lang::txt('Default Collection') }}</option>
          @endif
        </select>
      </div>

      <div class="admin-field">
        <label for="billboardordering" class="label">
          {{ Lang::txt('COM_BILLBOARDS_FIELD_ORDERING') }}
        </label>
        @if($row->id)
          @php
            $query = Billboard::select('ordering', 'value')
                ->select('name', 'text')
                ->whereEquals('collection_id', $row->collection_id)
                ->toString();
          @endphp
          {!! Html::select('ordering', 'billboard[ordering]', $query, null, $row->id) !!}
        @else
          <input type="hidden" name="billboard[ordering]" id="ordering" value="" />
          <span class="text-sm text-muted-foreground">{{ Lang::txt('COM_BILLBOARDS_ASC') }}</span>
        @endif
      </div>

      <div class="admin-field">
        <label for="billboardheader" class="label">
          {{ Lang::txt('COM_BILLBOARDS_FIELD_HEADER') }}
        </label>
        <input type="text"
               name="billboard[header]"
               id="billboardheader"
               class="input input-bordered w-full"
               value="{{ $row->get('header', '') }}" />
      </div>

      <div class="admin-field">
        <label for="billboard-image" class="label">
          {{ Lang::txt('COM_BILLBOARDS_FIELD_BACKGROUND_IMG') }}
        </label>
        <input type="file"
               name="billboard-image"
               id="billboard-image"
               class="file-input file-input-bordered w-full" />
      </div>

      <div class="admin-field">
        <label for="billboard-text" class="label">
          {{ Lang::txt('COM_BILLBOARDS_FIELD_TEXT') }}
        </label>
        {!! $__view->editor(
            'billboard[text]',
            e($row->get('text', '')),
            45,
            13,
            'billboard-text',
            ['buttons' => false]
        ) !!}
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Learn More Link --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_BILLBOARDS_LEARN_MORE') }}">

        <div class="admin-field">
          <label for="billboardlearnmoretext" class="label">
            {{ Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_TEXT') }}
          </label>
          <input type="text"
                 name="billboard[learn_more_text]"
                 id="billboardlearnmoretext"
                 class="input input-bordered w-full"
                 value="{{ $row->get('learn_more_text', '') }}" />
        </div>

        <div class="admin-field">
          <label for="billboardlearnmoretarget" class="label">
            {{ Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_TARGET') }}
          </label>
          <input type="text"
                 name="billboard[learn_more_target]"
                 id="billboardlearnmoretarget"
                 class="input input-bordered w-full"
                 value="{{ $row->get('learn_more_target', '') }}" />
        </div>

        <div class="admin-field">
          <label for="billboardlearnmoreclass" class="label">
            {{ Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_CLASS') }}
          </label>
          <input type="text"
                 name="billboard[learn_more_class]"
                 id="billboardlearnmoreclass"
                 class="input input-bordered w-full"
                 value="{{ $row->get('learn_more_class', '') }}" />
        </div>

        <div class="admin-field">
          <label for="billboardlearnmorelocation" class="label">
            {{ Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_LOCATION') }}
          </label>
          @php $loc = $row->learn_more_location; @endphp
          <select name="billboard[learn_more_location]"
                  id="billboardlearnmorelocation"
                  class="select select-bordered w-full">
            <option value="topleft" @selected($loc == 'topleft')>
              {{ Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_LOCATION_TOP_LEFT') }}
            </option>
            <option value="topright" @selected($loc == 'topright')>
              {{ Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_LOCATION_TOP_RIGHT') }}
            </option>
            <option value="bottomleft" @selected($loc == 'bottomleft')>
              {{ Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_LOCATION_BOTTOM_LEFT') }}
            </option>
            <option value="bottomright" @selected($loc == 'bottomright')>
              {{ Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_LOCATION_BOTTOM_RIGHT') }}
            </option>
            <option value="relative" @selected($loc == 'relative')>
              {{ Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_LOCATION_RELATIVE') }}
            </option>
          </select>
        </div>

    </x-admin-fieldset>

    {{-- Current Image --}}
    @if($row->get('background_img', false))
      @php
        $imgPath = PATH_ROOT . DS . ltrim($row->background_img, DS);
        $image = new \Hubzero\Image\Processor($imgPath);
      @endphp
      @if(count($image->getErrors()) == 0)
        @php $image->resize(500); @endphp
        <x-admin-fieldset legend="{{ Lang::txt('COM_BILLBOARDS_CURRENT_IMG') }}">
            <img src="{{ $image->inline() }}"
                 alt="billboard image"
                 class="rounded-lg w-full" />
        </x-admin-fieldset>
      @endif
    @endif

    {{-- Styling --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_BILLBOARDS_STYLING') }}">

        <div class="admin-field">
          <label for="billboardalias" class="label">
            {{ Lang::txt('COM_BILLBOARDS_FIELD_ALIAS') }}
          </label>
          <input type="text"
                 name="billboard[alias]"
                 id="billboardalias"
                 class="input input-bordered w-full"
                 value="{{ $row->get('alias', '') }}" />
        </div>

        <div class="admin-field">
          <label for="billboardpadding" class="label">
            {{ Lang::txt('COM_BILLBOARDS_FIELD_PADDING') }}
          </label>
          <input type="text"
                 name="billboard[padding]"
                 id="billboardpadding"
                 class="input input-bordered w-full"
                 value="{{ $row->get('padding', '') }}" />
        </div>

        <div class="admin-field">
          <label for="billboardcss" class="label">
            {{ Lang::txt('COM_BILLBOARDS_FIELD_CSS') }}
          </label>
          <textarea name="billboard[css]"
                    id="billboardcss"
                    class="textarea textarea-bordered w-full font-mono text-sm"
                    rows="8">{{ $row->get('css', '') }}</textarea>
        </div>

    </x-admin-fieldset>
  @endslot

  {{-- Hidden fields --}}
  <input type="hidden" name="billboard[id]" value="{{ $row->get('id') }}" />
</x-admin-edit>
