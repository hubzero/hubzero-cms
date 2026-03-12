{{--
  OAI-PMH — Admin settings/overview

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $canDo = \Components\Oaipmh\Helpers\Permissions::getActions('component');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_OAIPMH_SETTINGS') }}"
    icon="oaipmh"
    :canDo="$canDo"
    option="{{ $option }}"
/>

<form action="{!! Route::url('index.php?option=' . $option, false) !!}"
      method="post"
      name="adminForm"
      id="item-form">

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Overview --}}
    <x-admin-fieldset legend="Overview" body-class="prose prose-sm max-w-none">
        <p>The Open Archives Initiative Protocol for Metadata Harvesting (OAI-PMH) is a low-barrier mechanism for repository interoperability. Data Providers are repositories that expose structured metadata via OAI-PMH. Service Providers then make OAI-PMH service requests to harvest that metadata.</p>
        <p>OAI-PMH is a set of six verbs or services that are invoked within HTTP.</p>
    </x-admin-fieldset>

    {{-- Data Providers --}}
    <x-admin-fieldset legend="Data Providers" body-class="prose prose-sm max-w-none">
        <p>The OAI-PMH component uses data providers for feeding records to the service. Each provider is represented by a plugin. This allows for easy addition, removal, and configuration of data types without having to modify the core OAIPMH code.</p>
        <p>The list of available plugins can be found and configured by navigating to the <strong>Plugins Manager</strong> and filtering by type <code>oaipmh</code>.</p>
    </x-admin-fieldset>

    {{-- Schemas --}}
    <x-admin-fieldset legend="Schemas" body-class="prose prose-sm max-w-none">
        <p>The OAI-PMH component allows for output in various data schemas. Click the <strong>Schemas</strong> sub-menu item to view the installed schemas and the <code>metadataPrefix</code> used to output data in the respective schema.</p>
    </x-admin-fieldset>

    {{-- Manual Harvesting Guide --}}
    <x-admin-fieldset legend="Manual Harvesting Guide" body-class="prose prose-sm max-w-none">
        <p>Begin with:</p>
        <pre>yourhub.com/oaipmh?</pre>

        <p>Add a verb:</p>
        <pre>verb=GetRecord&amp;identifier=(a unique record ID)
verb=ListSets
verb=Identify
verb=ListMetadataFormats
verb=ListIdentifiers
verb=ListRecords</pre>

        <p>Specify your metadata format (not needed for ListSets, Identify, ListMetadataFormats):</p>
        <pre>&amp;metadataPrefix=oai_dc</pre>

        <p>If using ListIdentifiers or ListRecords, you may specify a date range:</p>
        <pre>&amp;from=YYYY-MM-DD
&amp;until=YYYY-MM-DD</pre>

        <p>To view the next page of results, find the <code>&lt;resumptionToken&gt;</code> at the bottom of the XML and append it:</p>
        <pre>&amp;resumptionToken=XXXXX</pre>

        <p><strong>Example:</strong></p>
        <pre>yourhub.com/oaipmh?verb=ListRecords&amp;metadataPrefix=oai_dc</pre>
    </x-admin-fieldset>

  </div>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="task" value="save" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
</form>
