{{--
  Citations browse — affiliated and non-affiliated citation lists.

  Variables (from plugin):
    $option         — string: component option
    $resource       — object: resource model
    $citations      — array: citation objects
    $citationFormat — string: citation display format

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $numaff = 0;
  $numnon = 0;
  $affiliated = '';
  $nonaffiliated = '';

  if ($citations) {
      $formatter = new \Components\Citations\Helpers\Format();

      foreach ($citations as $cite) {
          $item  = "\t<li>\n";
          $item .= $cite->formatted(['format' => $citationFormat]);

          $bibtexUrl = Route::url(
              'index.php?option=com_citations&task=download&id=' . $cite->id
              . '&citationFormat=bibtex&no_html=1'
          );
          $endnoteUrl = Route::url(
              'index.php?option=com_citations&task=download&id=' . $cite->id
              . '&citationFormat=endnote&no_html=1'
          );

          $item .= "\t\t<p class=\"details\">\n";
          $item .= "\t\t\t<a href=\"" . $bibtexUrl . '" title="'
              . Lang::txt('PLG_RESOURCES_CITATIONS_DOWNLOAD_BIBTEX') . '">BibTex</a> <span>|</span> ' . "\n";
          $item .= "\t\t\t<a href=\"" . $endnoteUrl . '" title="'
              . Lang::txt('PLG_RESOURCES_CITATIONS_DOWNLOAD_ENDNOTE') . '">EndNote</a>' . "\n";

          if ($cite->eprint) {
              $item .= "\t\t\t <span>|</span> <a href=\"" . stripslashes($cite->eprint) . '">'
                  . Lang::txt('PLG_RESOURCES_CITATIONS_ELECTRONIC_PAPER') . "</a>\n";
          }

          $item .= "\t\t</p>\n";
          $item .= "\t</li>\n";

          if ($cite->affiliated) {
              $affiliated .= $item;
              $numaff++;
          } else {
              $nonaffiliated .= $item;
              $numnon++;
          }
      }
  }

  $citBase = 'index.php?option=' . $option . '&id=' . $resource->id . '&active=citations';
@endphp

<h3>
  {{ Lang::txt('PLG_RESOURCES_CITATIONS') }}
  <span>
    <a href="{{ Route::url($citBase . '#nonaffiliated') }}">{{ Lang::txt('PLG_RESOURCES_CITATIONS_NONAFF') }} ({{ $numnon }})</a> |
    <a href="{{ Route::url($citBase . '#affiliated') }}">{{ Lang::txt('PLG_RESOURCES_CITATIONS_AFF') }} ({{ $numaff }})</a>
  </span>
</h3>

@if($citations)
  @if($nonaffiliated)
    <h4>{{ Lang::txt('PLG_RESOURCES_CITATIONS_NOT_AFFILIATED') }}</h4>
    <ul class="citations results">
      {!! $nonaffiliated !!}
    </ul>
  @endif
  @if($affiliated)
    <h4>{{ Lang::txt('PLG_RESOURCES_CITATIONS_AFFILIATED') }}</h4>
    <ul class="citations results">
      {!! $affiliated !!}
    </ul>
  @endif
@else
  <p>{{ Lang::txt('PLG_RESOURCES_CITATIONS_NO_CITATIONS_FOUND') }}</p>
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
    $tab = Request::getCmd('active', 'citations');

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
