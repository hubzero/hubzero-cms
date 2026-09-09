{{--
  Group module add/edit form.

  Variables from controller:
    $group         — Group object
    $module        — Module model
    $pages         — collection of Page models
    $order         — collection of modules for ordering
    $stylesheets   — array: content CSS stylesheets
    $notifications — array: queued notification messages

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $__view->css()
         ->js()
         ->css('jquery.fancyselect.css', 'system')
         ->js('jquery.fancyselect', 'system');

  $cn       = $group->get('cn');
  $isEdit   = (bool) $module->get('id');
  $title    = $isEdit
      ? Lang::txt('COM_GROUPS_PAGES_EDIT_MODULE')
      : Lang::txt('COM_GROUPS_PAGES_ADD_MODULE');
  $backUrl  = Route::url('index.php?option=com_groups&cn=' . $cn . '&task=pages#modules');
  $saveUrl  = Route::url('index.php?option=com_groups&cn=' . $cn . '&controller=modules&task=save');

  // Get active menu assignments
  $menus      = $module->menu('list');
  $activeMenu = !$module->get('id') ? [0] : [];
  foreach ($menus as $menu) {
      $activeMenu[] = $menu->get('pageid');
  }

  // CKEditor config
  $allowPhp      = true;
  $allowScripts  = true;
  $startupMode   = 'wysiwyg';
  $showSourceBtn = true;

  if (!$group->isSuperGroup()) {
      $allowPhp     = false;
      $allowScripts = false;
  }

  if (
      strstr(stripslashes($module->get('content')), '<script>') ||
      strstr(stripslashes($module->get('content')), '<?php')
  ) {
      $startupMode = 'source';
  }

  $browseUrl = Route::url(
      'index.php?option=com_groups&cn=' . $cn . '&controller=media&task=filebrowser&tmpl=component'
  );

  $config = [
      'startupMode'               => $startupMode,
      'sourceViewButton'          => $showSourceBtn,
      'contentCss'                => $stylesheets,
      'fileBrowserWindowWidth'    => 1200,
      'fileBrowserBrowseUrl'      => $browseUrl,
      'fileBrowserImageBrowseUrl' => $browseUrl,
      'allowPhpTags'              => $allowPhp,
      'allowScriptTags'           => $allowScripts,
  ];

  if ($group->isSuperGroup()) {
      $config['templates_replace'] = false;
      $config['templates_files'] = [
          'pagelayouts' => substr(PATH_APP, strlen(PATH_ROOT))
              . '/site/groups/' . $group->get('gidNumber')
              . '/template/assets/js/pagelayouts.js',
      ];
  }

  $editor = new \Hubzero\Html\Editor('ckeditor');
@endphp

<x-page-container :title="$title">
  @slot('actions')
    <a class="btn btn-sm" href="{{ $backUrl }}">
      {{ Lang::txt('COM_GROUPS_ACTION_BACK_TO_MANAGE_MODULES') }}
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
        <x-form-section :heading="Lang::txt('COM_GROUPS_PAGES_MODULE_DETAILS')">
          <x-form-field name="module[title]" inputId="field-title"
                        :label="Lang::txt('COM_GROUPS_PAGES_MODULE_TITLE')"
                        :required="true">
            <input type="text" name="module[title]" id="field-title"
                   class="input input-bordered w-full"
                   value="{{ e(stripslashes($module->get('title'))) }}" />
          </x-form-field>

          <x-form-field name="module[content]" inputId="field-content"
                        :label="Lang::txt('COM_GROUPS_PAGES_MODULE_CONTENT')"
                        :required="true">
            {!! $editor->display(
                'module[content]',
                stripslashes($module->get('content')),
                '100%',
                '100px',
                0,
                0,
                false,
                'field-content',
                null,
                null,
                $config
            ) !!}
          </x-form-field>
        </x-form-section>

        <x-form-section :heading="Lang::txt('COM_GROUPS_PAGES_MODULE_MENU_ASSIGNMENT')">
          <x-form-field name="menu[assignment]" inputId="field-assignment"
                        :label="Lang::txt('COM_GROUPS_PAGES_MODULE_ASSIGNMENT')"
                        :required="true">
            <select name="menu[assignment]" id="field-assignment"
                    class="select select-bordered w-full">
              <option value="0">
                {{ Lang::txt('COM_GROUPS_PAGES_MODULE_ASSIGNMENT_ALL') }}
              </option>
              <option value="" @if(!in_array(0, $activeMenu)) selected @endif>
                {{ Lang::txt('COM_GROUPS_PAGES_MODULE_ASSIGNMENT_SELECTED') }}
              </option>
            </select>
          </x-form-field>

          <x-form-field name="menu[assigned][]" inputId="field-assignment-menu"
                        :label="Lang::txt('COM_GROUPS_PAGES_MODULE_SELECTION')">
            <div class="flex gap-2 mb-2">
              <button type="button" id="selectall" class="btn btn-xs btn-ghost">
                {{ Lang::txt('COM_GROUPS_PAGES_MODULE_SELECTION_ALL') }}
              </button>
              <button type="button" id="clearselection" class="btn btn-xs btn-ghost">
                {{ Lang::txt('COM_GROUPS_PAGES_MODULE_SELECTION_CLEAR') }}
              </button>
            </div>
            <fieldset class="assignment space-y-1"
                      @if(in_array(0, $activeMenu)) disabled @endif>
              @foreach($pages as $page)
                @php
                  $ckd = in_array($page->get('id'), $activeMenu) || in_array(0, $activeMenu);
                @endphp
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="checkbox" class="checkbox checkbox-sm"
                         name="menu[assigned][]"
                         value="{{ $page->get('id') }}"
                         @if($ckd) checked @endif />
                  <span>{{ $page->get('title') }}</span>
                </label>
              @endforeach
            </fieldset>
          </x-form-field>
        </x-form-section>
      </div>

      <div class="lg:w-64 shrink-0 space-y-4">
        <x-form-section :heading="Lang::txt('COM_GROUPS_PAGES_MODULE_PUBLISH')">
          <x-form-field name="module[state]" inputId="field-state"
                        :label="Lang::txt('COM_GROUPS_PAGES_MODULE_STATUS')">
            <select name="module[state]" id="field-state"
                    class="select select-bordered w-full">
              <option value="1">{{ Lang::txt('COM_GROUPS_PAGES_MODULE_STATUS_PUBLISHED') }}</option>
              <option value="0">{{ Lang::txt('COM_GROUPS_PAGES_MODULE_STATUS_UNPUBLISHED') }}</option>
            </select>
          </x-form-field>
        </x-form-section>

        <div class="flex gap-2">
          <button type="submit" class="btn btn-info">
            {{ Lang::txt('COM_GROUPS_PAGES_SAVE_MODULE') }}
          </button>
          <a href="{{ $backUrl }}" class="btn">{{ Lang::txt('JCANCEL') }}</a>
        </div>

        <x-form-section :heading="Lang::txt('COM_GROUPS_PAGES_MODULE_SETTINGS')">
          <x-form-field name="module[position]" inputId="field-position"
                        :label="Lang::txt('COM_GROUPS_PAGES_MODULE_POSITION')">
            <input type="text" name="module[position]" id="field-position"
                   class="input input-bordered w-full"
                   value="{{ e(stripslashes($module->get('position'))) }}" />
          </x-form-field>

          @if($isEdit)
            <x-form-field name="module[ordering]" inputId="field-ordering"
                          :label="Lang::txt('COM_GROUPS_PAGES_MODULE_ORDERING')">
              <select name="module[ordering]" id="field-ordering"
                      class="select select-bordered w-full">
                @foreach($order as $k => $o)
                  <option value="{{ $k + 1 }}"
                          @if($o->get('title') === $module->get('title')) selected @endif>
                    {{ ($k + 1) . '. ' . $o->get('title') }}
                  </option>
                @endforeach
              </select>
            </x-form-field>
          @endif
        </x-form-section>
      </div>
    </div>

    <input type="hidden" name="module[id]" value="{{ $module->get('id') }}" />
    <input type="hidden" name="option" value="com_groups" />
    <input type="hidden" name="controller" value="modules" />
    <input type="hidden" name="return"
           value="{{ e(Request::getString('return', '', 'get')) }}" />
    <input type="hidden" name="task" value="save" />
  </form>
</x-page-container>
