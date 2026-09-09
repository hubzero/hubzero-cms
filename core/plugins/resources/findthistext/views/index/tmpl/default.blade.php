{{--
  Find This Text — links to DOI, local library, Google Scholar, DeepDyve.

  Variables (from plugin):
    $model   — object: resource model
    $openurl — object|null: OpenURL resolver config (->text, ->icon, ->link)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;

  $__view->css();

  // Parse custom fields for DOI/ISBN/ISSN
  $resourceFields = [];
  preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $model->fulltxt, $matches, PREG_SET_ORDER);
  foreach ($matches as $match) {
      $resourceFields[$match[1]] = $match[2];
  }
@endphp

<h3>{{ Lang::txt('PLG_RESOURCES_FINDTHISTEXT') }}</h3>
<p>{{ Lang::txt('PLG_RESOURCES_FINDTHISTEXT_DESC') }}</p>

<table class="find">
  <tbody>
    @if(isset($resourceFields['doi']) && $resourceFields['doi'] != '')
      @php $doiUrl = 'https://doi.org/' . $resourceFields['doi']; @endphp
      <tr>
        <th>{{ Lang::txt('PLG_RESOURCES_FINDTHISTEXT_FIELD_DOI_LABEL') }}</th>
        <td>
          <a rel="external" href="{{ $doiUrl }}">{{ $doiUrl }}</a>
        </td>
      </tr>
    @endif

    @if($openurl)
      <tr>
        <th>{{ Lang::txt('PLG_RESOURCES_FINDTHISTEXT_FIELD_LOCALLIBRARY_LABEL') }}</th>
        <td>
          {{ Lang::txt('PLG_RESOURCES_FINDTHISTEXT_FIELD_LOCALLIBRARY_DESC') }}
          @php
            $linkData = ['title' => $model->title];
            foreach (['doi', 'isbn', 'issn'] as $field) {
                if (isset($resourceFields[$field]) && $resourceFields[$field] != '') {
                    $linkData[$field] = $resourceFields[$field];
                }
            }
            $link = rtrim($openurl->link, '?') . '?' . http_build_query($linkData);
          @endphp
          <a rel="external" href="{{ $link }}">
            @if($openurl->icon)
              <img src="{{ $openurl->icon }}" alt="" />
            @else
              {{ $openurl->text }}
            @endif
          </a>
        </td>
      </tr>
    @endif

    <tr>
      <th>{{ Lang::txt('PLG_RESOURCES_FINDTHISTEXT_FIELD_GOOGLESCHOLAR_LABEL') }}</th>
      <td>
        @php
          $query = '';
          if (isset($resourceFields['doi']) && $resourceFields['doi'] != '') {
              $query = $resourceFields['doi'];
          } elseif ($model->resource && $model->resource->title) {
              $query = $model->title;
          }
          $scholarUrl = 'https://scholar.google.com/scholar?q=' . urlencode($query);
        @endphp
        <a rel="external"
           title="{{ Lang::txt('PLG_RESOURCES_FINDTHISTEXT_FIELD_GOOGLESCHOLAR_LABEL') }}"
           href="{{ $scholarUrl }}">
          {{ Lang::txt('PLG_RESOURCES_FINDTHISTEXT_FIELD_GOOGLESCHOLAR_LABEL') }}
        </a>
      </td>
    </tr>

    <tr>
      <th>{{ Lang::txt('PLG_RESOURCES_FINDTHISTEXT_FIELD_OTHERSOURCES_LABEL') }}</th>
      <td>
        <ul>
          <li>
            @php
              $deepdyveUrl = 'https://www.deepdyve.com/search?query=' . urlencode($model->title);
            @endphp
            {!! Lang::txt('PLG_RESOURCES_FINDTHISTEXT_SOURCES_DEEPDYVE', $deepdyveUrl) !!}
          </li>
        </ul>
      </td>
    </tr>
  </tbody>
</table>
