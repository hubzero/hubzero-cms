<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$mprefix = Request::getVar('metadataPrefix', 'oai_dc');

echo '<?xml version="1.0" encoding="utf-8"?>';

// Based off of work by Christopher Gutteridge, University of Southampton
?>
<!--

	All the elements really needed for EPrints are done but if
	you want to use this XSL for other OAI archive you may want
	to make some minor changes or additions.

	Not Done
		The 'about' section of 'record'
		The 'compession' part of 'identify'
		The optional attributes of 'resumptionToken'
		The optional 'setDescription' container of 'set'

	All the links just link to oai_dc versions of records.

-->
<xsl:stylesheet
		version="1.0"
		xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
		xmlns:oai="http://www.openarchives.org/OAI/2.0/"
>
<xsl:output method="html"/>
<xsl:template name="style">
	body {
		padding: 0;
		margin: 2em;
		font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
		font-size: 0.9em;
		font-weight: 400;
		line-height: 1.5em;
		color: #444;
		background: #f4f4f4;
		text-rendering: optimizeLegibility;
	}
	table {
		width: 100%;
		font-size: 1em;
		line-height: 1.7em;
	}
	td.value {
		vertical-align: top;
		text-aling: left;
		padding: 0.2em 0.2em 0.2em 0.5em;
		border: 1px solid #f1f1f1;
	}
	th.key,
	td.key {
		width: 20%;
		padding: 0.2em 0.5em 0.2em 0.2em;
		text-align: right;
		white-space: nowrap;
		vertical-align: top;
		font-weight: bold;
		border: 1px solid #f1f1f1;
	}
	.requestInfo {
		background: #e1e1e1;
		margin-bottom: 2em;
	}
	.oaiContainer tr:hover th.key,
	.oaiContainer tr:hover td.key {
		background-color: #ffc;
	}
	.oaiContainer tr:hover td.value {
		background-color: #ffffe0;
	}
	h1, h2, h3 {
		clear: left;
	}
	h1 {
		padding-bottom: 0.2em;
		margin-bottom: 0;
		font-weight: normal;
	}
	h2 {
		margin-bottom: 0.5em;
	}
	h3 {
		margin-bottom: 0.3em;
		font-size: medium;
	}
	a,
	a:visited {
		color: #222;
	}
	a:hover {
		color: #01aed9;
	}
	.oaiContainer {
		background-color: #fff;
		border: 1px solid #e9e9e9;
		margin-bottom: 1.5em;
		padding: 1.5em;
	}
	.oaiContainer .oaiContainer {
		border: none;
		background: transparent;
		margin: 0;
		padding: 0;
	}
	.oaiContainer:hover {
		border-color: #222;
	}
	.oaiContainer h2,
	.oaiRecordTitle {
		font-size: 1.2em;
		font-weight: bold;
		padding: 0;
		margin: 0;
		line-height: 1;
	}
	.oaiRecord {
		margin-top: 1.5em;
		padding-top: 1.5em;
		border-top: 1px solid #e9e9e9;
	}

	.results {
		margin-bottom: 1.5em;
	}
	.quicklinks {
		margin: 1em 0 2em 0;
		padding: 0.2em;
		text-align: left;
		clear: left;
		list-style: none;
		border: 2px solid #ccc;
	}
	.quicklinks li {
		display: inline-block;
		margin: 0;
		padding: 0 0.2em 0 0.4em;
		border-left: 1px solid #ddd;
	}
	.quicklinks li:first-child {
		border-left: none;
	}
	.quicklinks li a,
	.quicklinks li a:visited {
		color: #61c3b9;
	}
	.quicklinks li a:hover {
		color: #ed7c7c;
	}

	.intro {
		background-color: #5cb4e4;
		color: #fff;
		padding: 1em;
		margin: 1em 0 0.5em 0;
		text-align: center;
	}
	.authors {
		font-style: italic;
		margin-bottom: 1.5em;
	}
	.options a {
		margin-right: 1em;
	}

	.relations {
		list-style: none;
		margin: 0;
		padding: 0;
	}
	.relations li {
		margin: 0.2em 0 0 0;
		padding: 0;
	}
	.relations li.ref {
		margin: 1em 0 0 0;
		padding: 1em 0 0 0;
		border-top: 1px solid #f1f1f1;
	}
	.relations li:first-child,
	.relations li.ref:first-child {
		margin-top: 0;
		padding-top: 0;
		border-top: none;
	}
	.keywords { 
		list-style-type: none;
		margin:0;
		padding: 0;
		font-size: 90%;
	}
	.keywords li {
		line-height: 1;
		display: inline-block;
		background-color: #888;
		padding: 0.2em 0.5em;
		color: #fff;
		margin-right: 0.5em;
		-webkit-border-radius: 0.25em;
		-moz-border-radius: 0.25em;
		-ms-border-radius: 0.25em;
		border-radius: 0.25em;
	}
	.errors {
		color: #fff;
		padding: 1.5em;
		background-color: #dd5555;
		border: 2px solid #e84c3d;
		border: 2px solid rgba(0, 0, 0, 0.2);
	}
	.errors h2 {
		margin-top: 0;
		padding-top: 0;
	}
	.errors th.key,
	.errors td.value {
		border-color: #fff;
		border-color: rgba(255, 255, 255, 0.2);
		color: #fff;
		color: rgba(255, 255, 255, 0.8);
	}
	<xsl:call-template name='xmlstyle' />
