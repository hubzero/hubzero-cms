{{--
  OAI-PMH — Metadata schemas list

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $canDo = \Components\Oaipmh\Helpers\Permissions::getActions('component');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_OAIPMH_SETTINGS') }}"
    icon="oaipmh"
    :canDo="$canDo"
    option="{{ $option }}"
/>

<form action="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller, false) !!}"
      method="post"
      name="adminForm"
      id="adminForm">

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th>{{ Lang::txt('COM_OAIPMH_SCHEMA_NAME') }}</th>
          <th>{{ Lang::txt('COM_OAIPMH_SCHEMA_PREFIX') }}</th>
          <th>{{ Lang::txt('COM_OAIPMH_SCHEMA_FORMAT') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach($service->getSchemas() as $name)
          @php
            $service->setSchema($name);
            $schema = $service->getSchema();
          @endphp
          <tr>
            <td class="font-medium">{{ $schema->name() }}</td>
            <td>{{ $schema->prefix() }}</td>
            <td><code>&amp;metadataPrefix={{ $schema->prefix() }}</code></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="{{ $task }}" />
</form>
