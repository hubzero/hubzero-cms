{{--
  Versions browse — tool version history table.

  Variables (from plugin):
    $option   — string: component option
    $resource — object: resource model
    $rows     — array: version records
    $tconfig  — object: com_tools config params

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $__view->css();
  $cls = 'even';
@endphp

<h3 class="section-header">{{ Lang::txt('PLG_RESOURCES_VERSIONS') }}</h3>

@if($rows)
  <table class="resource-versions">
    <thead>
      <tr>
        <th>{{ Lang::txt('PLG_RESOURCES_VERSIONS_VERSION') }}</th>
        <th>{{ Lang::txt('PLG_RESOURCES_VERSIONS_RELEASED') }}</th>
        <th>{{ Lang::txt('PLG_RESOURCES_VERSIONS_DOI_HANDLE') }}</th>
        <th>{{ Lang::txt('PLG_RESOURCES_VERSIONS_PUBLISHED') }}</th>
      </tr>
    </thead>
    <tbody>
      @foreach($rows as $v)
        @php
          $handle = '';
          if (isset($v->doi) && $v->doi && $tconfig->get('doi_shoulder')) {
              $handle = 'doi:'
                  . (isset($v->doi_shoulder) ? $v->doi_shoulder : $tconfig->get('doi_shoulder'))
                  . '/' . strtoupper($v->doi);
              $handle = '<a href="' . $tconfig->get('doi_resolve', 'https://doi.org/') . $handle . '">'
                  . $handle . '</a>';
          } elseif (isset($v->doi_label) && $v->doi_label) {
              $handle = 'doi:10254/' . $tconfig->get('doi_prefix') . $resource->id . '.' . $v->doi_label;
              $handle = '<a href="http://hdl.handle.net/' . $handle . '">' . $handle . '</a>';
          }

          $cls = ($cls == 'even') ? 'odd' : 'even';
        @endphp
        <tr class="{{ $cls }}">
          <td>
            @if($v->version)
              @php
                $versionUrl = Route::url(
                    'index.php?option=' . $option . '&id=' . $resource->id . '&rev=' . $v->revision
                );
              @endphp
              <a href="{{ $versionUrl }}">{{ $v->version }}</a>
            @else
              N/A
            @endif
          </td>
          <td>
            @if($v->released && $v->released != '0000-00-00 00:00:00')
              {{ Date::of($v->released)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}
            @else
              N/A
            @endif
          </td>
          <td>{!! $handle ?: 'N/A' !!}</td>
          <td>
            @php
              $stateClass = ($v->state == '1') ? 'toolpublished' : 'toolunpublished';
              $stateLabel = ($v->state == '1')
                  ? Lang::txt('PLG_RESOURCES_VERSIONS_YES')
                  : Lang::txt('PLG_RESOURCES_VERSIONS_NO');
            @endphp
            <span class="version-state {{ $stateClass }}">{{ $stateLabel }}</span>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
@else
  <p>{{ Lang::txt('PLG_RESOURCES_VERSIONS_NO_VERIONS_FOUND') }}</p>
@endif

<div class="customfields">
  @php
    $data = [];
    preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $resource->fulltxt, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $data[$match[1]] = str_replace('="/site', '="' . substr(PATH_APP, strlen(PATH_ROOT)) . '/site', $match[2]);
    }

    $elements = new \Components\Resources\Models\Elements($data, $resource->type->customFields);
    $schema = $elements->getSchema();
    $tab = Request::getCmd('active', 'versions');

    if (is_object($schema)) {
        if (!isset($schema->fields) || !is_array($schema->fields)) {
            $schema->fields = [];
        }
        foreach ($schema->fields as $field) {
            if (isset($data[$field->name])
                && $elements->display($field->type, $data[$field->name])
                && isset($field->display) && $field->display == $tab
            ) {
                echo '<h4>' . $field->label . '</h4>';
                echo '<div class="resource-content">';
                echo $elements->display($field->type, $data[$field->name]);
                echo '</div>';
            }
        }
    }
  @endphp
</div>
