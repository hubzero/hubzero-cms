{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
$__view->css('hubpresenter.css');
@endphp

<div role="alert" class="alert alert-error">
    <h2>Oops, We Encountered an Error.</h2>
    <p>Use the error messages below to try and resolve the issue. If you are still unable to fix the problem report
    your problem to the system administrator by entering a <a href="/support/ticket/new">support ticket.</a></p>
    <ol>
        @foreach ($__view->getErrors() as $error)
            <li>{!! $error !!}</li>
        @endforeach
    </ol>
</div>
