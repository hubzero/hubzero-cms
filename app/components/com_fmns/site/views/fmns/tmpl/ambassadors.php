<?php
$this->css('ambassadors');
$this->css("https://fonts.googleapis.com/css?family=Martel");
$this->js('ambassadors');

Document::setTitle(Lang::txt('COM_FMNS_AMBASSADORS'));

Pathway::append(
	Lang::txt('COM_FMNS_AMBASSADORS'),
	'index.php?option=' . $this->option . '&controller=' . $this->controller
);
?>

<main class="wrapper">
<header class="ambassador-header">
<div class="intro">
<h2>QUBES Ambassadors</h2>

<p>Are you interested in sharing what you learned in your QUBES FMN with a wider audience? Are you interested in spreading the word about QUBES to new possible participants? Become a QUBES Ambassador!</p>
</div>
</header>

<div class="ambassador">
<p>QUBES Ambassadors are an integral part of promoting professional development opportunities available through QUBES to new audiences. As a QUBES Ambassador, you will present and share information on the Faculty Mentoring Network (FMN) in which you participated. In doing so, you will recruit colleagues to participate in an FMN and/or the QUBES community as a whole, ensure the continued enhancement of their own teaching practices, and share the resources you have created during your FMN.</p>

<p>The QUBES Ambassadors program represents formal recognition of your service within the QUBES community and in addition to receiving funding to assist in extending the reach of QUBES, this award may be listed on your CV or tenure and promotion documentations.</p>
</div>

<div class="participate-wrapper">
<div class="big">&nbsp;</div>

<div class="participate">
<p>There are many ways that you can pursue a QUBES Ambassadorship. Here are a few examples, though these are not the only options.</p>

<ul>
	<li>Present at a discipline-specific conference (ESA, BSA, SVP, ASBMB) mini-grant for registration (up to $250)</li>
	<li>Present at an education conference (NABT, local science teacher conference) mini-grant for registration (up to $250)</li>
	<li>Present a local workshop for colleagues</li>
	<li>Present a local workshop for postdoctoral researchers and graduate students</li>
	<li>Present a local workshop for K-12 teachers</li>
	<li>Help defer costs of publications</li>
</ul>

<p>Have another idea? For more information and with any questions, Contact QUBES!</p>

<p class="center"><span class="helpme link"><a href="/support/">Contact Us</a></span></p>
</div>
</div>

<!-- <div class="apply">

</div> -->
</main>
