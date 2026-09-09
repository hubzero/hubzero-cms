{{--
  Tool pipeline finalize — final review page before tool approval.

  Variables from controller:
    $title      — page title
    $option     — component option string
    $controller — controller name
    $status     — associative array of tool status fields

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->css('pipeline.css');
  $__view->js('pipeline.js');

  // Access text lookups
  $toolaccess = \Components\Tools\Helpers\Html::getToolAccess(
      $status['exec'],
      $status['membergroups']
  );
  $codeaccess = \Components\Tools\Helpers\Html::getCodeAccess($status['code']);
  $wikiaccess = \Components\Tools\Helpers\Html::getWikiAccess($status['wiki']);

  // URLs
  $statusUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=status&app=' . $status['toolname']
  );
  $newUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=create'
  );
  $versionFormAction = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=finalizeversion&app=' . $status['toolname']
  );
  $editUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=edit&app=' . $status['toolname']
  );
  $versionsUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=versions&action=confirm&app=' . $status['toolname']
  );
  $previewUrl = Route::url(
      'index.php?option=com_resources&alias='
      . $status['toolname'] . '&rev=dev'
  );
  $licEditUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=license&app=' . $status['toolname']
      . '&action=confirm'
  );

  $approvedNum = \Components\Tools\Helpers\Html::getStatusNum('Approved');
  $devTeam = \Components\Tools\Helpers\Html::getDevTeam($status['developers']);
  $authors = \Components\Tools\Helpers\Html::getDevTeam($status['authors']);
@endphp

<x-page-container>
  <header id="content-header">
    <h2>{{ $__view->escape($title) }}</h2>

    <div id="content-header-extra">
      <ul id="useroptions" class="flex gap-2">
        <li>
          <a class="btn btn-sm btn-outline" href="{{ $statusUrl }}">
            {{ Lang::txt('COM_TOOLS_TOOL_STATUS') }}
          </a>
        </li>
        <li>
          <a class="btn btn-sm btn-outline" href="{{ $newUrl }}">
            {{ Lang::txt('COM_TOOLS_CONTRIBTOOL_NEW_TOOL') }}
          </a>
        </li>
      </ul>
    </div>
  </header>

  <section class="main section">
    {!! \Components\Tools\Helpers\Html::writeApproval('Approve') !!}

    @if ($__view->getError())
      <div class="alert alert-error">
        {!! implode('<br />', $__view->getErrors()) !!}
      </div>
    @endif

    <h4>{{ Lang::txt('COM_TOOLS_CONTRIBTOOL_FINAL_REVIEW') }}:</h4>

    <form action="{{ $versionFormAction }}" method="post" id="versionForm" name="versionForm">
      <input type="hidden" name="option" value="{{ $option }}" />
      <input type="hidden" name="controller" value="{{ $controller }}" />
      <input type="hidden" name="task" value="finalizeversion" />
      <input type="hidden" name="newstate" value="{{ $approvedNum }}" />
      <input type="hidden" name="id" value="{{ $status['toolid'] }}" />
      <input type="hidden" name="app" value="{{ $status['toolname'] }}" />
      {!! Html::input('token') !!}

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Tool Information --}}
        <div class="card bg-base-100 shadow-sm">
          <div class="card-body">
            <div class="card-title flex justify-between items-center">
              <span>{{ Lang::txt('COM_TOOLS_TOOL_INFO') }}</span>
              <a class="btn btn-xs btn-ghost" href="{{ $editUrl }}">
                {{ Lang::txt('COM_TOOLS_EDIT') }}
              </a>
            </div>

            <dl class="divide-y divide-base-200">
              <div class="flex gap-2 py-2">
                <dt class="font-semibold min-w-[8rem]">{{ Lang::txt('COM_TOOLS_TITLE') }}:</dt>
                <dd>{{ $__view->escape(stripslashes($status['title'])) }}</dd>
              </div>
              <div class="flex gap-2 py-2">
                <dt class="font-semibold min-w-[8rem]">{{ Lang::txt('COM_TOOLS_VERSION') }}:</dt>
                <dd>
                  {{ $__view->escape(stripslashes($status['version'])) }}
                  <a class="link link-primary text-sm ml-2" href="{{ $versionsUrl }}">
                    [{{ Lang::txt('COM_TOOLS_EDIT') }}]
                  </a>
                </dd>
              </div>
              <div class="flex gap-2 py-2">
                <dt class="font-semibold min-w-[8rem]">{{ Lang::txt('COM_TOOLS_DESCRIPTION') }}:</dt>
                <dd>{{ $__view->escape(stripslashes($status['description'])) }}</dd>
              </div>
              <div class="flex gap-2 py-2">
                <dt class="font-semibold min-w-[8rem]">{{ Lang::txt('COM_TOOLS_TOOL_ACCESS') }}:</dt>
                <dd>{{ $toolaccess }}</dd>
              </div>
              <div class="flex gap-2 py-2">
                <dt class="font-semibold min-w-[8rem]">{{ Lang::txt('COM_TOOLS_SOURCE_CODE') }}:</dt>
                <dd>{{ $codeaccess }}</dd>
              </div>
              <div class="flex gap-2 py-2">
                <dt class="font-semibold min-w-[8rem]">{{ Lang::txt('COM_TOOLS_WIKI_ACCESS') }}:</dt>
                <dd>{{ $wikiaccess }}</dd>
              </div>
              <div class="flex gap-2 py-2">
                <dt class="font-semibold min-w-[8rem]">{{ Lang::txt('COM_TOOLS_SCREEN_SIZE') }}:</dt>
                <dd>{{ $status['vncGeometry'] }}</dd>
              </div>
              <div class="flex gap-2 py-2">
                <dt class="font-semibold min-w-[8rem]">{{ Lang::txt('COM_TOOLS_DEVELOPERS') }}:</dt>
                <dd>{!! $devTeam !!}</dd>
              </div>
              <div class="flex gap-2 py-2">
                <dt class="font-semibold min-w-[8rem]">{{ Lang::txt('COM_TOOLS_AUTHORS') }}:</dt>
                <dd>{!! $authors !!}</dd>
              </div>
            </dl>

            <div class="mt-4">
              <a class="link link-primary" href="{{ $previewUrl }}">
                {{ Lang::txt('COM_TOOLS_PREVIEW_RES_PAGE') }}
              </a>
            </div>
          </div>
        </div>

        {{-- License --}}
        @if ($status['license'])
          <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
              <div class="card-title flex justify-between items-center">
                <span>{{ Lang::txt('COM_TOOLS_TOOL_LICENSE') }}</span>
                <a class="btn btn-xs btn-ghost" href="{{ $licEditUrl }}">
                  {{ Lang::txt('COM_TOOLS_EDIT') }}
                </a>
              </div>

              <pre class="bg-base-200 p-4 rounded-lg text-sm whitespace-pre-wrap overflow-x-auto max-h-96">{{ $__view->escape(stripslashes($status['license'])) }}</pre>
            </div>
          </div>
        @endif
      </div>

      <div class="mt-6">
        <button type="submit" class="btn btn-primary">
          {{ Lang::txt('COM_TOOLS_APPROVE_THIS_TOOL') }}
        </button>
      </div>
    </form>
  </section>
</x-page-container>
