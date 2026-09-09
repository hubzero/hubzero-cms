{{--
  Groups mini display — compact owning group + shared-with groups.

  Variables (from plugin):
    $group     — object|null: owning group
    $aclgroups — array: groups this resource is shared with

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->css();
@endphp

@if($group)
  @php
    $logo = $group->getLogo();
    $groupDesc = e(stripslashes($group->get('description')));
    $groupUrl = Route::url('index.php?option=com_groups&cn=' . $group->get('cn'));
    $groupImgAlt = Lang::txt('PLG_RESOURCES_GROUPS_IMAGE', $groupDesc);
    $groupLink = '<a href="' . $groupUrl . '">' . $groupDesc . '</a>';
    $belongsText = Lang::txt('PLG_RESOURCES_GROUPS_BELONGS_TO_GROUP', $groupLink);
  @endphp
  <div id="group-owner" class="container">
    <h4>Owned by</h4>
    <div class="group-content">
      @if($logo)
        <p class="group-img">
          <a href="{{ $groupUrl }}">
            <img src="{{ $logo }}" width="50" alt="{{ $groupImgAlt }}" />
          </a>
        </p>
        <p class="group-description group-withlogo">{!! $belongsText !!}</p>
      @else
        <p class="group-description">{!! $belongsText !!}</p>
      @endif
    </div>
    <a class="owner" href="{{ $groupUrl }}">{{ $groupDesc }}</a>
  </div>
@endif

@if($aclgroups)
  <div id="group-shared" class="container">
    <h4>Shared with</h4>
    @foreach($aclgroups as $aclGroup)
      @php
        $aclLogo = $aclGroup->getLogo();
        $aclDesc = e(stripslashes($aclGroup->get('description')));
        $aclAlt = Lang::txt('PLG_RESOURCES_GROUPS_IMAGE', $aclDesc);
        $aclUrl = Route::url('index.php?option=com_groups&cn=' . $aclGroup->get('cn'));
      @endphp
      <a href="{{ $aclUrl }}" class="shared-with-group">
        <div class="inner">
          @if($aclLogo)
            <div class="img">
              <img src="{{ $aclLogo }}" alt="{{ $aclAlt }}" />
            </div>
          @endif
          <p class="group-description">{{ e(stripslashes($aclGroup->get('description'))) }}</p>
        </div>
      </a>
    @endforeach
  </div>
@endif
