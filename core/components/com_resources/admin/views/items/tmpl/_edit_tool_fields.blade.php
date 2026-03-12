{{--
  Resource Edit — Tool resource fields partial

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

<div class="alert alert-warning mb-4">
  {{ Lang::txt('COM_RESOURCES_WARNING_TOOLS_USE_PIPELINE') }}
</div>

<div class="admin-field">
  <label for="field-alias" class="label">
    {{ Lang::txt('COM_RESOURCES_FIELD_ALIAS') }}
  </label>
  <input type="text"
         name="fields[alias]"
         id="field-alias"
         class="input input-bordered w-full"
         maxlength="250"
         value="{{ $row->alias }}" />
</div>

<div class="admin-field">
  <label for="attrib-canonical" class="label">
    {{ Lang::txt('COM_RESOURCES_FIELD_CANONICAL') }}
  </label>
  <input type="text"
         name="attrib[canonical]"
         id="attrib-canonical"
         class="input input-bordered w-full"
         maxlength="250"
         value="{{ $row->attribs->get('canonical', '') }}" />
  <p class="text-xs text-muted-foreground mt-1">
    {{ Lang::txt('COM_RESOURCES_FIELD_CANONICAL_HINT') }}
  </p>
</div>

<input type="hidden"
       name="fields[title]"
       id="field-title"
       value="{{ $row->title }}" />
<input type="hidden" name="fields[type]" id="type" value="7" />
