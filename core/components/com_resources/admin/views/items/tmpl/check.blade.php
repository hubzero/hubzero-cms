{{--
  Resource Audit — Integrity check results

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $__view->css('audit.css');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_RESOURCES') }}: {{ Lang::txt('COM_RESOURCES_AUDIT') }}"
    icon="resources"
/>

<form action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=check', false) }}"
      method="post"
      name="adminForm"
      id="item-form">

  @foreach($tests as $key => $testItem)
    @php
      $passed = $testItem['total'] <= 0 ? 0 : round(($testItem['totals']['passed'] / $testItem['total']) * 100, 2);
      $failed = $testItem['total'] <= 0 ? 0 : round(($testItem['totals']['failed'] / $testItem['total']) * 100, 2);
      $passedWidth = $passed + $failed;

      $baseCheckUrl = 'index.php?option=' . $option
          . '&controller=' . $controller
          . '&task=check&test=' . $key;
    @endphp
    <div class="test mb-6">
      <div class="grid grid-cols-12 gap-4 items-center">
        <div class="col-span-6">
          <h3 class="text-lg font-semibold">
            <span class="test-title">{{ $testItem['name'] }}</span>
            <span class="text-sm font-normal text-muted-foreground ml-2">
              {!! Lang::txt('COM_RESOURCES_NUM_TOTAL', '<strong>' . $testItem['total'] . '</strong>') !!}
            </span>
          </h3>
          <div class="bars mt-2">
            <span class="bar skipped" data-style-width="100%"></span>
            <span class="bar passed" data-style-width="{{ $passedWidth }}%"></span>
            <span class="bar failed" data-style-width="{{ $failed }}%"></span>
          </div>
        </div>
        <div class="col-span-2 text-center">
          <a href="{{ Route::url($baseCheckUrl . '&status=failed', false) }}"
             class="test-value failed badge badge-error">
            {!! Lang::txt('COM_RESOURCES_NUM_FAILED', $testItem['totals']['failed']) !!}
          </a>
        </div>
        <div class="col-span-2 text-center">
          <a href="{{ Route::url($baseCheckUrl . '&status=passed', false) }}"
             class="test-value passed badge badge-success">
            {!! Lang::txt('COM_RESOURCES_NUM_PASSED', $testItem['totals']['passed']) !!}
          </a>
        </div>
        <div class="col-span-2 text-center">
          <a href="{{ Route::url($baseCheckUrl . '&status=skipped', false) }}"
             class="test-value skipped badge badge-ghost">
            {!! Lang::txt('COM_RESOURCES_NUM_SKIPPED', $testItem['totals']['skipped']) !!}
          </a>
        </div>
      </div>

      @if(isset($test) && $test == $key && isset($status) && $status)
        <div class="test-data mt-4">
          <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>{{ Lang::txt('COM_RESOURCES_AUDIT_ID') }}</th>
                  <th>{{ Lang::txt('COM_RESOURCES_AUDIT_PARENT_ID') }}</th>
                  <th>{{ Lang::txt('COM_RESOURCES_AUDIT_ENTRY') }}</th>
                  <th>{{ Lang::txt('COM_RESOURCES_AUDIT_STATUS') }}</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $statusMap = ['failed' => -1, 'skipped' => 0, 'passed' => 1];
                  $statusVal = $statusMap[$status] ?? 0;

                  $results = \Hubzero\Content\Auditor\Result::all()
                      ->whereEquals('scope', 'resource')
                      ->whereEquals('test_id', $test)
                      ->whereEquals('status', $statusVal)
                      ->ordered()
                      ->rows();
                @endphp
                @foreach($results as $result)
                  <tr>
                    <th>{{ $result->get('scope_id') }}</th>
                    <td>
                      @php
                        $parents = \Components\Resources\Models\Association::all()
                            ->whereEquals('child_id', $result->get('scope_id'))
                            ->rows()
                            ->fieldsByKey('parent_id');
                      @endphp
                      @if(!empty($parents))
                        {{ implode(', ', $parents) }}
                      @endif
                    </td>
                    <td>
                      @php
                        $editUrl = Route::url(
                            'index.php?option=com_resources&task=edit&id=' . $result->get('scope_id'), false
                        );
                        if ($notes = $result->get('notes')) {
                            $notes = json_decode($notes);
                            if (isset($notes->field)) {
                                $result->set('title', $notes->field);
                            }
                        }
                      @endphp
                      <a href="{{ $editUrl }}" class="link link-primary">
                        {{ $result->get('title', Lang::txt('COM_RESOURCES_UNKNOWN')) }}
                      </a>
                    </td>
                    <td>
                      <span class="badge badge-sm test-status {{ $result->status() }}">
                        {{ $result->status() }}
                      </span>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endif
    </div>
  @endforeach
</form>