</xsl:template>

<xsl:variable name='identifier' select="substring-before(concat(substring-after(/oai:OAI-PMH/oai:request,'identifier='),'&amp;'),'&amp;')" />

<xsl:template match="/">
	<html>
		<head>
			<title>OAI Request Results</title>
			<style><xsl:call-template name="style"/></style>
		</head>
		<body>
			<h1>OAI Request Results</h1>

			<xsl:call-template name="quicklinks"/>

			<p class="intro">You are viewing an HTML version of the XML OAI response. To see the underlying XML use your web browsers view source option.</p>

			<xsl:apply-templates select="/oai:OAI-PMH" />
		</body>
	</html>
</xsl:template>

<xsl:template name="quicklinks">
	<ul class="quicklinks">
		<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&verb=Identify&metadataPrefix=' . $mprefix); ?>">Identify</a></li> 
		<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&verb=ListRecords&metadataPrefix=' . $mprefix); ?>">ListRecords</a></li>
		<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&verb=ListSets&metadataPrefix=' . $mprefix); ?>">ListSets</a></li>
		<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&verb=ListMetadataFormats&metadataPrefix=' . $mprefix); ?>">ListMetadataFormats</a></li>
		<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&verb=ListIdentifiers&metadataPrefix=' . $mprefix); ?>">ListIdentifiers</a></li>
	</ul>
</xsl:template>

<xsl:template match="/oai:OAI-PMH">
	<table class="requestInfo">
		<tbody>
			<tr>
				<th class="key">Timestamp</th>
				<td class="value"><xsl:value-of select="oai:responseDate"/></td>
			</tr>
			<tr>
				<th class="key">Request URL</th>
				<td class="value"><xsl:value-of select="oai:request"/></td>
			</tr>
			<tr>
				<th class="key">Request Type</th>
				<td class="value"><xsl:value-of select="oai:request/@verb"/></td>
			</tr>
		</tbody>
	</table>
	<xsl:choose>
		<xsl:when test="oai:error">
			<div class="errors">
				<h2>Error(s)</h2>
				<p>The request could not be completed due to the following error or errors.</p>
				<div class="results">
					<xsl:apply-templates select="oai:error"/>
				</div>
			</div>
		</xsl:when>
		<xsl:otherwise>
			<div class="results">
				<xsl:apply-templates select="oai:Identify" />
				<xsl:apply-templates select="oai:GetRecord"/>
				<xsl:apply-templates select="oai:ListRecords"/>
				<xsl:apply-templates select="oai:ListSets"/>
				<xsl:apply-templates select="oai:ListMetadataFormats"/>
				<xsl:apply-templates select="oai:ListIdentifiers"/>
			</div>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>


<!-- ERROR -->
<xsl:template match="/oai:OAI-PMH/oai:error">
	<table class="values">
		<tbody>
			<tr>
				<th class="key">Error Code</th>
				<td class="value"><xsl:value-of select="@code"/></td>
			</tr>
		</tbody>
	</table>
	<p class="error"><xsl:value-of select="." /></p>
</xsl:template>


