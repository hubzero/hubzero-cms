{{--
  Redirect Links — Batch update form (partial)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
@endphp

<div class="admin-fieldset mt-4">
  <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_REDIRECT_HEADING_UPDATE_LINKS') }}</h3>
  <div class="admin-fieldset-body">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">

      <div class="admin-field">
        <label for="new_url" class="label">{{ Lang::txt('COM_REDIRECT_FIELD_NEW_URL_LABEL') }}</label>
        <input type="text"
               name="new_url"
               id="new_url"
               class="input input-bordered w-full"
               value="" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_REDIRECT_FIELD_NEW_URL_DESC') }}</p>
      </div>

      <div class="admin-field">
        <label for="comment" class="label">{{ Lang::txt('COM_REDIRECT_FIELD_COMMENT_LABEL') }}</label>
        <input type="text"
               name="comment"
               id="comment"
               class="input input-bordered w-full"
               value="" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_REDIRECT_FIELD_COMMENT_DESC') }}</p>
      </div>

      <div class="admin-field">
        <button type="button" id="update-links" class="btn btn-primary">
          {{ Lang::txt('COM_REDIRECT_BUTTON_UPDATE_LINKS') }}
        </button>
      </div>

    </div>
  </div>
</div>
