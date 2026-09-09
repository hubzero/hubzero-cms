{{--
  Publication detail view — upper pane (title, authors, launch area, metadata)
  and lower tabbed content with sidebar extras.

  Variables from controller:
    $option         — component option string
    $publication    — publication model
    $config         — component config
    $contributable  — whether user can contribute
    $authorized     — authorization level
    $restricted     — restriction flag
    $database       — database instance
    $lastPubRelease — last published release
    $version        — version label
    $sections       — sections data for tabs
    $cats           — categories for tabs
    $tab            — active tab name

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  use Components\Publications\Helpers\Html;

  $__view->css();
  $__view->css('jquery.fancybox.css', 'system');
  $__view->js();

  $publication->authors();
  $publication->attachments();
  $publication->license();
@endphp

@if($config->get('launcher_layout', 0))
  @include('view::launcher', [
      'option'         => $option,
      'publication'    => $publication,
      'config'         => $config,
      'contributable'  => $contributable,
      'authorized'     => $authorized,
      'restricted'     => $restricted,
      'database'       => $database,
      'lastPubRelease' => $lastPubRelease,
      'version'        => $version,
      'sections'       => $sections,
      'cats'           => $cats,
  ])
@else
  <x-page-container>
    @slot('sidebar')
      @include('view::_metadata', [
          'option'         => $option,
          'publication'    => $publication,
          'config'         => $config,
          'version'        => $version,
          'sections'       => $sections,
          'cats'           => $cats,
          'params'         => $publication->params,
          'lastPubRelease' => $lastPubRelease,
      ])

      {{-- Lower sidebar: extra content --}}
      @if($publication->access('view-all'))
        @php
          $out = Event::trigger(
              'publications.onPublicationSub',
              [$publication, $option, 1]
          );
        @endphp
        @foreach($out as $ou)
          @if(isset($ou['html']))
            {!! $ou['html'] !!}
          @endif
        @endforeach

        @if($tab == 'about')
          {!! \Hubzero\Module\Helper::renderModules('extracontent') !!}
        @endif
      @endif
    @endslot

    {{-- Title --}}
    {!! Html::title($publication) !!}

    {{-- ========== Upper pane ========== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
      {{-- Left column: authors + abstract --}}
      <div class="lg:col-span-2">
        @if($publication->params->get('show_authors') && $publication->_authors)
          <div id="authorslist" class="mt-4">
            {!! Html::showContributors(
                $publication->_authors,
                true,
                false,
                false,
                false,
                $publication->params->get('format_authors', 0)
            ) !!}
          </div>
        @endif

        @if($publication->abstract)
          <p class="mt-4 text-base-content/80">
            {{ \Hubzero\Utility\Str::truncate(stripslashes(strip_tags($publication->abstract)), 250) }}
          </p>
        @endif

        {!! Html::showSubInfo($publication) !!}
      </div>

      {{-- Right column: launch area --}}
      <div>
        @if($publication->version->get('downloadDisabled'))
          <div role="alert" class="alert alert-warning">
            {{ Lang::txt('COM_PUBLICATIONS_DOWNLOAD_DATASET_DISABLED') }}
            {{ Lang::txt('COM_PUBLICATIONS_PLEASE') }}
            <a class="link" href="/support/ticket/new" target="_blank">
              {{ Lang::txt('COM_PUBLICATIONS_SUBMIT_TICKET') }}
            </a>
            {{ Lang::txt('COM_PUBLICATIONS_TO_INQUIRE_DATASET_STATUS') }}
          </div>
        @else
          @if($tab != 'play')
            @php
              $elements = $publication->_curationModel->getElements(1);
              $attModel = new \Components\Publications\Models\Attachments($database);
              $launcher = '';

              if ($elements) {
                  $element = $elements[0];
                  $launcher = $attModel->drawLauncher(
                      $element->manifest->params->type,
                      $publication,
                      $element,
                      $elements,
                      $publication->access('view-all')
                  );
              }
            @endphp

            @if($elements)
              {!! $launcher !!}
            @endif

            {!! Html::drawSupportingItems($publication) !!}
            {!! Html::showVersionInfo($publication) !!}

            @if($publication->license() && $publication->license()->name != 'standard')
              {!! Html::showLicense($publication, 'play') !!}
            @endif
          @endif
        @endif
      </div>
    </div>

    {{-- Fork attribution --}}
    @if($v = $publication->forked_from)
      @php
        $db = App::get('db');
        $db->setQuery(
            "SELECT publication_id FROM `#__publication_versions` WHERE `id`="
            . $db->quote($v)
        );
        $p = $db->loadResult();

        $ancestor = new \Components\Publications\Models\Publication($p, 'default', $v);

        $from = '';
        if (
            $ancestor->version->get('state') == 1
            && (
                !$ancestor->version->get('published_up')
                || $ancestor->version->get('published_up') == '0000-00-00 00:00:00'
                || (
                    $ancestor->version->get('published_up') != '0000-00-00 00:00:00'
                    && $ancestor->version->get('published_up') <= Date::toSql()
                )
            )
            && (
                !$ancestor->version->get('published_down')
                || $ancestor->version->get('published_down') == '0000-00-00 00:00:00'
                || (
                    $ancestor->version->get('published_down') != '0000-00-00 00:00:00'
                    && $ancestor->version->get('published_down') > Date::toSql()
                )
            )
        ) {
            $ancestorUrl = Route::url(
                'index.php?option=com_publications&id='
                . $ancestor->get('id') . '&v='
                . $ancestor->version->get('version_number')
            );
            $ancestorTitle = e($ancestor->version->get('title'));
            $from = '<a class="link" href="' . $ancestorUrl . '">' . $ancestorTitle . '</a>';
        } else {
            $from = e($ancestor->version->get('title'))
                . ' <span class="badge badge-ghost badge-sm">'
                . Lang::txt('(unpublished)') . '</span>';
        }

        $from .= ' <span class="text-base-content/60">'
            . '<abbr title="' . Lang::txt('Version') . '">v</abbr> '
            . e($ancestor->version->get('version_label'))
            . '</span>';
      @endphp

      <p class="text-sm text-base-content/70 mt-2">
        {!! Lang::txt('Forked from: %s', $from) !!}
      </p>
    @endif

    @if($contributable)
      {!! Html::showAccessMessage($publication) !!}
    @endif

    {{-- ========== Lower pane: tabs + content ========== --}}
    @if($publication->access('view-all'))
      {!! Html::tabs(
          $option,
          $publication->id,
          $cats,
          $tab,
          $publication->alias,
          $version
      ) !!}

      {!! Html::sections(
          $sections,
          $cats,
          $tab,
          'hide',
          'main'
      ) !!}

      @if($tab == 'about')
        {!! Html::footer($publication) !!}
      @endif
    @endif

  </x-page-container>
@endif