<!-- IDENTIFY -->
<xsl:template match="/oai:OAI-PMH/oai:Identify">
	<div class="oaiContainer">
		<table class="values">
			<tr>
				<td class="key">Repository Name</td>
				<td class="value"><xsl:value-of select="oai:repositoryName"/></td>
			</tr>
			<tr>
				<td class="key">Base URL</td>
				<td class="value"><xsl:value-of select="oai:baseURL"/></td>
			</tr>
			<tr>
				<td class="key">Protocol Version</td>
				<td class="value"><xsl:value-of select="oai:protocolVersion"/></td>
			</tr>
			<tr>
				<td class="key">Earliest Datestamp</td>
				<td class="value"><xsl:value-of select="oai:earliestDatestamp"/></td>
			</tr>
			<tr>
				<td class="key">Deleted Record Policy</td>
				<td class="value"><xsl:value-of select="oai:deletedRecord"/></td>
			</tr>
			<tr>
				<td class="key">Granularity</td>
				<td class="value"><xsl:value-of select="oai:granularity"/></td>
			</tr>
			<xsl:apply-templates select="oai:adminEmail"/>
		</table>
		<xsl:apply-templates select="oai:description"/>
	</div>
</xsl:template>

<xsl:template match="/oai:OAI-PMH/oai:Identify/oai:adminEmail">
		<tr>
			<th class="key">Admin Email</th>
			<td class="value"><xsl:value-of select="."/></td>
		</tr>
</xsl:template>


<!-- Identify / Unsupported Description -->
<xsl:template match="oai:description/*" priority="-100">
	<h2>Unsupported Description Type</h2>
	<p>The XSL currently does not support this type of description.</p>
	<div class="xmlSource">
		<xsl:apply-templates select="." mode='xmlMarkup' />
	</div>
</xsl:template>


<!-- Identify / OAI-Identifier -->
<xsl:template match="id:oai-identifier" xmlns:id="http://www.openarchives.org/OAI/2.0/oai-identifier">
	<div class="oaiContainer">
		<h2>OAI-Identifier</h2>
		<table class="values">
			<tbody>
				<tr>
					<th class="key">Scheme</th>
					<td class="value"><xsl:value-of select="id:scheme"/></td>
				</tr>
				<tr>
					<th class="key">Repository Identifier</th>
					<td class="value"><xsl:value-of select="id:repositoryIdentifier"/></td>
				</tr>
				<tr>
					<th class="key">Delimiter</th>
					<td class="value"><xsl:value-of select="id:delimiter"/></td>
				</tr>
				<tr>
					<th class="key">Sample OAI Identifier</th>
					<td class="value"><xsl:value-of select="id:sampleIdentifier"/></td>
				</tr>
			</tbody>
		</table>
	</div>
</xsl:template>


<!-- Identify / EPrints -->
<xsl:template match="ep:eprints" xmlns:ep="http://www.openarchives.org/OAI/1.1/eprints">
	<h2>EPrints Description</h2>
	<xsl:if test="ep:content">
		<h3>Content</h3>
		<xsl:apply-templates select="ep:content"/>
	</xsl:if>
	<xsl:if test="ep:submissionPolicy">
		<h3>Submission Policy</h3>
		<xsl:apply-templates select="ep:submissionPolicy"/>
	</xsl:if>
	<h3>Metadata Policy</h3>
	<xsl:apply-templates select="ep:metadataPolicy"/>
	<h3>Data Policy</h3>
	<xsl:apply-templates select="ep:dataPolicy"/>
	<xsl:apply-templates select="ep:comment"/>
</xsl:template>

<xsl:template match="ep:content|ep:dataPolicy|ep:metadataPolicy|ep:submissionPolicy" xmlns:ep="http://www.openarchives.org/OAI/1.1/eprints">
	<xsl:if test="ep:text">
		<p><xsl:value-of select="ep:text" /></p>
	</xsl:if>
	<xsl:if test="ep:URL">
		<div>
			<a href="{ep:URL}"><xsl:value-of select="ep:URL" /></a>
		</div>
	</xsl:if>
</xsl:template>

<xsl:template match="ep:comment" xmlns:ep="http://www.openarchives.org/OAI/1.1/eprints">
	<h3>Comment</h3>
	<div>
		<xsl:value-of select="."/>
	</div>
