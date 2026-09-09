{{--
  Group page add/edit form.

  Variables from controller:
    $group         — Group object
    $page          — Page model
    $version       — PageVersion model
    $pages         — collection of all pages (for parent/ordering selects)
    $categories    — collection of Category models
    $pageTemplates — array: super group page templates
    $stylesheets   — array: content CSS stylesheets
    $config        — Registry: component config
    $notifications — array: queued notification messages

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;

  $__view->css()
         ->js()
         ->css('jquery.fancyselect.css', 'system')
         ->js('jquery.fancyselect', 'system')
         ->css('jquery.colpick.css', 'system')
         ->js('jquery.colpick', 'system');

  $cn = $group->get('cn');
  $base_link = 'index.php?option=com_groups&cn=' . $cn . '&task=pages';

  // Return link
  $return      = Request::getString('return', '');
  $return_link = $base_link;
  if ($return !== '' && filter_var(base64_decode($return), FILTER_VALIDATE_URL)) {
      $return_link = base64_decode($return);
  }

  // Page vars
  $id        = $page->get('id', '');
  $gidNumber = $page->get('gidNumber', '');
  $category  = $page->get('category', '');
  $alias     = $page->get('alias', '');
  $pageTitle = $page->get('title', '');
  $content   = stripslashes($version->get('content', ''));
  $ver       = $version->get('version', 0);
  $state     = $page->get('state', 1);
  $privacy   = $page->get('privacy', 'default');
  $home      = $page->get('home', 0);
  $parent    = $page->get('parent', 0);
  $readonly  = $home ? 'readonly' : '';

  // Comments settings
  $groupParams = new \Hubzero\Config\Registry($group->get('params'));
  $groupCommentSetting = $groupParams->get('page_comments', $config->get('page_comments', 3));
  $groupCommentSettingString = ($groupCommentSetting == 1) ? 'Yes' : 'No';
  $comments = intval($page->get('comments', 3));

  // Page heading
  $pageHeading = $page->get('id')
      ? Lang::txt('COM_GROUPS_PAGES_EDIT_PAGE', $pageTitle)
      : Lang::txt('COM_GROUPS_PAGES_ADD_PAGE');

  // CKEditor config
  $allowPhp      = true;
  $allowScripts  = true;
  $startupMode   = 'wysiwyg';
  $showSourceBtn = true;

  if (!is_object($group->params)) {
      $group->params = new \Hubzero\Config\Registry($group->params);
  }
  if (!$group->params->get('page_trusted', 0)) {
      if (!$group->isSuperGroup()) {
          $allowPhp     = false;
          $allowScripts = false;
          $content      = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);
          $content      = preg_replace('/<\?[\s\S]*?\?>/', '', $content);
      }
  }

  if (strstr(stripslashes($content), '<script>') || strstr(stripslashes($content), '<?php')) {
      $startupMode = 'source';
  }

  $formToken = Session::getFormToken();
  $browseUrl = Route::url(
      'index.php?option=com_groups&cn=' . $cn
      . '&controller=media&task=filebrowser&tmpl=component&' . $formToken . '=1',
      false
  );
  $uploadUrl = Route::url(
      'index.php?option=com_groups&cn=' . $cn
      . '&controller=media&task=ckeditorupload&tmpl=component&' . $formToken . '=1',
      false
  );

  $editorConfig = [
      'startupMode'               => $startupMode,
      'sourceViewButton'          => $showSourceBtn,
      'contentCss'                => $stylesheets,
      'height'                    => '500px',
      'fileBrowserWindowWidth'    => 1200,
      'fileBrowserBrowseUrl'      => $browseUrl,
      'fileBrowserImageBrowseUrl' => $browseUrl,
      'fileBrowserUploadUrl'      => $uploadUrl,
      'allowPhpTags'              => $allowPhp,
      'allowScriptTags'           => $allowScripts,
  ];

  if ($group->isSuperGroup()) {
      $editorConfig['templates_replace'] = false;
      $editorConfig['templates_files'] = [
          'pagelayouts' => '/app/site/groups/' . $group->get('gidNumber')
              . '/template/assets/js/pagelayouts.js',
      ];
  }

  $editor = App::get('editor');

  // Overview access for privacy inherit label
  $access = \Hubzero\User\Group\Helper::getPluginAccess($group, 'overview');
  switch ($access) {
      case 'anyone':     $accessName = Lang::txt('COM_GROUPS_PLUGIN_ANYONE'); break;
      case 'registered': $accessName = Lang::txt('COM_GROUPS_PLUGIN_REGISTERED'); break;
      case 'members':    $accessName = Lang::txt('COM_GROUPS_PLUGIN_MEMBERS'); break;
      default:           $accessName = $access; break;
  }

  $saveUrl = Route::url('index.php?option=com_groups&cn=' . $cn . '&controller=pages&task=save');
@endphp

<x-page-container :title="$pageHeading">
  @slot('actions')
    <a class="btn btn-sm" href="{{ Route::url($base_link) }}">
      {{ Lang::txt('COM_GROUPS_ACTION_BACK_TO_MANAGE_PAGES') }}
    </a>
  @endslot

  @foreach($notifications as $notification)
    <div class="alert alert-{{ $notification['type'] === 'passed' ? 'success' : e($notification['type']) }}"
         role="alert">
      {!! $notification['message'] !!}
    </div>
  @endforeach

  <form action="{{ $saveUrl }}" method="post" id="hubForm">
    <div class="flex flex-col lg:flex-row gap-6">
      <div class="flex-1 min-w-0">
        <x-form-section :heading="Lang::txt('COM_GROUPS_PAGES_PAGE_DETAILS')">
          <x-form-field name="page[title]" inputId="field-title"
                        :label="Lang::txt('COM_GROUPS_PAGES_PAGE_TITLE')"
                        :required="true">
            <input type="text" name="page[title]" id="field-title"
                   class="input input-bordered w-full"
                   value="{{ e(stripslashes($pageTitle)) }}"
                   {{ $readonly }} />
          </x-form-field>

          <x-form-field name="page[alias]" inputId="field-url"
                        :label="Lang::txt('COM_GROUPS_PAGES_PAGE_URL')">
            <input type="text" name="page[alias]" id="field-url"
                   class="input input-bordered w-full"
                   value="{{ e($alias) }}"
                   {{ $readonly }} />
            <span class="text-sm text-base-content/60">
              {{ Lang::txt('COM_GROUPS_PAGES_PAGE_URL_HINT') }}
            </span>
          </x-form-field>

          <x-form-field name="pageversion[content]" inputId="pagecontent"
                        :label="Lang::txt('COM_GROUPS_PAGES_PAGE_CONTENT')"
                        :required="true">
            {!! $editor->display(
                'pageversion[content]',
                e($content),
                '100%',
                '400',
                0,
                0,
                false,
                'pagecontent',
                null,
                null,
                $editorConfig
            ) !!}
          </x-form-field>
        </x-form-section>
      </div>

      <div class="lg:w-64 shrink-0 space-y-4">
        <x-form-section :heading="Lang::txt('COM_GROUPS_PAGES_PAGE_PUBLISH')">
          <x-form-field name="page[state]" inputId="field-state"
                        :label="Lang::txt('COM_GROUPS_PAGES_PAGE_STATUS')"
                        :required="true">
            <select name="page[state]" class="select select-bordered w-full"
                    {{ $readonly }}>
              <option value="1" @if($state == 1) selected @endif>
                {{ Lang::txt('COM_GROUPS_PAGES_PAGE_STATUS_PUBLISHED') }}
              </option>
              <option value="0" @if($state == 0) selected @endif>
                {{ Lang::txt('COM_GROUPS_PAGES_PAGE_STATUS_UNPUBLISHED') }}
              </option>
            </select>
          </x-form-field>

          <x-form-field name="page[privacy]" inputId="field-privacy"
                        :label="Lang::txt('COM_GROUPS_PAGES_PAGE_PRIVACY')"
                        :required="true">
            <select name="page[privacy]" class="select select-bordered w-full">
              <option value="default" @if($privacy === 'default') selected @endif>
                {{ Lang::txt('COM_GROUPS_PAGES_PAGE_PRIVACY_INHERIT', $accessName) }}
              </option>
              <option value="members" @if($privacy === 'members') selected @endif>
                {{ Lang::txt('COM_GROUPS_PAGES_PAGE_PRIVACY_PRIVATE') }}
              </option>
            </select>
          </x-form-field>

          @if($page->get('id'))
            @php
              $versionsUrl = Route::url('index.php?option=com_groups&cn=' . $cn
                  . '&controller=pages&task=versions&pageid=' . $page->get('id'));
            @endphp
            <div>
              <span class="font-semibold text-sm">
                {{ Lang::txt('COM_GROUPS_PAGES_PAGE_VERSIONS') }}:
              </span>
              <a class="btn btn-sm btn-ghost mt-1" href="{{ $versionsUrl }}">
                {{ Lang::txt('COM_GROUPS_PAGES_PAGE_VERSIONS_BROWSE', $page->versions()->count()) }}
              </a>
            </div>
          @endif
        </x-form-section>

        <div class="flex gap-2">
          <button type="submit" class="btn btn-info" data-action="save">
            {{ Lang::txt('COM_GROUPS_PAGES_SAVE_PAGE') }}
          </button>
          <a href="{{ Route::url($return_link) }}" class="btn">{{ Lang::txt('JCANCEL') }}</a>
        </div>

        <x-form-section :heading="Lang::txt('COM_GROUPS_PAGES_PAGE_SETTINGS')">
          @php
            $addCatUrl = Route::url('index.php?option=com_groups&cn=' . $group->get('gidNumber')
                . '&controller=categories&task=add&no_html=1');
          @endphp
          <x-form-field name="page[category]" inputId="page-category"
                        :label="Lang::txt('COM_GROUPS_PAGES_PAGE_CATEGORY')">
            <select name="page[category]" class="select select-bordered w-full page-category"
                    data-url="{{ $addCatUrl }}">
              <option value="">{{ Lang::txt('COM_GROUPS_PAGES_PAGE_CATEGORY_OPTION_NULL') }}</option>
              @foreach($categories as $pageCategory)
                <option value="{{ $pageCategory->get('id') }}"
                        data-color="#{{ $pageCategory->get('color') }}"
                        @if($category == $pageCategory->get('id')) selected @endif>
                  {{ $pageCategory->get('title') }}
                </option>
              @endforeach
              <option value="other">{{ Lang::txt('COM_GROUPS_PAGES_PAGE_CATEGORY_OPTION_OTHER') }}</option>
            </select>
            <span class="text-sm text-base-content/60">
              {{ Lang::txt('COM_GROUPS_PAGES_PAGE_CATEGORY_HINT') }}
            </span>
          </x-form-field>

          @if($page->get('home') == 0)
            <x-form-field name="page[parent]" inputId="page-parent"
                          :label="Lang::txt('COM_GROUPS_PAGES_PAGE_PARENT')">
              <select name="page[parent]" class="select select-bordered w-full page-parent">
                @foreach($pages as $p)
                  @if($p->get('id') == $id) @continue @endif
                  <option value="{{ $p->get('id') }}"
                          @if($parent == $p->get('id')) selected @endif>
                    {!! $p->heirarchyIndicator(' &ndash; ') . e($p->get('title')) !!}
                  </option>
                @endforeach
              </select>
              <span class="text-sm text-base-content/60">
                {{ Lang::txt('COM_GROUPS_PAGES_PAGE_PARENT_HINT') }}
              </span>
            </x-form-field>
          @endif

          @if($page->get('id') && $page->get('home') == 0)
            <x-form-field name="page[left]" inputId="page-ordering"
                          :label="Lang::txt('COM_GROUPS_PAGES_PAGE_ORDER')">
              <select name="page[left]" class="select select-bordered w-full page-ordering">
                @foreach($pages as $p)
                  <option value="{{ $p->get('lft') }}"
                          data-parent="{{ $p->get('parent') }}"
                          @if($p->get('title') === $pageTitle) selected @endif>
                    {{ $p->get('lft') . ' ' . $p->get('title') }}
                  </option>
                @endforeach
              </select>
              <span class="text-sm text-base-content/60">
                {{ Lang::txt('COM_GROUPS_PAGES_PAGE_ORDER_HINT') }}
              </span>
            </x-form-field>
          @endif

          <hr class="my-2" />

          <x-form-field name="page[comments]" inputId="page-comments"
                        :label="Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS')">
            <select name="page[comments]" class="select select-bordered w-full">
              <option value="3" @if($comments === 3) selected @endif>
                {{ Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_INHERIT', $groupCommentSettingString) }}
              </option>
              <option value="0" @if($comments === 0) selected @endif>
                {{ Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_NO') }}
              </option>
              <option value="1" @if($comments === 1) selected @endif>
                {{ Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_YES') }}
              </option>
              <option value="2" @if($comments === 2) selected @endif>
                {{ Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_LOCK') }}
              </option>
            </select>
            <span class="text-sm text-base-content/60">
              {{ Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_HINT') }}
            </span>
          </x-form-field>

          @if($group->isSuperGroup() && count($pageTemplates) > 0)
            <hr class="my-2" />

            <x-form-field name="page[template]" inputId="page-template"
                          :label="Lang::txt('COM_GROUPS_PAGES_PAGE_TEMPLATE')">
              <select name="page[template]" class="select select-bordered w-full">
                <option value="">{{ Lang::txt('COM_GROUPS_PAGES_PAGE_TEMPLATE_OPTION_NULL') }}</option>
                @foreach($pageTemplates as $name => $file)
                  @php $tmpl = str_replace('.php', '', $file); @endphp
                  <option value="{{ $tmpl }}"
                          @if($page->get('template') === $tmpl) selected @endif>
                    {{ $name }}
                  </option>
                @endforeach
              </select>
              <span class="text-sm text-base-content/60">
                {{ Lang::txt('COM_GROUPS_PAGES_PAGE_TEMPLATE_HINT') }}
              </span>
            </x-form-field>
          @endif
        </x-form-section>
      </div>
    </div>

    {!! Html::input('token') !!}
    <input type="hidden" name="page[id]" value="{{ $id }}" />
    <input type="hidden" name="option" value="com_groups" />
    <input type="hidden" name="controller" value="pages" />
    <input type="hidden" name="return"
           value="{{ e(Request::getString('return', '', 'get')) }}" />
    <input type="hidden" name="task" value="save" />
  </form>
</x-page-container>
