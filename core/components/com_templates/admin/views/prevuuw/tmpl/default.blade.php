{{--
  com_templates — Template preview (iframe)

  Variables: $id (template element name), $template (template name),
             $url (base URL), $tp (template positions flag), $client (client object)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  Toolbar::title(Lang::txt('COM_TEMPLATES_MANAGER'), 'thememanager');
  Toolbar::custom('edit', 'back.png', 'back_f2.png', 'Back', false, false);

  $formAction  = Route::url('index.php?option=' . $option, false);
  $previewUrl  = $url . 'index.php?tp=' . $tp . '&template=' . $id;
@endphp

<form action="{{ $formAction }}"
      method="post"
      name="adminForm"
      id="adminForm">

  <div class="flex items-center justify-between mb-4 px-1">
    <h3 class="text-base font-semibold">
      {{ Lang::txt('COM_TEMPLATES_SITE_PREVIEW') }}
    </h3>
    <a href="{{ $previewUrl }}" rel="noopener" target="_blank" class="link link-primary text-sm">
      {{ Lang::txt('JBROWSERTARGET_NEW') }}
    </a>
  </div>

  <div class="temprev border border-base-300 rounded-box overflow-hidden">
    <iframe src="{{ $previewUrl }}"
            name="previewframe"
            title="{{ Lang::txt('COM_TEMPLATES_SITE_PREVIEW') }}"
            class="previewframe w-full min-h-[600px] border-0"></iframe>
  </div>

  <input type="hidden" name="id"         value="{{ $id }}" />
  <input type="hidden" name="template"   value="{{ $template }}" />
  <input type="hidden" name="option"     value="{{ $option }}" />
  <input type="hidden" name="task"       value="" />
  <input type="hidden" name="client"     value="{{ $client->id }}" />
  {!! Html::input('token') !!}

</form>