</xsl:template>


<!-- Identify / Friends -->
<xsl:template match="fr:friends" xmlns:fr="http://www.openarchives.org/OAI/2.0/friends/">
	<h2>Friends</h2>
	<ul>
		<xsl:apply-templates select="fr:baseURL"/>
	</ul>
</xsl:template>

<xsl:template match="fr:baseURL" xmlns:fr="http://www.openarchives.org/OAI/2.0/friends/">
	<li>
		<xsl:value-of select="."/> 
		<xsl:text> </xsl:text>
		<a class="link" href="{.}?verb=Identify">Identify</a>
	</li>
</xsl:template>


<!-- Identify / Branding -->
<xsl:template match="br:branding" xmlns:br="http://www.openarchives.org/OAI/2.0/branding/">
	<h2>Branding</h2>
	<xsl:apply-templates select="br:collectionIcon"/>
	<xsl:apply-templates select="br:metadataRendering"/>
</xsl:template>

<xsl:template match="br:collectionIcon" xmlns:br="http://www.openarchives.org/OAI/2.0/branding/">
	<h3>Icon</h3>
	<xsl:choose>
		<xsl:when test="link!=''">
			<a href="{br:link}"><img src="{br:url}" alt="{br:title}" width="{br:width}" height="{br:height}" border="0" /></a>
		</xsl:when>
		<xsl:otherwise>
			<img src="{br:url}" alt="{br:title}" width="{br:width}" height="{br:height}" border="0" />
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="br:metadataRendering" xmlns:br="http://www.openarchives.org/OAI/2.0/branding/">
	<h3>Metadata Rendering Rule</h3>
	<table class="values">
		<tbody>
			<tr>
				<th class="key">URL</th>
				<td class="value"><xsl:value-of select="."/></td>
			</tr>
			<tr>
				<th class="key">Namespace</th>
				<td class="value"><xsl:value-of select="@metadataNamespace"/></td>
			</tr>
			<tr>
				<th class="key">Mime Type</th>
				<td class="value"><xsl:value-of select="@mimetype"/></td>
			</tr>
		</tbody>
	</table>
</xsl:template>


<!-- Identify / Gateway -->
<xsl:template match="gw:gateway" xmlns:gw="http://www.openarchives.org/OAI/2.0/gateway/x">
	<h2>Gateway Information</h2>
	<table class="values">
		<tbody>
			<tr>
				<th class="key">Source</th>
				<td class="value"><xsl:value-of select="gw:source"/></td>
			</tr>
			<tr>
				<th class="key">Description</th>
				<td class="value"><xsl:value-of select="gw:gatewayDescription"/></td>
			</tr>
			<xsl:apply-templates select="gw:gatewayAdmin"/>
			<xsl:if test="gw:gatewayURL">
				<tr>
					<th class="key">URL</th>
					<td class="value"><xsl:value-of select="gw:gatewayURL"/></td>
				</tr>
			</xsl:if>
			<xsl:if test="gw:gatewayNotes">
				<tr>
					<th class="key">Notes</th>
					<td class="value"><xsl:value-of select="gw:gatewayNotes"/></td>
				</tr>
			</xsl:if>
		</tbody>
	</table>
</xsl:template>

<xsl:template match="gw:gatewayAdmin" xmlns:gw="http://www.openarchives.org/OAI/2.0/gateway/">
	<tr>
		<th class="key">Admin</th>
		<td class="value"><xsl:value-of select="."/></td>
	</tr>
</xsl:template>


<!-- GetRecord -->
<xsl:template match="oai:GetRecord">
	<xsl:apply-templates select="oai:record" />
</xsl:template>

<!-- ListRecords -->
<xsl:template match="oai:ListRecords">
	<xsl:apply-templates select="oai:record" />
	<xsl:apply-templates select="oai:resumptionToken" />
</xsl:template>

<!-- ListIdentifiers -->
<xsl:template match="oai:ListIdentifiers">
	<xsl:apply-templates select="oai:header" />
	<xsl:apply-templates select="oai:resumptionToken" />
</xsl:template>

<!-- ListSets -->
<xsl:template match="oai:ListSets">
	<xsl:apply-templates select="oai:set" />
	<xsl:apply-templates select="oai:resumptionToken" />
