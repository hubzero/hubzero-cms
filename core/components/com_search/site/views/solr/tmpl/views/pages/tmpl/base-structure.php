<!-- Guide page heading -->
<header class="guide-header">
	<h1>Base structure</h1>
</header>
<!-- End guide page heading -->

<!-- Guide container -->
<section class="guide-section container">
	<div class="inner">
		<div class="content">
			<p>Each component needs a global wrapper. Thinking about some kind of global namespacing, like <code class="css">.hub-component</code>.</p>

			<p>For easy styling and greater flexibility each logical part of the component should be wrapped in the tag with a <code class="css">.container</code> class name (beginning with the <a href="main-heading" >main heading</a> where applicable) wrapping the element with an <code class="css">.inner</code> class name wrapping the element with a <code class="css">.content</code> class name.</p>
		</div>
	</div>
</section>

<!-- Guide note -->
<p class="guide-note">The general building block structure is represented as follows:</p>
<!-- End guide note -->

<!-- Code snippet container -->
<section class="container">
	<div class="inner">
		<code class="html">
			<div class="content">
				<pre class="brush: xml;">
					<?php echo htmlspecialchars(
					'<section class="container introduction">
						<div class="inner">
							<div class="content">
								<p>I may be drunk, Miss, but in the morning I will be sober and you will still be ugly.</p>
							</div>
						</div>
					</section>'
					);?>
				</pre>
			</div>
		</code>
	</div>
</section>
<!-- End code snippet container -->