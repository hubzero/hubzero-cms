{{--
  Badge criteria/validation display.

  Note: The 'image' action is handled in the controller (binary file serving).
  This template only renders the 'criteria' and 'validation' display views.

  Variables from controller (badgeTask):
    $badge  — Section\Badge model instance
    $config — Component params (Registry)
    $action — string: 'criteria' | 'validation'
    $token  — string: validation token (for validation action)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\User;

  $__view->css();

  $title = '';
  $body  = '';

  switch ($action) {
      case 'criteria':
          $title = Lang::txt('COM_COURSES_BADGE_CRITERIA');
          $body  = $badge->get('criteria_text');
          break;

      case 'validation':
          if (!$token) {
              App::abort(404, Lang::txt('COM_COURSES_INVALID_REQUEST'));
          }

          $db = App::get('db');

          $memberBadge = new \Components\Courses\Tables\MemberBadge($db);
          $memberBadge->load(['validation_token' => $token]);

          if (!$memberBadge->get('id')) {
              App::abort(404, Lang::txt('COM_COURSES_INVALID_REQUEST'));
          }

          $memberTbl = new \Components\Courses\Tables\Member($db);
          $memberTbl->loadByMemberId($memberBadge->member_id);
          $userId = $memberTbl->get('user_id');

          $criteria = new \Components\Courses\Tables\SectionBadgeCriteria($db);
          $criteria->load($memberBadge->get('criteria_id'));

          $title = Lang::txt('COM_COURSES_BADGE_VALIDATION');
          $userName   = User::getInstance($userId)->get('name');
          $earnedDate = Date::of($memberBadge->get('earned_on'))->format('M d, Y');
          break;

      default:
          App::abort(404, Lang::txt('COM_COURSES_INVALID_REQUEST'));
          break;
  }
@endphp

<x-page-container :title="$title">
  @if($action === 'criteria')
    <div class="prose max-w-none">
      {!! $body !!}
    </div>
  @elseif($action === 'validation')
    <div class="flex flex-col items-center gap-6 py-8">
      <img src="{{ $badge->get('img_url') }}"
           alt="{{ Lang::txt('COM_COURSES_BADGE_IMAGE') }}"
           class="w-32 h-32 object-contain" />

      <p class="text-lg text-center">
        {{ Lang::txt('COM_COURSES_BADGE_VALIDATION_TEXT', $userName, $earnedDate) }}
      </p>

      @if($criteria->get('text'))
        <div class="prose max-w-none">
          {!! $criteria->get('text') !!}
        </div>
      @endif
    </div>
  @endif
</x-page-container>
