{{--
  Poll Title module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@if ($poll->title)
  {{ stripslashes($poll->title) }}
@else
  {{ stripslashes($params->get('message')) }}
@endif
