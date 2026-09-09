{{--
  About/index — resource description, custom fields, citations, tags, submitters.

  Variables (from plugin):
    $model   — object: resource model
    $option  — string: component option
    $plugin  — object: plugin params
    $tags    — collection: tag objects

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Component;
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $__view->css();

  $sef = Route::url($model->link());

  // Set the display date
  $thedate = $model->datetime;
  if ($model->isTool() && $model->curtool) {
      $thedate = $model->curtool->released;
  }
  if ($thedate == '0000-00-00 00:00:00') {
      $thedate = '';
  }

  $model->introtext = stripslashes($model->introtext);
  $model->fulltxt = stripslashes($model->fulltxt);
  $model->fulltxt = ($model->fulltxt)
      ? trim($model->fulltxt)
      : trim($model->introtext);
  $model->fulltxt = str_replace(
      '="/site',
      '="' . substr(PATH_APP, strlen(PATH_ROOT)) . '/site',
      $model->fulltxt
  );

  // Parse <nb:field> tags
  $type = $model->type;
  $data = [];
  preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $model->fulltxt, $matches, PREG_SET_ORDER);
  foreach ($matches as $match) {
      $data[$match[1]] = str_replace(
          '="/site',
          '="' . substr(PATH_APP, strlen(PATH_ROOT)) . '/site',
          $match[2]
      );
  }

  $elements = new \Components\Resources\Models\Elements($data, $model->type->customFields);
  $schema = $elements->getSchema();

  if ($model->introtext) {
      Document::setDescription(strip_tags($model->introtext));
  }

  $tab = Request::getCmd('active', 'about');
  $maintext = $model->description;
@endphp

<div class="subject abouttab">
  @if($model->isTool())
    @if(!($model->revision == 'dev' || !$model->toolpublished))
      @php
        $ss = $model->screenshots()
            ->whereEquals('versionid', $model->versionid)
            ->ordered()
            ->rows();

        $__view->view('_screenshots')
            ->set('id', $model->id)
            ->set('created', $model->created)
            ->set('upath', $model->params->get('uploadpath'))
            ->set('versionid', $model->versionid)
            ->set('sinfo', $ss)
            ->set('slidebar', 1)
            ->display();
      @endphp
    @endif
  @endif

  <div class="resource">
    @if($thedate)
      <div class="grid">
        <div class="col span-half">
    @endif

    <h4>{{ Lang::txt('PLG_RESOURCES_ABOUT_CATEGORY') }}</h4>
    @php
      $categoryUrl = Route::url('index.php?option=' . $option . '&type=' . $model->type->get('alias'));
    @endphp
    <p class="resource-content">
      <a href="{{ $categoryUrl }}">{{ e(stripslashes($model->type->get('type'))) }}</a>
    </p>

    @if($thedate)
        </div>
        <div class="col span-half omega">
          <h4>{{ Lang::txt('PLG_RESOURCES_ABOUT_PUBLISHED_ON') }}</h4>
          <p class="resource-content">
            <time datetime="{{ $thedate }}">
              {{ Date::of($thedate)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}
            </time>
          </p>
        </div>
      </div>
    @endif

    @if(!$model->access('view-all'))
      {{-- Protected — only show the introtext --}}
      <h4>{{ Lang::txt('PLG_RESOURCES_ABOUT_ABSTRACT') }}</h4>
      <div class="resource-content">
        {!! $maintext !!}
      </div>
    @else
      @if(trim($maintext))
        <h4>{{ Lang::txt('PLG_RESOURCES_ABOUT_ABSTRACT') }}</h4>
        <div class="resource-content">
          {!! $maintext !!}
        </div>
      @endif

      @php
        $citations = '';
        if (is_object($schema)) {
            if (!isset($schema->fields) || !is_array($schema->fields)) {
                $schema->fields = [];
            }
            foreach ($schema->fields as $field) {
                if (isset($data[$field->name])) {
                    if ($field->name == 'citations') {
                        $citations = $data[$field->name];
                    } elseif (
                        $elements->display($field->type, $data[$field->name])
                        && ((isset($field->display) && $field->display == $tab)
                            || (!isset($field->display) && 'about' == $tab))
                    ) {
                        echo '<h4>' . $field->label . '</h4>';
                        echo '<div class="resource-content">';
                        echo $elements->display($field->type, $data[$field->name]);
                        echo '</div>';
                    }
                }
            }
        }
      @endphp

      @if($model->params->get('show_citation'))
        @php
          $revision = 0;
          $showCitation = $model->params->get('show_citation');

          if ($showCitation == 1 || $showCitation == 2) {
              $cite = new stdClass();
              $cite->title    = $model->title;
              $cite->year     = ($thedate ? Date::of($thedate)->toLocal('Y') : Date::of('now')->toLocal('Y'));
              $cite->location = Request::base() . ltrim($sef, '/');
              $cite->date     = Date::toSql();
              $cite->url      = '';
              $cite->type     = '';

              $authors = [];
              $contributors = $model->isTool()
                  ? $model->contributors('tool')
                  : $model->contributors('!submitter');
              if ($contributors) {
                  foreach ($contributors as $contributor) {
                      if ($contributor->role == 'submitter') {
                          continue;
                      }
                      $authors[] = $contributor->name;
                  }
              }
              $cite->author = implode(';', $authors);

              if ($model->isTool()) {
                  $tconfig = Component::params('com_tools');
                  $doi = '';
                  if ($model->doi && ($model->doi_shoulder || $tconfig->get('doi_shoulder'))) {
                      $doi = ($model->doi_shoulder ?: $tconfig->get('doi_shoulder'))
                          . '/' . strtoupper($model->doi);
                      $cite->doi = $doi;
                  }
                  $revision = $model->revision ?: '';
              }

              if ($showCitation == 2) {
                  $citations = '';
              }
          } else {
              $cite = null;
          }

          $citeinstruct = \Components\Resources\Helpers\Html::citation(
              $option, $cite, $model->id, $citations, $model->type, $revision
          );

          $hasCitations = isset($citations) && ($citations != null || $citations != '');
          $citationsHeading = $hasCitations ? Lang::txt('PLG_RESOURCES_ABOUT_CITE_THIS') : '';
          $citationsContent = $hasCitations ? $citeinstruct : '';
          $hasCite = isset($cite) && ($cite != null || $cite != '');
          $citeHeading = $hasCite ? Lang::txt('PLG_RESOURCES_ABOUT_CITE_THIS') : '';
          $citeContent = $hasCite ? $citeinstruct : '';
        @endphp

        @if($showCitation == 3)
          <h4 id="citethis">{{ $citationsHeading }}</h4>
          <div class="resource-content">{!! $citationsContent !!}</div>
        @else
          <h4>{{ $citeHeading }}</h4>
          <div class="resource-content">{!! $citeContent !!}</div>
        @endif
      @endif
    @endif

    @if($model->attribs->get('timeof', ''))
      <h4>{{ Lang::txt('PLG_RESOURCES_ABOUT_TIME') }}</h4>
      <p class="resource-content"><time>@php
        $timeof = $model->attribs->get('timeof', '');
        if (substr($timeof, -8, 8) == '00:00:00') {
            $exp = Lang::txt('DATE_FORMAT_HZ1');
        } else {
            $exp = Lang::txt('TIME_FORMAT_HZ1') . ', ' . Lang::txt('DATE_FORMAT_HZ1');
        }
        if (substr($timeof, 4, 1) == '-') {
            $seminarTime = ($timeof != '0000-00-00 00:00:00' && $timeof != '')
                ? Date::of($timeof)->toLocal($exp)
                : '';
        } else {
            $seminarTime = $timeof;
        }
        echo e($seminarTime);
      @endphp</time></p>
    @endif

    @if($model->attribs->get('location', ''))
      <h4>{{ Lang::txt('PLG_RESOURCES_ABOUT_LOCATION') }}</h4>
      <p class="resource-content">{{ e($model->attribs->get('location', '')) }}</p>
    @endif

    @if($model->contributors('submitter'))
      <h4>{{ Lang::txt('PLG_RESOURCES_ABOUT_SUBMITTER') }}</h4>
      <div class="resource-content">
        <div id="submitterlist">
          @php
            $view = new \Hubzero\Component\View([
                'base_path' => Component::path('com_resources') . DS . 'site',
                'name'      => 'view',
                'layout'    => '_submitters',
            ]);
            $view->set('option', $option);
            $view->contributors = $model->contributors('submitter');
            $view->badges       = $plugin->get('badges', 0);
            $view->showorgs     = 1;
            $view->display();
          @endphp
        </div>
      </div>
    @endif

    @if($model->params->get('show_assocs'))
      @if($tags->count())
        <h4>{{ Lang::txt('PLG_RESOURCES_ABOUT_TAGS') }}</h4>
        <div class="resource-content">
          @php
            $view = new \Hubzero\Component\View([
                'base_path' => Component::path('com_tags') . '/site',
                'name'      => 'tags',
                'layout'    => '_cloud',
            ]);
            $view->set('config', Component::params('com_tags'));
            $view->set('tags', $tags);
            $view->display();
          @endphp
        </div>
      @endif
    @endif
  </div>
</div>