</xsl:template>

<xsl:template match="oai:set">
	<div class="oaiContainer oaiSet">
		<!-- <h2>Set</h2> -->
		<table class="values">
			<tbody>
				<tr>
					<th class="key">Name</th>
					<td class="value"><xsl:value-of select="oai:setName"/></td>
				</tr>
				<xsl:apply-templates select="oai:setSpec" />
			</tbody>
		</table>
	</div>
</xsl:template>

<!-- ListMetadataFormats -->

<xsl:template match="oai:ListMetadataFormats">
	<xsl:choose>
		<xsl:when test="$identifier">
			<p>This is a list of metadata formats available for the record "<xsl:value-of select='$identifier' />". Use these links to view the metadata: <xsl:apply-templates select="oai:metadataFormat/oai:metadataPrefix" /></p>
		</xsl:when>
		<xsl:otherwise>
			<p>This is a list of metadata formats available from this archive.</p>
		</xsl:otherwise>
	</xsl:choose>
	<xsl:apply-templates select="oai:metadataFormat" />
</xsl:template>

<xsl:template match="oai:metadataFormat">
	<div class="oaiContainer">
		<!-- <h2>Metadata Format</h2> -->
		<table class="values">
			<tbody>
				<tr>
					<td class="key">metadataPrefix</td>
					<td class="value"><a class="link" href="<?php echo Route::url('index.php?option=' . $this->option . '&verb=ListRecords&metadataPrefix={oai:metadataPrefix}'); ?>"><xsl:value-of select="oai:metadataPrefix"/></a></td>
				</tr>
				<tr>
					<td class="key">metadataNamespace</td>
					<td class="value"><xsl:value-of select="oai:metadataNamespace"/></td>
				</tr>
				<tr>
					<td class="key">schema</td>
					<td class="value"><a href="{oai:schema}"><xsl:value-of select="oai:schema"/></a></td>
				</tr>
			</tbody>
		</table>
	</div>
</xsl:template>

<xsl:template match="oai:metadataPrefix">
			<xsl:text> </xsl:text><a class="link" href="<?php echo Route::url('index.php?option=' . $this->option . '&verb=GetRecord&metadataPrefix={.}&identifier={$identifier}'); ?>"><xsl:value-of select='.' /></a>
</xsl:template>

<!-- record object -->

<xsl:template match="oai:record" xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/" xmlns:oai_qdc="http://worldcat.org/xmlschemas/qdc-1.0/" xmlns:dc="http://purl.org/dc/elements/1.1/">
	<div class="oaiContainer">
		<xsl:choose>
			<xsl:when test="oai:metadata">
				<h2 class="oaiRecordTitle"><xsl:value-of select="oai:metadata/oai_dc:dc/dc:title"/><xsl:value-of select="oai:metadata/oai_qdc:qualifieddc/dc:title"/></h2>
				<div class="oaiRecord">
					<xsl:apply-templates select="oai:metadata" />
					<xsl:apply-templates select="oai:about" />
				</div>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="oai:header" />
			</xsl:otherwise>
		</xsl:choose>
	</div>
</xsl:template>


<xsl:template match="oai:header">
	<div class="oaiContainer">
	<table class="values">
		<tbody>
			<tr>
				<th class="key">OAI Identifier</th>
				<td class="value">
					<a class="link" href="<?php echo Route::url('index.php?option=' . $this->option . '&verb=GetRecord&metadataPrefix=&metadataPrefix=' . $mprefix . '&identifier={oai:identifier}'); ?>"><xsl:value-of select="oai:identifier"/></a>
					<!--<span class="options">
						<xsl:text> </xsl:text><a class="link" href="<?php echo Route::url('index.php?option=' . $this->option . '&verb=GetRecord&metadataPrefix=&metadataPrefix=' . $mprefix . '&identifier={oai:identifier}'); ?>">oai_dc</a>
						<xsl:text> </xsl:text><a class="link" href="<?php echo Route::url('index.php?option=' . $this->option . '&verb=ListMetadataFormats&identifier={oai:identifier}'); ?>">formats</a>
					</span>-->
				</td>
			</tr>
			<tr>
				<th class="key">Datestamp</th>
				<td class="value"><xsl:value-of select="oai:datestamp"/></td>
			</tr>
			<xsl:apply-templates select="oai:setSpec"/>
		</tbody>
	</table>
	</div>
	<xsl:if test="@status='deleted'">
		<p>This record has been deleted.</p>
	</xsl:if>
