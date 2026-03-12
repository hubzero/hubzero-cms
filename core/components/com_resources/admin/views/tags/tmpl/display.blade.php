{{--
  Resource Tags — Admin tag management view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $__view->js();
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_RESOURCES') }}: {{ Lang::txt('COM_RESOURCES_TAGS') }} #{{ $row->id }}"
    icon="resources"
    :edit="true"
/>

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('COM_RESOURCES_TAGS_CREATE') }}">
      <p>{{ Lang::txt('COM_RESOURCES_TAGS_CREATE_HELP') }}</p>

      <div class="admin-field">
        <label for="tags-men" class="label">
          {{ Lang::txt('COM_RESOURCES_TAGS_FIELD_NEW_TAGS') }}:
        </label>
        <input type="text"
               name="tags"
               id="tags-men"
               class="input input-bordered w-full"
               value="" />
      </div>
  </x-admin-fieldset>

  <x-admin-fieldset legend="{{ Lang::txt('COM_RESOURCES_TAGS_EXISTING') }}">
      <p>{{ Lang::txt('COM_RESOURCES_TAGS_EXISTING_HELP') }}</p>

      <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
        <table class="admin-table">
          <thead>
            <tr>
              <th class="column-check"></th>
              <th>{{ Lang::txt('COM_RESOURCES_TAGS_RAW_TAG') }}</th>
              <th>{{ Lang::txt('COM_RESOURCES_TAGS_TAG') }}</th>
              <th>{{ Lang::txt('COM_RESOURCES_TAGS_ADMIN') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($tags as $i => $tag)
              @php
                $isChecked = in_array($tag->tag, $mytagarray);
                $tagStr    = $tag->tag;
              @endphp
              <tr>
                <td class="column-check">
                  <input type="checkbox"
                         name="tgs[]"
                         id="cb{{ $i }}"
                         value="{{ $tag->tag }}"
                         class="checkbox checkbox-sm"
                         aria-label="{{ $tag->raw_tag }}"
                         @checked($isChecked) />
                </td>
                <td>
                  <a href="#" class="addtag link link-hover text-primary" data-tag="{{ $tagStr }}">
                    {{ $tag->raw_tag }}
                  </a>
                </td>
                <td>
                  <a href="#" class="addtag link link-hover text-primary" data-tag="{{ $tagStr }}">
                    {{ $tag->tag }}
                  </a>
                </td>
                <td>
                  @if($tag->admin == 1)
                    <span class="badge badge-sm badge-info">admin</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
  </x-admin-fieldset>

  <input type="hidden" name="id" value="{{ $id }}" />
  <input type="hidden" name="task" value="save" />
</x-admin-form>
