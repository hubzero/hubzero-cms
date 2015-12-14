<!-- Guide page heading -->
<header class="guide-header">
	<h1>Page heading</h1>
</header>
<!-- End guide page heading -->

<!-- Guide container -->
<section class="guide-section container">
	<div class="inner">
		<h2>Basic heading, no options or any auxiliary info</h2>

		<div class="content">
			<p>This heading is the simpliest and probably the most common heading.</p>
		</div>
	</div>
</section>
<!-- End guide container -->

<header class="page-header container">
	<div class="inner">
		<main>
			<h1>Get your facts first, then you can distort them as you please</h1>
		</main>
	</div>
</header>

<!-- Code snippet container -->
<section class="container">
	<div class="inner">
		<code class="html">
			<div class="content">
				<pre class="brush: xml;">
					<?php echo htmlspecialchars(
					'<header class="page-header container">
						<div class="inner">
							<main>
								<h1>Get your facts first, then you can distort them as you please</h1>
							</main>
						</div>
					</header>'
					);?>
				</pre>
			</div>
		</code>
	</div>
</section>
<!-- End code snippet container -->

<!-- Guide container -->
<section class="guide-section container">
	<div class="inner">
		<h2>Basic heading with auxiliary info</h2>

		<div class="content">
			<p>Heading + Some auxiliary info below</p>
		</div>
	</div>
</section>
<!-- End guide container -->

<header class="page-header container">
	<div class="inner">
		<main>
			<h1>Get your facts first, then you can distort them as you please</h1>

			<div class="aux">
				Some auxiliry info, links, etc...
			</div>
		</main>
	</div>
</header>

<section class="container">
	<div class="inner">
		<code class="html">
			<pre class="brush: xml;">
				<?php echo htmlspecialchars(
				'<header class="page-header container">
					<div class="inner">
						<main>
							<h1>Basic heading with auxiliary info</h1>

							<div class="aux">
								Some auxiliry info, links, etc...
							</div>
						</main>
					</div>
				</header>'
				);?>
			</pre>
		</code>
	</div>
</section>


<!-- Guide container -->
<section class="guide-section container">
	<div class="inner">
		<h2>Heading with options</h2>

		<div class="content">
			<p>Heading + options, usually buttons for the global functions</p>
			<p>Note the class name <code class="css">.with-options</code> on the outmost <code>&lt;header&gt;</code> tag. This is required to properly handle all children elenments.</p>
		</div>
	</div>
</section>
<!-- End guide container -->

<header class="page-header with-options container">
	<div class="inner">
		<main>
			<h1>Get your facts first, then you can distort them as you please.</h1>
		</main>

		<aside>
			<div class="content">
				<ul>
					<li><a class="icon-add btn" href="#">Option button</a></li>
				</ul>
			</div>
		</aside>
	</div>
</header>

<section class="container">
	<div class="inner">
		<code class="html">
			<pre class="brush: xml;">
				<?php echo htmlspecialchars(
				'<header class="page-header with-options container">
					<div class="inner">
						<main>
							<h1>Get your facts first, then you can distort them as you please.</h1>
						</main>

						<aside>
							<div class="content">
								<ul>
									<li><a class="icon-add btn" href="#">Option button</a></li>
								</ul>
							</div>
						</aside>
					</div>
				</header>'
				);?>
			</pre>
		</code>
	</div>
</section>

<!-- Guide note -->
<p class="guide-note">Some optional summary info below the buttons:</p>
<!-- End guide note -->

<header class="page-header with-options container">
	<div class="inner">
		<main>
			<h1>Get your facts first, then you can distort them as you please.</h1>
		</main>

		<aside>
			<div class="content">
				<ul>
					<li><a class="icon-add btn" href="#">Option button</a></li>
					<li><a class="icon-add btn" href="#">Another button</a></li>
				</ul>
				<div class="summary">
					<div>Some optional summary info</div>
				</div>
			</div>
		</aside>
	</div>
</header>

<section class="container">
	<div class="inner">
		<code class="html">
			<pre class="brush: xml;">
				<?php echo htmlspecialchars(
						'<header class="page-header with-options container">
					<div class="inner">
						<main>
							<h1>Get your facts first, then you can distort them as you please.</h1>
						</main>

						<aside>
							<div class="content">
								<ul>
									<li><a class="icon-add btn" href="#">Option button</a></li>
									<li><a class="icon-add btn" href="#">Another button</a></li>
								</ul>
								<div class="summary">
									<div>Some optional summary info</div>
								</div>
							</div>
						</aside>
					</div>
				</header>'
				);?>
			</pre>
		</code>
	</div>
</section>

<header class="page-header with-options container">
	<div class="inner">
		<main>
			<h1>Get your facts first, then you can distort them as you please.</h1>
			<div class="aux">
				Some auxiliry info, links, etc...
			</div>
		</main>

		<aside>
			<div class="content">
				<ul>
					<li><a class="icon-add btn" href="#">Option button</a></li>
					<li><a class="icon-add btn" href="#">Another button</a></li>
					<li><a class="icon-add btn" href="#">Yet another button</a></li>
				</ul>
				<div class="summary">
					<div>Some optional summary info</div>
				</div>
			</div>
		</aside>
	</div>
</header>

<section class="container">
	<div class="inner">
		<code class="html">
			<pre class="brush: xml;">
				<?php echo htmlspecialchars(
						'<header class="page-header with-options container">
					<div class="inner">
						<main>
							<h1>Get your facts first, then you can distort them as you please.</h1>
							<div class="aux">
								Some auxiliry info, links, etc...
							</div>
						</main>

						<aside>
							<div class="content">
								<ul>
									<li><a class="icon-add btn" href="#">Option button</a></li>
									<li><a class="icon-add btn" href="#">Another button</a></li>
									<li><a class="icon-add btn" href="#">Yet another button</a></li>
								</ul>
								<div class="summary">
									<div>Some optional summary info</div>
								</div>
							</div>
						</aside>
					</div>
				</header>'
				);?>
			</pre>
		</code>
	</div>
</section>