</xsl:template>


<xsl:template match="oai:about">
	<p>"about" part of record container not supported by the XSL</p>
</xsl:template>

<xsl:template match="oai:metadata">
	<div class="metadata">
		<xsl:apply-templates select="*" />
	</div>
</xsl:template>


<!-- oai setSpec object -->
<xsl:template match="oai:setSpec">
	<tr>
		<th class="key">Spec</th>
		<td class="value">
			<!--<xsl:value-of select="."/>-->
			<span class="options">
				<xsl:text> </xsl:text><a class="link" href="<?php echo Route::url('index.php?option=' . $this->option . '&verb=ListIdentifiers&metadataPrefix=' . $mprefix . '&set={.}'); ?>">Identifiers</a>
				<xsl:text> </xsl:text><a class="link" href="<?php echo Route::url('index.php?option=' . $this->option . '&verb=ListRecords&metadataPrefix=' . $mprefix . '&set={.}'); ?>">Records</a>
			</span>
		</td>
	</tr>
</xsl:template>


<!-- oai resumptionToken -->
<xsl:template match="oai:resumptionToken">
	<p>There are more results.</p>
	<table class="values">
		<tbody>
			<tr>
				<th class="key">resumptionToken:</th>
				<td class="value">
					<xsl:value-of select="."/>
					<xsl:text> </xsl:text>
					<a class="link" href="<?php echo Route::url('index.php?option=' . $this->option . '&verb={/oai:OAI-PMH/oai:request/@verb}&metadataPrefix=' . $mprefix . '&resumptionToken={.}'); ?>">Resume</a>
				</td>
			</tr>
		</tbody>
	</table>
</xsl:template>

<!-- unknown metadata format -->
<xsl:template match="oai:metadata/*" priority='-100'>
	<h3>Unknown Metadata Format</h3>
	<div class="xmlSource">
		<xsl:apply-templates select="." mode='xmlMarkup' />
	</div>
</xsl:template>

<!-- oai_dc record -->
<xsl:template match="oai_dc:dc" xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/" xmlns:dc="http://purl.org/dc/elements/1.1/" >
	<div class="dcdata">
		<xsl:if test="count(dc:creator) &gt; 0">
			<div class="authors">
				By <xsl:apply-templates select="dc:creator" />
			</div>
		</xsl:if>
		<table>
			<tbody>
				<xsl:apply-templates select="*[not(self::dc:creator or self::dc:subject or self::dc:title or self::dc:relation)]" />
				<xsl:if test="count(dc:relation) &gt; 0">
					<tr>
						<th class="key">Related</th>
						<td class="value">
							<ul class="relations"><xsl:apply-templates select="dc:relation"/></ul>
						</td>
					</tr>
				</xsl:if>
			</tbody>
		</table>
	</div>
</xsl:template>

