{{--
  Group Member Options — email notification preferences form.

  Variables from plugin:
    $option              — component option
    $group               — group object
    $recvEmailOptionID   — member option record ID
    $recvEmailOptionValue — current checkbox value (0 or 1)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Component;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $params = Component::params('com_groups');
  $forumCommentEmailNotifications = $params->get('email_forum_comments');
  $atLeastOneOption = (bool) $forumCommentEmailNotifications;

  $formUrl = Route::url(
      'index.php?option=' . $option
      . '&cn=' . $group->get('cn')
      . '&active=memberoptions'
  );
@endphp

<form action="{{ $formUrl }}" method="post" id="memberoptionform">
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="cn" value="{{ $group->get('cn') }}" />
  <input type="hidden" name="action" value="savememberoptions" />
  <input type="hidden" name="memberoptionid" value="{{ $recvEmailOptionID }}" />

  <h3 class="text-lg font-semibold mb-4">{{ Lang::txt('GROUP_MEMBEROPTIONS') }}</h3>

  <p class="text-base-content/70 mb-4">{{ Lang::txt('GROUP_MEMBEROPTIONS_DESC') }}</p>

  @if ($forumCommentEmailNotifications)
    <div class="form-control mb-4">
      <label class="label cursor-pointer justify-start gap-3">
        <input type="checkbox"
               id="recvpostemail"
               name="recvpostemail"
               value="1"
               class="checkbox checkbox-primary"
               @if($recvEmailOptionValue == 1) checked @endif />
        <span class="label-text">{{ Lang::txt('GROUP_RECEIVE_EMAILS_DISCUSSION_POSTS') }}</span>
      </label>
    </div>
  @endif

  @if ($atLeastOneOption)
    <div>
      <button type="submit" class="btn btn-primary btn-sm">
        {{ Lang::txt('Save') }}
      </button>
    </div>
  @else
    <p class="text-base-content/60">{{ Lang::txt('GROUP_MEMBEROPTIONS_NONE') }}</p>
  @endif
</form>
