{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    $webpath = $config->get('webpath');

    $authorized = $publication->access('view-all');

    $abstract = $publication->abstract;
    $description = $publication->describe('parsed');

    $publication->authors();
    $publication->attachments();
    $publication->license();

    // Parse custom metadata from publication metadata
    $data = [];
    preg_match_all(
        "#<nb:(.*?)>(.*?)</nb:(.*?)>#s",
        $publication->metadata ?? '',
        $matches,
        PREG_SET_ORDER
    );
    foreach ($matches as $match) {
        $data[$match[1]] = $match[2];
    }

    $category = $publication->_category;
    $customFields = $publication->_curationModel->getMetaSchema();

    $metaElements = new \Components\Publications\Models\Elements($data, $customFields);
    $schema = $metaElements->getSchema();
@endphp

<div>
    {{-- Gallery images --}}
    @php
        $modelHandler = new \Components\Publications\Models\Handlers($__view->database);
        $handler = $modelHandler->ini('imageviewer');
    @endphp
    @if ($handler)
        {!! $handler->showImageBand($publication) !!}
    @endif

    <h4>{{ Lang::txt('COM_PUBLICATIONS_DESCRIPTION') }}</h4>
    <div>
        {!! $description !!}
    </div>

    {{-- Content list --}}
    @php
        $listAll = $publication->_curationModel->_manifest->params->list_all ?? 0;
        $listLabel = $publication->_curationModel->_manifest->params->list_label
            ?? Lang::txt('COM_PUBLICATIONS_CONTENT_LIST');

        \Hubzero\Document\Assets::addPluginStylesheet('publications', 'supportingdocs');
    @endphp

    @if ($listAll)
        @php
            $prime = $publication->_curationModel->getElements(1);
            $second = $publication->_curationModel->getElements(2);
            $elements = array_merge($prime, $second);

            $attModel = new \Components\Publications\Models\Attachments($__view->database);

            $append = null;
            $list = '';
            $showArchive = false;
            $archiveUrl = '';

            if ($elements) {
                $path = \Components\Publications\Helpers\Html::buildPubPath(
                    $publication->id,
                    $publication->version_id,
                    $webpath,
                    '',
                    1
                );
                $licFile = $path . DS . 'LICENSE.txt';
                if (file_exists($licFile)) {
                    $licenseUrl = Route::url(
                        'index.php?option=' . $option
                        . '&id=' . $publication->id
                        . '&task=license&v=' . $publication->version_id
                    );
                    $append = '<li><a href="' . $licenseUrl . '" class="link" rel="external">'
                        . Lang::txt('COM_PUBLICATIONS_LICENSE_TERMS') . '</a></li>';
                }

                $tarname = Lang::txt('Publication') . '_' . $publication->id . '.zip';
                $archPath = $path . DS . $tarname;

                $showArchiveParam = $publication->_curationModel->_manifest->params->show_archival ?? 0;
                $archiveBase = 'index.php?option=com_publications&id=' . $publication->id
                    . '&task=serve&v=' . $publication->version_number;
                $archiveUrl = Route::url($archiveBase . '&render=archive');
                $showArchive = ($showArchiveParam && file_exists($archPath));

                $list = $attModel->listItems(
                    $elements,
                    $publication,
                    $authorized,
                    $append
                );
            }
        @endphp

        @if ($elements)
            <h4 class="text-lg font-semibold">
                {{ $listLabel ?: Lang::txt('COM_PUBLICATIONS_CONTENT_LIST') }}
                @if ($showArchive && $authorized)
                    <span>
                        @php
                            $browseUrl = Route::url($archiveBase . '&render=showcontents&tmpl=component');
                        @endphp
                        (<a href="{{ $browseUrl }}">
                            {{ Lang::txt('COM_PUBLICATIONS_BROWSE_ARCHIVE_PACKAGE') }}
                        </a>)
                    </span>
                    <span>
                        <a href="{{ $archiveUrl }}">
                            {{ Lang::txt('COM_PUBLICATIONS_ARCHIVE_PACKAGE') }}
                        </a>
                    </span>
                @endif
            </h4>
            <div>
                {!! $list !!}
            </div>
        @endif
    @endif

    {{-- Custom metadata fields --}}
    @php
        $citations = null;
    @endphp
    @if ($publication->params->get('show_metadata'))
        @php
            if (!isset($schema->fields) || !is_array($schema->fields)) {
                $schema = new stdClass();
                $schema->fields = [];
            }
        @endphp
        @foreach ($schema->fields as $field)
            @if (isset($data[$field->name]))
                @if ($field->name == 'citations')
                    @php
                        $citations = $data[$field->name];
                    @endphp
                @else
                    @php
                        $value = $metaElements->display($field->type, $data[$field->name]);
                    @endphp
                    @if ($value)
                        <h4>{{ $field->label }}</h4>
                        <div>
                            {!! $value !!}
                        </div>
                    @endif
                @endif
            @endif
        @endforeach
    @endif

    {{-- Citation --}}
    @if ($publication->params->get('show_citation'))
        @php
            $cite = null;
            $showCitation = $publication->params->get('show_citation');

            if ($showCitation == 1 || $showCitation == 2) {
                $cite = new stdClass();
                $cite->title = $publication->title;

                $hasPublishedUp = $publication->published_up
                    && $publication->published_up != '0000-00-00 00:00:00';
                $cite->year = $hasPublishedUp
                    ? Date::of($publication->published_up)->toLocal('Y')
                    : Date::of('now')->toLocal('Y');

                $cite->location = '';
                $cite->date = '';

                $cite->doi = $publication->doi ?: '';
                $cite->url = $cite->doi
                    ? trim($config->get('doi_resolve', 'https://doi.org/'), '/') . '/' . $cite->doi
                    : null;
                $cite->type = '';
                $cite->pages = '';
                $cite->author = $publication->getUnlinkedContributors();
                $cite->publisher = $config->get('doi_publisher', '');

                if ($publication->version_label > 1) {
                    $cite->version = $publication->version_label;
                }

                if ($showCitation == 2) {
                    $citations = '';
                }
            }

            $citeinstruct = \Components\Publications\Helpers\Html::citation($cite, $publication, $citations);
        @endphp
        <h4 id="citethis">{{ Lang::txt('COM_PUBLICATIONS_CITE_THIS') }}</h4>
        <div>
            {!! $citeinstruct !!}
        </div>
    @endif

    {{-- Submitter --}}
    @if ($publication->params->get('show_submitter') && $publication->submitter())
        <h4>{{ Lang::txt('COM_PUBLICATIONS_SUBMITTER') }}</h4>
        <div>
            @php
                $submitter = $publication->_submitter->name;
                $submitter .= $publication->_submitter->organization
                    ? ', ' . $publication->_submitter->organization : '';
            @endphp
            {{ $submitter }}
        </div>
    @endif

    {{-- Tags --}}
    @if ($publication->params->get('show_tags'))
        @php
            $publication->getTagCloud(User::authorise('core.admin') ? 1 : 0);
        @endphp
        @if ($publication->_tagCloud)
            <h4>{{ Lang::txt('COM_PUBLICATIONS_TAGS') }}</h4>
            <div>
                {!! $publication->_tagCloud !!}
            </div>
        @endif
    @endif

    {{-- Series --}}
    @if ($publication->params->get('show_series'))
        @php
            $series = $publication->getSeries();
        @endphp
        @if ($series)
            @include('about::_series_list', ['series' => $series])
        @endif
    @endif

    {{-- Version notes --}}
    @if ($publication->params->get('show_notes') && $publication->get('release_notes'))
        <h4>{{ Lang::txt('COM_PUBLICATIONS_NOTES') }}</h4>
        <div>
            {!! $publication->notes('parsed') !!}
        </div>
    @endif
</div>