<!-- oai_qdc record -->
<xsl:template match="oai_qdc:qualifieddc" xmlns:oai_qdc="http://worldcat.org/xmlschemas/qdc-1.0/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dc="http://purl.org/dc/elements/1.1/">
	<div class="dcdata">
		<xsl:if test="count(dc:creator) &gt; 0">
			<div class="authors">
				By <xsl:apply-templates select="dc:creator" />
			</div>
		</xsl:if>
		<table>
			<tbody>
				<xsl:apply-templates select="*[not(self::dc:creator or self::dc:subject or self::dc:title or self::dc:relation or self::dcterms:hasPart or self::dcterms:isPartOf or self::dcterms:hasVersion or self::dcterms:references or self::dcterms:isReferencedBy)]" />
				<xsl:if test="count(dc:relation) &gt; 0">
					<tr>
						<th class="key">Related</th>
						<td class="value">
							<ul class="relations"><xsl:apply-templates select="dc:relation"/></ul>
						</td>
					</tr>
				</xsl:if>
				<xsl:if test="count(dcterms:hasPart) &gt; 0">
					<tr>
						<th class="key">Parts</th>
						<td class="value">
							<ul class="relations"><xsl:apply-templates select="dcterms:hasPart"/></ul>
						</td>
					</tr>
				</xsl:if>
				<xsl:if test="count(dcterms:isPartOf) &gt; 0">
					<tr>
						<th class="key">Is Part Of</th>
						<td class="value">
							<ul class="relations"><xsl:apply-templates select="dcterms:isPartOf"/></ul>
						</td>
					</tr>
				</xsl:if>
				<xsl:if test="count(dcterms:hasVersion) &gt; 0">
					<tr>
						<th class="key">Versions</th>
						<td class="value">
							<ul class="relations"><xsl:apply-templates select="dcterms:hasVersion"/></ul>
						</td>
					</tr>
				</xsl:if>
				<xsl:if test="count(dcterms:references) &gt; 0">
					<tr>
						<th class="key">References</th>
						<td class="value">
							<ul class="relations"><xsl:apply-templates select="dcterms:references"/></ul>
						</td>
					</tr>
				</xsl:if>
				<xsl:if test="count(dcterms:isReferencedBy) &gt; 0">
					<tr>
						<th class="key">Is Referenced By</th>
						<td class="value">
							<ul class="relations"><xsl:apply-templates select="dcterms:isReferencedBy"/></ul>
						</td>
					</tr>
				</xsl:if>
				<xsl:if test="count(dc:subject) &gt; 0">
					<tr>
						<th class="key">Keywords</th>
						<td class="value"><ul class="keywords"><xsl:apply-templates select="dc:subject"/></ul></td>
					</tr>
				</xsl:if>
			</tbody>
		</table>
	</div>
</xsl:template>

<xsl:template match="dc:relation" xmlns:dc="http://purl.org/dc/elements/1.1/">
	<tr>
		<th class="key">Content</th>
		<td class="value">
			<xsl:choose>
				<xsl:when test='starts-with(.,"http" )'>
					<xsl:choose>
						<xsl:when test='string-length(.) &gt; 150'>
							<a class="link"><xsl:attribute name="href"><xsl:value-of select="."/></xsl:attribute>URL (hidden due to length)</a>
						</xsl:when>
						<xsl:otherwise>
							<a class="link"><xsl:attribute name="href"><xsl:value-of select="."/></xsl:attribute><xsl:value-of select="."/></a>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="."/>
				</xsl:otherwise>
			</xsl:choose>
		</td>
	</tr>
</xsl:template>

<xsl:template match="dc:title" xmlns:dc="http://purl.org/dc/elements/1.1/">
	<tr>
		<th class="key">Title</th>
		<td class="value"><xsl:value-of select="."/></td>
	</tr>
</xsl:template>

<xsl:template match="dc:creator" xmlns:dc="http://purl.org/dc/elements/1.1/">
	<span><xsl:value-of select="." /><xsl:if test="position() &lt; last()">, </xsl:if></span>
</xsl:template>

<xsl:template match="dc:subject" xmlns:dc="http://purl.org/dc/elements/1.1/">
	<li><xsl:value-of select="."/></li>
</xsl:template>

<xsl:template match="dc:relation" xmlns:dc="http://purl.org/dc/elements/1.1/">
	<li><a class="link"><xsl:attribute name="href"><xsl:value-of select="."/></xsl:attribute><xsl:value-of select="."/></a></li>
</xsl:template>

<xsl:template match="dcterms:hasVersion" xmlns:dcterms="http://purl.org/dc/terms/">
	<li><a class="link" href="{.}"><xsl:value-of select="."/></a></li>
</xsl:template>

<xsl:template match="dcterms:hasPart" xmlns:dcterms="http://purl.org/dc/terms/">
	<li><a class="link" href="{.}"><xsl:value-of select="."/></a></li>
</xsl:template>

<xsl:template match="dcterms:isPartOf" xmlns:dcterms="http://purl.org/dc/terms/">
	<li><a class="link" href="{.}"><xsl:value-of select="."/></a></li>
</xsl:template>

<xsl:template match="dcterms:references" xmlns:dcterms="http://purl.org/dc/terms/">
	<li class="ref"><xsl:value-of select="."/></li>
</xsl:template>

<xsl:template match="dcterms:isReferencedBy" xmlns:dcterms="http://purl.org/dc/terms/">
	<li class="ref"><xsl:value-of select="."/></li>
