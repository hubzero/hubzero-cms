{{--
  Courses: Asset Groups — Duplicate assets view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Courses\Helpers\Permissions::getActions();
  $__view->css('duplicateAssets');
  $__view->js('duplicateAssets');
@endphp

@php
  use Hubzero\Facades\Toolbar;

  Toolbar::title(
      Lang::txt('COM_COURSES') . ': Duplicate Assets For Existing Asset Groups',
      'courses'
  );
  Toolbar::cancel();
@endphp

@php
  $formAction = Route::url(
      'index.php?option=' . $option . '&controller=' . $controller, false
  ) . '&amp;task=dupassets';

  $courseId    = $course->get('id');
  $offeringId = $offering->get('id');
  $unitId     = $unit->get('id');

  $courseEditUrl = Route::url(
      'index.php?option=com_courses&controller=courses&task=edit&id=' . $courseId, false
  );
  $coursesUrl = Route::url('index.php?option=com_courses&controller=courses', false);
  $offeringsUrl = Route::url(
      'index.php?option=com_courses&controller=offerings&course=' . $courseId, false
  );
  $unitsUrl = Route::url(
      'index.php?option=com_courses&controller=units&offering=' . $offeringId, false
  );
  $assetGroupsUrl = Route::url(
      'index.php?option=com_courses&controller=assetgroups&unit=' . $unitId, false
  );
@endphp

<form action="{{ $formAction }}"
      method="post"
      name="adminForm"
      id="item-form"
      class="editform">

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
    <div class="lg:col-span-7">
      <x-form-section title="Assets to Duplicate">

        <input type="hidden" name="assetGroupIdToDuplicate" value="{{ $row->get('id') }}" />
        <input type="hidden" name="assetGroupParentIdToDuplicate" value="{{ $row->get('parent') }}" />
        <input type="hidden" name="unitIdToDuplicate" value="{{ $unit->get('id') }}" />
        <input type="hidden" name="offeringIdToDuplicate" value="{{ $offering->get('id') }}" />
        <input type="hidden" name="courseIdToDuplicate" value="{{ $course->get('id') }}" />

        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="controller" value="{{ $controller }}" />
        <input type="hidden" name="task" value="dupassets" />
        <input type="hidden" name="action" value="dupassets" />

        <div id="selectDropdownForAssetGroups" class="space-y-3 mb-4">
          <div>
            <label for="coursesSelect" class="label text-sm font-medium">
              Save to which Asset Group?
            </label>
          </div>
          <select id="coursesSelect"
                  name="filterCourses"
                  class="select select-bordered select-sm w-full">
            <option selected disabled>Select a Course</option>
          </select>
          <label for="offeringsSelect" class="sr-only">Select a Course Offering</label>
          <select id="offeringsSelect"
                  name="filterOfferings"
                  class="select select-bordered select-sm w-full">
            <option selected disabled>Select a Course Offering</option>
          </select>
          <label for="unitsSelect" class="sr-only">Select a Course Unit</label>
          <select id="unitsSelect"
                  name="filterUnits"
                  class="select select-bordered select-sm w-full">
            <option selected disabled>Select a Course Unit</option>
          </select>
          <label for="assetGroupsSelect" class="sr-only">Select a Asset Group</label>
          <select id="assetGroupsSelect"
                  name="filterAssetGroups"
                  class="select select-bordered select-sm w-full">
            <option selected disabled>Select a Asset Group</option>
          </select>
        </div>

        <p class="text-sm mb-4">
          <strong>{{ $assetsCount }}</strong>
          Assets That Will Be Copied from Current Asset Group
          <strong>{{ $row->get('id') }}</strong>
          &rarr; Asset Group Selected ABOVE.
        </p>

        <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Asset Id</th>
                <th>Asset Title</th>
                <th>Type</th>
                <th>State</th>
                <th>Ordering</th>
              </tr>
            </thead>
            <tbody>
              @foreach($assetrows as $assetrow)
                <tr>
                  <td>{{ $assetrow->id }}</td>
                  <td>{{ $assetrow->title }}</td>
                  <td>{{ $assetrow->type }}</td>
                  <td>
                    @if($assetrow->state == 2)
                      <span class="badge badge-sm badge-error">{{ Lang::txt('COM_COURSES_TRASHED') }}</span>
                    @elseif($assetrow->state == 1)
                      <span class="badge badge-sm badge-success">{{ Lang::txt('COM_COURSES_PUBLISHED') }}</span>
                    @else
                      <span class="badge badge-sm badge-ghost">{{ Lang::txt('COM_COURSES_UNPUBLISHED') }}</span>
                    @endif
                  </td>
                  <td>{{ $assetrow->ordering }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="mt-4">
          <button type="submit" class="btn btn-sm btn-primary">
            DUPLICATE ASSETS
          </button>
        </div>
      </x-form-section>
    </div>

    <div class="lg:col-span-5 space-y-4">
      <div class="bg-base-200/50 rounded-box p-4">
        <h3 class="font-bold text-sm mb-2">Course Meta Data</h3>
        <table class="meta-table">
          <tbody>
            <tr>
              <th>
                Course Id
                (<a href="{{ $coursesUrl }}" class="link link-primary text-xs">All</a>)
              </th>
              <td>
                <a href="{{ $courseEditUrl }}" class="link link-primary">{{ $course->get('id') }}</a>
              </td>
            </tr>
            <tr>
              <th>Course Title</th>
              <td>{{ $course->get('title') }}</td>
            </tr>
            <tr>
              <th>Course Alias</th>
              <td>{{ $course->get('alias') }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="bg-base-200/50 rounded-box p-4">
        <h3 class="font-bold text-sm mb-2">Offering Meta Data</h3>
        <table class="meta-table">
          <tbody>
            <tr>
              <th>Offering Id</th>
              <td>
                <a href="{{ $offeringsUrl }}" class="link link-primary">{{ $offering->get('id') }}</a>
              </td>
            </tr>
            <tr>
              <th>Offering Title</th>
              <td>{{ $offering->get('title') }}</td>
            </tr>
            <tr>
              <th>Offering Alias</th>
              <td>{{ $offering->get('alias') }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="bg-base-200/50 rounded-box p-4">
        <h3 class="font-bold text-sm mb-2">Unit Meta Data</h3>
        <table class="meta-table">
          <tbody>
            <tr>
              <th>Unit Id</th>
              <td>
                <a href="{{ $unitsUrl }}" class="link link-primary">{{ $unit->get('id') }}</a>
              </td>
            </tr>
            <tr>
              <th>Unit Title</th>
              <td>{{ $unit->get('title') }}</td>
            </tr>
            <tr>
              <th>Unit Alias</th>
              <td>{{ $unit->get('alias') }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="bg-base-200/50 rounded-box p-4">
        <h3 class="font-bold text-sm mb-2">Asset Group Meta Data</h3>
        <table class="meta-table">
          <tbody>
            <tr>
              <th>Asset Group Id</th>
              <td>
                <a href="{{ $assetGroupsUrl }}" class="link link-primary">{{ $row->get('id') }}</a>
              </td>
            </tr>
            <tr>
              <th>Asset Group Title</th>
              <td>{{ $row->get('title') }}</td>
            </tr>
            <tr>
              <th>Asset Group Parent</th>
              <td>{{ $row->get('parent') }}</td>
            </tr>
            <tr>
              <th>Asset Group Alias</th>
              <td>{{ $row->get('alias') }}</td>
            </tr>
            @if($row->get('created'))
              <tr>
                <th>Created On</th>
                <td>
                  <time datetime="{{ $row->get('created') }}">
                    {{ Date::of($row->get('created'))->toLocal() }}
                  </time>
                </td>
              </tr>
            @endif
            @if($row->get('created_by'))
              <tr>
                <th>Created By</th>
                <td>{{ User::getInstance($row->get('created_by'))->get('name') }}</td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {!! Html::input('token') !!}
</form>
