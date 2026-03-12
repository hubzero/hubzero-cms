{{--
  Resource Plugin — Management wrapper (renders plugin event output)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

@if($html)
  {!! $html !!}
@else
  <x-admin-toolbar
      title="{{ Lang::txt('COM_RESOURCES') }}: {{ Lang::txt('COM_RESOURCES_PLUGINS') }}"
      icon="resources"
      :edit="true"
  />

  <form action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
        method="post"
        name="adminForm"
        id="item-form">

    <div class="alert alert-warning">
      {{ Lang::txt('COM_RESOURCES_ERROR_PLUGIN_NO_INTERFACE') }}
    </div>

    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="action" value="" />

    {!! Html::input('token') !!}
  </form>
@endif