</xsl:template>

<xsl:template match="dc:description" xmlns:dc="http://purl.org/dc/elements/1.1/">
	<tr>
		<th class="key">Description</th>
		<td class="value"><xsl:value-of select="."/></td>
	</tr>
</xsl:template>

<xsl:template match="dc:publisher" xmlns:dc="http://purl.org/dc/elements/1.1/">
	<tr>
		<th class="key">Publisher</th>
		<td class="value"><xsl:value-of select="."/></td>
	</tr>
</xsl:template>

<xsl:template match="dc:contributor" xmlns:dc="http://purl.org/dc/elements/1.1/">
	<tr>
		<th class="key">Other Contributor</th>
		<td class="value"><xsl:value-of select="."/></td>
	</tr>
</xsl:template>

<xsl:template match="dc:date" xmlns:dc="http://purl.org/dc/elements/1.1/">
	<tr>
		<th class="key">Date</th>
		<td class="value"><xsl:value-of select="."/></td>
	</tr>
</xsl:template>

<xsl:template match="dc:type" xmlns:dc="http://purl.org/dc/elements/1.1/">
	<tr>
		<th class="key">Resource Type</th>
		<td class="value"><xsl:value-of select="."/></td>
	</tr>
</xsl:template>

<xsl:template match="dc:format" xmlns:dc="http://purl.org/dc/elements/1.1/">
	<tr>
		<th class="key">Format</th>
		<td class="value"><xsl:value-of select="."/></td>
	</tr>
</xsl:template>

<xsl:template match="dc:identifier" xmlns:dc="http://purl.org/dc/elements/1.1/">
	<tr>
		<th class="key">Resource Identifier</th>
		<td class="value"><a><xsl:attribute name="href"><xsl:value-of select="."/></xsl:attribute><xsl:value-of select="."/></a></td>
	</tr>
</xsl:template>

<xsl:template match="dc:source" xmlns:dc="http://purl.org/dc/elements/1.1/">
	<tr>
		<th class="key">Source</th>
		<td class="value"><xsl:value-of select="."/></td>
	</tr>
</xsl:template>

<xsl:template match="dc:language" xmlns:dc="http://purl.org/dc/elements/1.1/">
	<tr>
		<th class="key">Language</th>
		<td class="value"><xsl:value-of select="."/></td>
	</tr>
</xsl:template>

<xsl:template match="dc:coverage" xmlns:dc="http://purl.org/dc/elements/1.1/">
	<tr>
		<th class="key">Coverage</th>
		<td class="value"><xsl:value-of select="."/></td>
	</tr>
</xsl:template>

<xsl:template match="dc:rights" xmlns:dc="http://purl.org/dc/elements/1.1/">
	<tr>
		<th class="key">Rights Management</th>
		<td class="value"><xsl:value-of select="."/></td>
	</tr>
</xsl:template>

<!-- XML Pretty Maker -->
<xsl:template match="node()" mode='xmlMarkup'>
	<div class="xmlBlock">
		&lt;<span class="xmlTagName"><xsl:value-of select='name(.)' /></span><xsl:apply-templates select="@*" mode='xmlMarkup'/>&gt;<xsl:apply-templates select="node()" mode='xmlMarkup' />&lt;/<span class="xmlTagName"><xsl:value-of select='name(.)' /></span>&gt;
	</div>
</xsl:template>

<xsl:template match="text()" mode='xmlMarkup'><span class="xmlText"><xsl:value-of select='.' /></span></xsl:template>

<xsl:template match="@*" mode='xmlMarkup'>
	<xsl:text> </xsl:text><span class="xmlAttrName"><xsl:value-of select='name()' /></span>="<span class="xmlAttrValue"><xsl:value-of select='.' /></span>"
</xsl:template>

<xsl:template name="xmlstyle">
.xmlSource {
	font-size: 70%;
	border: solid #c0c0a0 1px;
	background-color: #ffffe0;
	padding: 2em 2em 2em 0em;
}
.xmlBlock {
	padding-left: 2em;
}
.xmlTagName {
	color: #800000;
	font-weight: bold;
}
.xmlAttrName {
	font-weight: bold;
}
.xmlAttrValue {
	color: #0000c0;
}
</xsl:template>

</xsl:stylesheet>
