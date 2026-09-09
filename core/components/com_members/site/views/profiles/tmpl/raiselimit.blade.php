{{--
  Resource limit increase request form. Allows users to request
  higher limits for sessions, storage, or meetings, or select
  which resource to increase.

  Variables from controller:
    $title         — string  Page title
    $resource      — string  Resource type (sessions|storage|meetings|select)
    $profile       — object  User profile object
    $authorized    — string  Authorization level (e.g. 'admin')
    $jobs_allowed  — int     Current max concurrent sessions
    $quota         — string  Current storage quota
    $max_meetings  — int     Current max online meetings
    $submit_button — string  Label for the submit/increase button

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Route;

    $__view->css();

    $formAction = Route::url($profile->link() . '&task=raiselimit');
    $feedbackUrl = Route::url('index.php?option=com_feedback');
    $pageTitle = $title . ': ' . ucfirst($resource);
@endphp

<x-page-container :title="$pageTitle">
    @slot('sidebar')
        <x-sidebar-card>
            <p class="info">
                When you have time, please leave some
                <a href="{{ $feedbackUrl }}">feedback</a>.
                We would like to know a little more about how you are
                using the site so that we can make improvements for everyone.
            </p>
        </x-sidebar-card>
    @endslot

    <form action="{{ $formAction }}" method="post" name="hubForm" id="hubForm">
        <fieldset>
        @if ($resource != 'select')
            <p>
                Please provide a short reason why you would like this increase in resources. Your
                request for additional resources will then be e-mailed to the site administrators
                who will grant your request or provide a reason why we are unable to meet your
                request at this time.
            </p>
            <div class="form-group">
                <label for="request">
                    Reason for Increase:
                </label>
                <textarea
                    name="request"
                    id="request"
                    rows="6"
                    cols="32"
                    class="textarea textarea-bordered w-full"></textarea>
            </div>
        </fieldset>

        <p class="submit">
            <button type="submit" name="raiselimit[{{ $resource }}]" class="btn btn-primary">
                Submit Request
            </button>
        </p>
        @else
            <h3>Hubzero Resources</h3>

            <table class="table table-zebra w-full">
                <tbody>
                    @if ($authorized == 'admin')
                        <tr>
                            <th>User Login:</th>
                            <td colspan="2">
                                <a href="{{ Route::url($profile->link()) }}">{{ e($profile->get('username')) }}</a>
                                <input
                                    name="login"
                                    id="login"
                                    type="hidden"
                                    value="{{ e($profile->get('username')) }}" />
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <th>Maximum Concurrent Sessions:</th>
                        <td>{{ $jobs_allowed }}</td>
                        <td>
                            <button type="submit" name="raiselimit[sessions]" id="raiselimitsessions" class="btn btn-primary btn-sm">
                                {{ $submit_button }}
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <th>Online Disk Storage Limit:</th>
                        <td>{{ $quota }}</td>
                        <td>
                            <button type="submit" name="raiselimit[storage]" id="raiselimitstorage" class="btn btn-primary btn-sm">
                                {{ $submit_button }}
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <th>Maximum Online Meetings:</th>
                        <td>{{ $max_meetings }}</td>
                        <td>
                            <button type="submit" name="raiselimit[meetings]" id="raiselimitmeetings" class="btn btn-primary btn-sm">
                                {{ $submit_button }}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="help">
                <h4>How do I get more resources?</h4>
                <p>
                    Click "Increase" for the resource you wish to request more. Depending on the resource and your
                    current limits, you will either be automatically granted more resources, asked to fill out some
                    feedback, asked to review a resource for others, or asked to email support.
                </p>
            </div>
        </fieldset>
        @endif
    </form>
</x-page-container>
