{{--
  Categories — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $userId    = User::get('id');
  $extension = $filters['extension'];
  $sort      = $filters['sort'] ?? 'lft';
  $sortDir   = $filters['sort_Dir'] ?? 'asc';
  $saveOrder = ($sort == 'lft') && !empty($extension);

  // Load the current extension's sys language file for the toolbar title
  // e.g. com_blog.sys → COM_BLOG="Blog"
  $lang = \Hubzero\Facades\App::get('language');
  if ($extension && !$lang->hasKey($extension)) {
      $sysFile = $extension . '.sys';
      $componentPath = \Hubzero\Facades\App::get('component')->path($extension) . '/admin';
      $lang->load($sysFile, PATH_APP . '/bootstrap/administrator', null, false, false)
          || $lang->load($sysFile, $componentPath, null, false, false);
  }

  // When ordering is active, group items by parent_id so siblings
  // are contiguous. Within each group, original lft order is preserved.
  if ($saveOrder) {
      $grouped = [];
      $groupOrder = [];
      foreach ($items as $itm) {
          $pid = $itm->parent_id;
          if (!isset($grouped[$pid])) {
              $grouped[$pid] = [];
              $groupOrder[] = $pid;
          }
          $grouped[$pid][] = $itm;
      }
      $items = [];
      foreach ($groupOrder as $pid) {
          foreach ($grouped[$pid] as $itm) {
              $items[] = $itm;
          }
      }
  }

  Toolbar::title(
      Lang::txt('COM_CATEGORIES_CATEGORIES_TITLE', Lang::txt($extension)),
      'categories'
  );
  if ($canDo->get('core.create')) {
      Toolbar::addNew();
  }
  if ($canDo->get('core.edit')) {
      Toolbar::editList();
  }
  Toolbar::spacer();
  if ($canDo->get('core.edit.state')) {
      Toolbar::publishList();
      Toolbar::unpublishList();
      Toolbar::spacer();
      Toolbar::archiveList();
      Toolbar::checkin();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList('', 'trash');
  }
  Toolbar::spacer();
  if ($canDo->get('core.admin')) {
      Toolbar::preferences($filters['extension'], '550');
  }
  Toolbar::help('categories');

  // Published filter options
  $publishedOptions = [
      ['value' => '',   'text' => Lang::txt('JOPTION_SELECT_PUBLISHED')],
      ['value' => '1',  'text' => Lang::txt('JPUBLISHED')],
      ['value' => '0',  'text' => Lang::txt('JUNPUBLISHED')],
      ['value' => '2',  'text' => Lang::txt('JARCHIVED')],
      ['value' => '-2', 'text' => Lang::txt('JTRASHED')],
  ];

  // Access filter options
  $accessOptions = Html::access('assetgroups');

  // Language filter options
  $langOptions = Html::contentlanguage('existing', true, true);

  // Level filter options
  $levelOptions = [];
  foreach ($f_levels as $lvl) {
      $levelOptions[] = ['value' => $lvl, 'text' => $lvl];
  }

  // Build access level lookup
  $accessLookup = [];
  if (is_array($accessOptions)) {
      foreach ($accessOptions as $opt) {
          $v = is_object($opt) ? $opt->value : ($opt['value'] ?? '');
          $t = is_object($opt) ? $opt->text  : ($opt['text'] ?? '');
          $accessLookup[$v] = $t;
      }
  }

  // Extension filter options (from DB)
  $db = \Hubzero\Facades\App::get('db');
  $db->setQuery(
      "SELECT DISTINCT extension FROM `#__categories`"
      . " WHERE extension != 'system' ORDER BY extension"
  );
  $extensionOptions = $db->loadColumn() ?: [];

  // Load remaining extension sys language files for the dropdown
  $extensionNames = [];
  foreach ($extensionOptions as $ext) {
      if (!$lang->hasKey($ext)) {
          $sysFile = $ext . '.sys';
          $componentPath = \Hubzero\Facades\App::get('component')->path($ext) . '/admin';
          $lang->load($sysFile, PATH_APP . '/bootstrap/administrator', null, false, false)
              || $lang->load($sysFile, $componentPath, null, false, false);
      }
      $extensionNames[$ext] = $lang->hasKey($ext)
          ? Lang::txt($ext)
          : ucwords(str_replace('_', ' ', str_replace('com_', '', $ext)));
  }

  // Build parent title lookup for ordering context
  $parentTitles = [];
  foreach ($items as $itm) {
      $parentTitles[$itm->id] = $itm->title;
  }
  // Look up any parent titles not on the current page
  $missingParentIds = [];
  foreach ($items as $itm) {
      if ($itm->parent_id > 1 && !isset($parentTitles[$itm->parent_id])) {
          $missingParentIds[$itm->parent_id] = true;
      }
  }
  if (!empty($missingParentIds)) {
      $ids = implode(',', array_map('intval', array_keys($missingParentIds)));
      $db->setQuery("SELECT id, title FROM `#__categories` WHERE id IN ($ids)");
      foreach ($db->loadObjectList() as $row) {
          $parentTitles[$row->id] = $row->title;
      }
  }

  $originalOrders = [];
@endphp

<x-admin-form
    option="com_categories"
    controller="categories"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  @slot('filters')
    <div class="flex flex-wrap items-center gap-2 w-full">
      <div class="flex items-center gap-2">
        <input type="text"
               name="filter_search"
               id="filter_search"
               class="input input-sm input-bordered w-64"
               placeholder="{{ Lang::txt('COM_CATEGORIES_ITEMS_SEARCH_FILTER') }}"
               value="{{ $filters['search'] ?? '' }}" />
        <button type="submit" class="btn btn-sm btn-primary">
          {{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}
        </button>
      </div>

      <div class="flex flex-wrap items-center gap-2 ml-auto">
        <select name="extension"
                id="filter_extension"
                class="select select-sm select-bordered"
                aria-label="{{ Lang::txt('COM_CATEGORIES_FILTER_EXTENSION') }}"
                data-submit-on-change>
          <option value="">All Extensions</option>
          @foreach($extensionOptions as $ext)
            <option value="{{ $ext }}"
                    @selected($extension === $ext)>
              {{ $extensionNames[$ext] }}
            </option>
          @endforeach
        </select>

        <select name="filter_level"
                id="filter_level"
                class="select select-sm select-bordered"
                aria-label="{{ Lang::txt('JOPTION_SELECT_MAX_LEVELS') }}"
                data-submit-on-change>
          <option value="">{{ Lang::txt('JOPTION_SELECT_MAX_LEVELS') }}</option>
          @foreach($levelOptions as $opt)
            <option value="{{ $opt['value'] }}"
                    @selected(($filters['level'] ?? '') == $opt['value'])>
              {{ $opt['text'] }}
            </option>
          @endforeach
        </select>

        <select name="filter_published"
                id="filter_published"
                class="select select-sm select-bordered"
                aria-label="{{ Lang::txt('JOPTION_SELECT_PUBLISHED') }}"
                data-submit-on-change>
          @foreach($publishedOptions as $opt)
            <option value="{{ $opt['value'] }}"
                    @selected(($filters['published'] ?? '') === $opt['value'])>
              {{ $opt['text'] }}
            </option>
          @endforeach
        </select>

        <select name="filter_access"
                id="filter_access"
                class="select select-sm select-bordered"
                aria-label="{{ Lang::txt('JOPTION_SELECT_ACCESS') }}"
                data-submit-on-change>
          <option value="">{{ Lang::txt('JOPTION_SELECT_ACCESS') }}</option>
          @if(is_array($accessOptions))
            @foreach($accessOptions as $opt)
              @php
                $aVal = is_object($opt) ? $opt->value : ($opt['value'] ?? '');
                $aTxt = is_object($opt) ? $opt->text  : ($opt['text'] ?? '');
              @endphp
              <option value="{{ $aVal }}"
                      @selected(($filters['access'] ?? '') == $aVal)>
                {{ $aTxt }}
              </option>
            @endforeach
          @endif
        </select>

        <select name="filter_language"
                id="filter_language"
                class="select select-sm select-bordered"
                aria-label="{{ Lang::txt('JOPTION_SELECT_LANGUAGE') }}"
                data-submit-on-change>
          <option value="">{{ Lang::txt('JOPTION_SELECT_LANGUAGE') }}</option>
          @if(is_array($langOptions))
            @foreach($langOptions as $opt)
              @php
                $lVal = is_object($opt) ? $opt->value : ($opt['value'] ?? '');
                $lTxt = is_object($opt) ? $opt->text  : ($opt['text'] ?? '');
              @endphp
              <option value="{{ $lVal }}"
                      @selected(($filters['language'] ?? '') == $lVal)>
                {{ $lTxt }}
              </option>
            @endforeach
          @endif
        </select>
      </div>
    </div>
  @endslot

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>
            {!! Html::grid('sort', 'JGLOBAL_TITLE', 'title', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'JSTATUS', 'published', $sortDir, $sort) !!}
          </th>
          @if($saveOrder)
            <th class="w-20 text-center">
              {{ Lang::txt('JGRID_HEADING_ORDERING') }}
            </th>
          @endif
          <th>
            {!! Html::grid('sort', 'JGRID_HEADING_ACCESS', 'access', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'JGRID_HEADING_LANGUAGE', 'language', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'JGRID_HEADING_ID', 'id', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="{{ $saveOrder ? 7 : 6 }}">
            <div class="admin-pagination">
              {!! $pagination->getListFooter() !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @php
          $rowIndex = 0;
          $lastParentId = null;
          $colCount = $saveOrder ? 7 : 6;
        @endphp
        @foreach($items as $i => $item)
          {{-- Skip the ROOT system category --}}
          @if($item->alias === 'root' && $item->extension === 'system')
            @continue
          @endif
          @if($saveOrder && $item->parent_id !== $lastParentId)
            @php
              $groupLabel = ($item->level <= 1)
                  ? 'Top Level'
                  : (isset($parentTitles[$item->parent_id])
                      ? 'children of ' . $parentTitles[$item->parent_id]
                      : 'children of #' . $item->parent_id);
              $lastParentId = $item->parent_id;
            @endphp
            <tr class="order-group-header">
              <td colspan="{{ $colCount }}">
                <span class="order-group-label">{{ $groupLabel }}</span>
              </td>
            </tr>
          @endif
          @php
            $orderkey = array_search(
                $item->id,
                $ordering[$item->parent_id] ?? []
            );
            $catAsset   = $extension . '.category.' . $item->id;
            $canEdit    = User::authorise('core.edit', $catAsset);
            $canCheckin = User::authorise('core.admin', 'com_checkin')
                || $item->checked_out == $userId
                || $item->checked_out == 0;
            $canEditOwn = User::authorise('core.edit.own', $catAsset)
                && $item->created_user_id == $userId;
            $canChange  = $canEdit || $canEditOwn
                || User::authorise('core.edit.state', $catAsset)
                || User::authorise('core.edit.state', $extension);

            $editUrl = Route::url(
                'index.php?option=com_categories&task=category.edit'
                . '&id=' . $item->id
                . '&extension=' . $extension,
                false, false
            );

            // Tree indentation
            $depth = max(0, $item->level - 1);
            $indentHtml = $depth > 0
                ? '<span class="text-faint-foreground select-none" aria-hidden="true">'
                  . str_repeat('— ', $depth)
                  . '</span>'
                : '';

            // State
            $pub = $item->get('published', 0);
            if ($pub == 1) {
                $stateText = Lang::txt('JPUBLISHED');
                $stateCls  = 'badge-success';
            } elseif ($pub == 2) {
                $stateText = Lang::txt('JARCHIVED');
                $stateCls  = 'badge-info';
            } elseif ($pub == -2) {
                $stateText = Lang::txt('JTRASHED');
                $stateCls  = 'badge-warning';
            } else {
                $stateText = Lang::txt('JUNPUBLISHED');
                $stateCls  = 'badge-ghost';
            }

            // Ordering
            $hasPrev = $saveOrder && isset($ordering[$item->parent_id][$orderkey - 1]);
            $hasNext = $saveOrder && isset($ordering[$item->parent_id][$orderkey + 1]);
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="cid[]"
                     id="cb{{ $rowIndex }}"
                     value="{{ $item->id }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ Lang::txt('JGRID_CHECKBOX_ROW_N', $rowIndex + 1) }}"
                     data-check-item />
            </td>
            <td>
              <div class="flex items-center gap-1">
                {!! $indentHtml !!}
                @if($item->checked_out)
                  {!! Html::grid(
                      'checkedout', $rowIndex, $item->editor->name ?? '',
                      $item->checked_out_time, 'categories.', $canCheckin
                  ) !!}
                @endif
                @if($canEdit || $canEditOwn)
                  <a href="{{ $editUrl }}"
                     class="link link-hover text-primary font-medium">
                    {{ $item->title }}
                  </a>
                @else
                  {{ $item->title }}
                @endif
              </div>
              <div class="text-xs text-muted-foreground mt-0.5 ml-{{ $depth > 0 ? 4 : 0 }}">
                {{ $item->alias }}
                @if(!empty($item->note))
                  <span class="text-muted-foreground italic ml-1">{{ $item->note }}</span>
                @endif
              </div>
            </td>
            <td class="column-status">
              @if($canChange)
                <a href="{{ Route::url('index.php?option=com_categories&task=categories.' . ($pub == 1 ? 'unpublish' : 'publish') . '&id=' . $item->id . '&extension=' . $extension . '&' . Session::getFormToken() . '=1', false) }}">
                  <span class="badge badge-sm {{ $stateCls }}">{{ $stateText }}</span>
                </a>
              @else
                <span class="badge badge-sm {{ $stateCls }}">{{ $stateText }}</span>
              @endif
            </td>
            @if($saveOrder)
              <td class="text-center whitespace-nowrap">
                <div class="order-group">
                  @if($hasPrev)
                    <button type="button"
                            class="order-btn"
                            data-order-btn
                            data-cb="cb{{ $rowIndex }}"
                            data-task="categories.orderup"
                            title="{{ Lang::txt('JLIB_HTML_MOVE_UP') }}">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                           stroke="currentColor" stroke-width="2.5"
                           stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 15l7-7 7 7"/>
                      </svg>
                    </button>
                  @else
                    <span class="order-btn is-disabled">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                           stroke="currentColor" stroke-width="2.5"
                           stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 15l7-7 7 7"/>
                      </svg>
                    </span>
                  @endif
                  <span class="order-num">{{ $orderkey + 1 }}</span>
                  @if($hasNext)
                    <button type="button"
                            class="order-btn"
                            data-order-btn
                            data-cb="cb{{ $rowIndex }}"
                            data-task="categories.orderdown"
                            title="{{ Lang::txt('JLIB_HTML_MOVE_DOWN') }}">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                           stroke="currentColor" stroke-width="2.5"
                           stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 9l-7 7-7-7"/>
                      </svg>
                    </button>
                  @else
                    <span class="order-btn is-disabled">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                           stroke="currentColor" stroke-width="2.5"
                           stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 9l-7 7-7-7"/>
                      </svg>
                    </span>
                  @endif
                </div>
                @php $originalOrders[] = $orderkey + 1; @endphp
              </td>
            @endif
            <td>{{ $accessLookup[$item->access] ?? $item->access }}</td>
            <td>
              @if($item->language == '*')
                {{ Lang::txt('JALL', 'language') }}
              @else
                {{ $item->language_title ?? Lang::txt('JUNDEFINED') }}
              @endif
            </td>
            <td>
              <span title="{{ $item->lft }}-{{ $item->rgt }}">
                {{ (int) $item->id }}
              </span>
            </td>
          </tr>
          @php $rowIndex++; @endphp
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- Batch processing --}}
  @php
    $canBatch = User::authorise('core.create', $extension)
        && User::authorise('core.edit', $extension)
        && User::authorise('core.edit.state', $extension);
  @endphp
  @if($canBatch)
    @php
      $batchAccessOptions = Html::access('assetgroups');
      $batchLangOptions   = Html::contentlanguage('existing', true, true);
      $batchCatOptions    = Html::category('categories', $extension, [
          'filter.published' => is_numeric($filters['published'] ?? '')
              ? $filters['published'] : '',
      ]);
    @endphp
    <details class="batch-details border border-base-300 bg-base-100 rounded-box mt-4">
      <summary class="batch-summary">
        <svg class="batch-chevron" width="16" height="16" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2.5"
             stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 18l6-6-6-6"/>
        </svg>
        {{ Lang::txt('COM_CATEGORIES_BATCH_OPTIONS') }}
      </summary>
      <div class="batch-content">
        <p class="batch-tip">
          {{ Lang::txt('COM_CATEGORIES_BATCH_TIP') }}
        </p>
        <div class="batch-grid">
          <div class="batch-field">
            <label for="batch-access">{{ Lang::txt('JLIB_HTML_BATCH_ACCESS_LABEL') }}</label>
            <select name="batch[assetgroup_id]" id="batch-access">
              <option value="">{{ Lang::txt('JLIB_HTML_BATCH_NOCHANGE') }}</option>
              @if(is_array($batchAccessOptions))
                @foreach($batchAccessOptions as $opt)
                  @php
                    $v = is_object($opt) ? $opt->value : ($opt['value'] ?? '');
                    $t = is_object($opt) ? $opt->text  : ($opt['text'] ?? '');
                  @endphp
                  <option value="{{ $v }}">{{ $t }}</option>
                @endforeach
              @endif
            </select>
          </div>
          <div class="batch-field">
            <label for="batch-language-id">{{ Lang::txt('JLIB_HTML_BATCH_LANGUAGE_LABEL') }}</label>
            <select name="batch[language_id]" id="batch-language-id">
              <option value="">{{ Lang::txt('JLIB_HTML_BATCH_LANGUAGE_NOCHANGE') }}</option>
              @if(is_array($batchLangOptions))
                @foreach($batchLangOptions as $opt)
                  @php
                    $v = is_object($opt) ? $opt->value : ($opt['value'] ?? '');
                    $t = is_object($opt) ? $opt->text  : ($opt['text'] ?? '');
                  @endphp
                  <option value="{{ $v }}">{{ $t }}</option>
                @endforeach
              @endif
            </select>
          </div>
          <div class="batch-field">
            <label for="batch-category-id">{{ Lang::txt('COM_CATEGORIES_BATCH_CATEGORY_LABEL') }}</label>
            <select name="batch[category_id]" id="batch-category-id">
              <option value="">{{ Lang::txt('JSELECT') }}</option>
              @if(is_array($batchCatOptions))
                @foreach($batchCatOptions as $opt)
                  @php
                    $v = is_object($opt) ? $opt->value : ($opt['value'] ?? '');
                    $t = is_object($opt) ? $opt->text  : ($opt['text'] ?? '');
                  @endphp
                  <option value="{{ $v }}">{{ $t }}</option>
                @endforeach
              @endif
            </select>
          </div>
          <div class="batch-field">
            <label>Move or Copy</label>
            <div class="batch-radios">
              <label>
                <input type="radio" name="batch[move_copy]" value="m" checked />
                {{ Lang::txt('JLIB_HTML_BATCH_MOVE') }}
              </label>
              <label>
                <input type="radio" name="batch[move_copy]" value="c" />
                {{ Lang::txt('JLIB_HTML_BATCH_COPY') }}
              </label>
            </div>
          </div>
        </div>
        <div class="batch-actions">
          <button type="submit" class="btn btn-sm btn-primary"
                  id="btn-batch-submit">
            {{ Lang::txt('JGLOBAL_BATCH_PROCESS') }}
          </button>
          <button type="button" class="btn btn-sm btn-ghost"
                  id="btn-batch-clear">
            {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
          </button>
        </div>
      </div>
    </details>
  @endif

  <input type="hidden" name="original_order_values"
         value="{{ implode(',', $originalOrders) }}" />
</x-admin-form>
