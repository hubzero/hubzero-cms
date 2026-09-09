{{--
 * Hidden form fields partial for setup/edit forms
 *
 * Variables:
 *   $model      - Project model object
 *   $option     - Component option string
 *   $controller - Controller name string
 *   $section    - Active section string
 *   $step       - Current step number
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Html;
@endphp

<input type="hidden" name="option" value="{{ $option }}" />
<input type="hidden" name="alias" value="{{ $model->get('alias') }}" />
<input type="hidden" name="pid" id="pid" value="{{ $model->get('id') }}" />
<input type="hidden" name="controller" value="{{ $controller }}" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="setup" id="insetup" value="{{ $model->inSetup() ? 1 : 0 }}" />
<input type="hidden" name="active" value="{{ $section }}" />
<input type="hidden" name="access" value="{{ $model->get('access') }}" />
<input type="hidden" name="step" id="step" value="{{ $step }}" />
<input
    type="hidden"
    name="gid"
    value="{{ $model->get('owned_by_group') ? $model->get('owned_by_group') : 0 }}"
/>

{!! Html::input('token') !!}
{!! Html::input('honeypot') !!}
