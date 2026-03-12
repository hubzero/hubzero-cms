{{--
  SAML — Admin overview

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\User;

  $canDo = User::authorise('core.admin', $option);
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_SAML_TITLE') }}"
    icon="saml"
    option="{{ $option }}"
    :preferences="$canDo"
/>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

  {{-- Overview --}}
  <x-admin-fieldset legend="Overview" body-class="prose prose-sm max-w-none">
      <p>
        SAML (Security Assertion Markup Language) enables single sign-on
        by exchanging authentication data between an Identity Provider (IdP)
        and this site acting as a Service Provider (SP).
      </p>
      <p>
        All settings are managed through component parameters — click the
        <strong>Options</strong> button in the toolbar above.
      </p>
  </x-admin-fieldset>

  {{-- Identity Provider --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_CONFIG_IDP_FIELDSET_LABEL') }}" body-class="prose prose-sm max-w-none">
      <p>Configure the external Identity Provider that authenticates users:</p>
      <ul>
        <li><strong>Entity ID</strong> — unique identifier for the IdP</li>
        <li><strong>SSO URL</strong> — single sign-on endpoint</li>
        <li><strong>SLO URL</strong> — single logout endpoint</li>
        <li><strong>Certificate</strong> — X.509 certificate for signature verification</li>
      </ul>
  </x-admin-fieldset>

  {{-- Service Provider --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_CONFIG_SP_FIELDSET_LABEL') }}" body-class="prose prose-sm max-w-none">
      <p>Configure this site as a SAML Service Provider:</p>
      <ul>
        <li><strong>Entity ID</strong> — unique identifier for this SP</li>
        <li><strong>ACS URL</strong> — assertion consumer service endpoint</li>
        <li><strong>Certificate &amp; Key</strong> — file paths for request signing</li>
      </ul>
  </x-admin-fieldset>

  {{-- Help --}}
  <x-admin-fieldset legend="Resources" body-class="prose prose-sm max-w-none">
      <p>For more information on SAML authentication:</p>
      <ul>
        <li><a href="https://wiki.oasis-open.org/security/FrontPage" target="_blank" rel="noopener">OASIS SAML Specification</a></li>
        <li><a href="https://simplesamlphp.org/docs/stable/" target="_blank" rel="noopener">SimpleSAMLphp Documentation</a></li>
      </ul>
  </x-admin-fieldset>

</div>
