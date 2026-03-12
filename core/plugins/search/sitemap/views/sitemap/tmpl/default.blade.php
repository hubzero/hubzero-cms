{{--
  Search Sitemap — Admin view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
@endphp

<div class="admin-fieldset">
  <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_SEARCH_SITEMAP') }}</h3>
  <div class="admin-fieldset-body">

    <form action="index.php?option=com_search"
          method="post"
          name="sitemapForm">
      <input type="hidden"
             name="search-task"
             value="{{ $edit ? 'SiteMapSaveEdit' : 'SiteMapEdit' }}" />

      <table class="admin-table">
        <thead>
          <tr>
            <th scope="col">{{ Lang::txt('COM_SEARCH_COL_TITLE') }}</th>
            <th scope="col">{{ Lang::txt('COM_SEARCH_COL_LINK') }}</th>
            <th scope="col">{{ Lang::txt('COM_SEARCH_COL_DESCRIPTION') }}</th>
            <th scope="col"></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($map as $item)
            <tr>
              @if ($edit == $item['id'])
                @php
                  $titleVal = $_POST['sm-title'] ?? $item['title'];
                  $linkVal  = $_POST['sm-link'] ?? $item['link'];
                  $descVal  = $_POST['sm-description'] ?? $item['description'];
                @endphp
                <td>
                  <input type="text"
                         name="sm-title"
                         class="input input-bordered input-sm w-full"
                         value="{{ $titleVal }}" />
                </td>
                <td>
                  <input type="text"
                         name="sm-link"
                         class="input input-bordered input-sm w-full"
                         value="{{ $linkVal }}" />
                </td>
                <td>
                  <textarea name="sm-description"
                            class="textarea textarea-bordered w-full"
                            rows="3">{{ $descVal }}</textarea>
                </td>
                <td class="text-right">
                  <input type="hidden" name="sm-id" value="{{ $item['id'] }}" />
                  <button type="submit"
                          name="save"
                          class="btn btn-xs btn-success">
                    {{ Lang::txt('COM_SEARCH_SAVE') }}
                  </button>
                  <button type="submit"
                          name="cancel"
                          class="btn btn-xs">
                    {{ Lang::txt('COM_SEARCH_CANCEL') }}
                  </button>
                </td>
              @else
                <td>{{ $item['title'] }}</td>
                <td>{{ $item['link'] }}</td>
                <td>{{ $item['description'] }}</td>
                <td class="text-right">
                  @if (!$edit)
                    <button type="submit"
                            name="edit-{{ $item['id'] }}"
                            class="btn btn-xs btn-outline">
                      {{ Lang::txt('COM_SEARCH_EDIT') }}
                    </button>
                    <button type="submit"
                            name="delete-{{ $item['id'] }}"
                            class="btn btn-xs btn-error btn-outline">
                      {{ Lang::txt('COM_SEARCH_DELETE') }}
                    </button>
                  @endif
                </td>
              @endif
            </tr>
          @endforeach

          @if (!$edit)
            <tr>
              @php
                $newTitleVal = $_POST['new-sm-title'] ?? '';
                $newLinkVal  = $_POST['new-sm-link'] ?? '';
                $newDescVal  = $_POST['new-sm-description'] ?? '';
              @endphp
              <td>
                <input type="text"
                       name="new-sm-title"
                       class="input input-bordered input-sm w-full"
                       placeholder="{{ Lang::txt('COM_SEARCH_COL_TITLE') }}"
                       value="{{ $newTitleVal }}" />
              </td>
              <td>
                <input type="text"
                       name="new-sm-link"
                       class="input input-bordered input-sm w-full"
                       placeholder="{{ Lang::txt('COM_SEARCH_COL_LINK') }}"
                       value="{{ $newLinkVal }}" />
              </td>
              <td>
                <textarea name="new-sm-description"
                          class="textarea textarea-bordered w-full"
                          rows="3"
                          placeholder="{{ Lang::txt('COM_SEARCH_COL_DESCRIPTION') }}">{{ $newDescVal }}</textarea>
              </td>
              <td class="text-right">
                <button type="submit"
                        name="add"
                        class="btn btn-xs btn-success">
                  {{ Lang::txt('COM_SEARCH_ADD') }}
                </button>
              </td>
            </tr>
          @endif
        </tbody>
      </table>
    </form>

  </div>
</div>
