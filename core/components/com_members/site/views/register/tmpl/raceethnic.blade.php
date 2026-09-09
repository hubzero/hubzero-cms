{{--
 * Racial and ethnic heritage identification info page
 *
 * Variables:
 *   (none — static content)
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    $__view->css('register');
@endphp

<x-page-container title="Racial and Ethnic Heritage Identification">
    <p>
        We are committed to supporting diversity and underrepresented minorities in science education, and this
        information helps us better demonstrate success toward that goal, allowing us to continue providing this
        service to you. Please provide this information if you feel comfortable doing so.
    </p>
    <p><strong>This information will not affect the level of service you receive!</strong></p>
    <p>
        <strong>All users are asked to clarify if they are of Hispanic origin or descent, but only United States
        citizens and Permanent Resident Visa holders need answer the Racial Background section.</strong>
    </p>
    <ul class="list-disc list-inside space-y-3">
        <li>
            <strong>Hispanic or Latino</strong><br />
            Persons of Cuban, Mexican, Puerto Rican, Central or South American, or other Spanish culture of origin,
            regardless of race. Ethnic subgroups are listed here for selection, or you may enter another subgroup if
            your culture is not listed.
        </li>
        <li>
            <strong>American Indian or Alaska Native</strong><br />
            Persons having origins in any of the original peoples of North and South America (including Central
            America), and who maintains cultural identification through tribal affiliation or community recognition.
        </li>
        <li>
            <strong>Asian</strong><br />
            Persons having origins in any of the original peoples of the Far East, Southeast Asia or the Indian
            subcontinent, including for example, Cambodia, China, India, Japan, Korea, Malaysia, Pakistan, the
            Philippine Islands, Thailand, and Vietnam.
        </li>
        <li>
            <strong>Black or African American</strong><br />
            Persons having origins in any of the black racial groups of Africa.
        </li>
        <li>
            <strong>Native Hawaiian or Other Pacific Islander</strong><br />
            Persons having origins in any of the original peoples of Hawaii, Guam, Samoa, or other Pacific Islands.
        </li>
        <li>
            <strong>White</strong><br />
            Persons having origins in any of the original peoples of Europe, the Middle East, or North Africa.
        </li>
        <li>
            <strong>Do not wish to reveal</strong><br />
            You may select this if you would prefer to not disclose this information.
        </li>
    </ul>
</x-page-container>
