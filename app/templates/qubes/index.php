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

Html::behavior('framework', true);
Html::behavior('modal');

$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/hub.js?v=' . filemtime(__DIR__ . '/js/hub.js'));

$isFrontPage = false;
if ($this->countModules('qslider')) 
{
	$isFrontPage = true;
}

// Index page files only
if ($isFrontPage)
{
	// temp for slider -->
	$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/jquery.flexslider.js?v=' . filemtime(__DIR__ . '/js/jquery.flexslider.js'));
	//$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/masonry.pkgd.min.js?v=' . filemtime(__DIR__ . '/js/masonry.pkgd.min.js'));
	$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/home.js?v=' . filemtime(__DIR__ . '/js/home.js'));
}

$browser = new \Hubzero\Browser\Detector();
$cls = array(
	$browser->name(),
	$browser->name() . $browser->major()
);

$this->setTitle(Config::get('sitename') . ' - ' . $this->getTitle());
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo implode(' ', $cls); ?> ie ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo implode(' ', $cls); ?> ie ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo implode(' ', $cls); ?> ie ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo implode(' ', $cls); ?> ie ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo implode(' ', $cls); ?>"> <!--<![endif]-->
	<head>
		<meta name="viewport" content="width=device-width" />
		
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo \Hubzero\Document\Assets::getSystemStylesheet(); ?>" />
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/main.css" />
		<link rel="stylesheet" type="text/css" media="print"  href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/print.css" />

		<jdoc:include type="head" />

		<!--[if lt IE 9]><script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/html5.js"></script><![endif]-->

		<!--[if IE 10]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie10.css" /><![endif]-->
		<!--[if IE 9]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie9.css" /><![endif]-->
		<!--[if IE 8]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie8.css" /><![endif]-->
		<!--[if IE 7]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie7.css" /><![endif]-->
	</head>
	<body class="<?php echo($isFrontPage ? 'home' : ''); ?>">
		<jdoc:include type="modules" name="notices" />
		<jdoc:include type="modules" name="helppane" />

		<div id="header" class="cf">

			<div id="mobile-nav" class="show-m">
				<a id="mobile-menu" href="#">
					<p id="nav-icon"><span></span></p>
					<em><?php echo Lang::txt('TPL_QUBES_MENU'); ?></em>
				</a>
			</div>

			<nav id="hubLogo">
				<a href="<?php echo Request::base(); ?>" title="<?php echo Config::get('sitename'); ?>">
					<?php echo file_get_contents(__DIR__ . '/svg/logo.svg'); ?>
				</a>
			</nav>

			<div class="mobile-wrapper">
				<div id="toolbar" class="cf <?php if (!User::get('guest')) { echo 'loggedin'; } else { echo 'loggedout'; } ?>">

					<jdoc:include type="modules" name="search" />

					<?php if (!User::get('guest')) { ?>
						<a id="usersname" class="item" href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id')); ?>">
							<img src="<?php echo User::picture(); ?>" alt="<?php echo User::get('name'); ?>">
							<?php  //echo User::get('name'); ?>
							DASHBOARD
						</a>
						<a id="logout" class="item" title="log out" href="<?php echo Route::url('index.php?option=com_logout'); ?>"><span><?php echo Lang::txt('Logout'); ?></span></a>
					<?php } else { ?>
						<a id="login" class="item" href="<?php echo Route::url('index.php?option=com_login'); ?>" title="<?php echo Lang::txt('Login'); ?>"><?php echo Lang::txt('Login'); ?></a>
						<a id="register" class="item" href="<?php echo Route::url('index.php?option=com_register'); ?>" title="<?php echo Lang::txt('Sign up for a free account'); ?>"><?php echo Lang::txt('Sign Up'); ?></a>
					<?php } ?>
				</div>

				<div class="tr">
					<?php if ($this->countModules('helppane')) : ?>
						<p id="tab" class="helpme">
							<a href="<?php echo Route::url('index.php?option=com_support'); ?>" title="<?php echo Lang::txt('Need help? Send a trouble report to our support team.'); ?>">
								<span><?php echo Lang::txt('Need Help?'); ?></span>
							</a>
						</p>
					<?php endif; ?>
					<div class="social" class="cf">
						<ul class="cf">
							<li><a href="https://www.facebook.com/qubeshub" target="_blank" class="fb">&nbsp;<span>Facebook</span></a></li>
							<li><a href="https://twitter.com/qubeshub" target="_blank" class="tw">&nbsp;<span>Twitter</span></a></li>
							<li><a href="https://www.pinterest.com/qubeshub/" target="_blank" class="pn">&nbsp;<span>Pintrest</span></a></li>
						</ul>
					</div>
				</div>

				<div id="nav" class="cf">
					<jdoc:include type="modules" name="user3" />
				</div><!-- / #nav -->
			</div>
		</div><!-- / #header -->

		<?php if (!$this->countModules('bannerjs or welcome')) : ?>
			<div id="trail">
				<jdoc:include type="modules" name="breadcrumbs" />
				<jdoc:include type="modules" name="collector" />
			</div><!-- / #trail -->
		<?php endif; ?>

		<div id="splash">
			<?php if ($this->getBuffer('message')) : ?>
				<jdoc:include type="message" />
			<?php endif; ?>
		</div><!-- / #splash -->

		<div id="wrap" class="transit200">
			<main id="content" class="<?php echo Request::getCmd('option', ''); ?>" role="main">

				<?php if ($isFrontPage) : ?>
					<jdoc:include type="modules" name="qslider" />
				<?php endif; ?>

				<div class="inner<?php if ($this->countModules('left or right')) { echo ' withmenu'; } ?>">
					<?php if ($this->countModules('left or right')) : ?>
						<section class="main section">
					<?php endif; ?>

					<?php if ($this->countModules('left')) : ?>
							<aside class="aside">
								<jdoc:include type="modules" name="left" />
							</aside><!-- / .aside -->
					<?php endif; ?>
					<?php if ($this->countModules('left or right')) : ?>
							<div class="subject">
					<?php endif; ?>

								<!-- start component output -->
								<jdoc:include type="component" />
								<!-- end component output -->

					<?php if ($this->countModules('left or right')) : ?>
							</div><!-- / .subject -->
					<?php endif; ?>
					<?php if ($this->countModules('right')) : ?>
							<aside class="aside">
								<jdoc:include type="modules" name="right" />
							</aside><!-- / .aside -->
					<?php endif; ?>

					<?php if ($this->countModules('left or right')) : ?>
						</section><!-- / .main section -->
					<?php endif; ?>
				</div><!-- / .inner -->
			</main><!-- / #content -->

			<footer id="footer" class="cf">
				<div class="grid break3">
					<div class="col span8 l">
						<svg xmlns="http://www.w3.org/2000/svg" width="158.019" height="45.662" viewBox="0 0 158.019 45.662"><g fill="#fff"><path d="M12.553 40.479c.749-2.227 2.55-5.34 6.831-8.091l-7.318-4.225v-10.659l7.325-4.231c-4.283-2.75-6.085-5.865-6.832-8.09l-.117-.404c-6.72 3.286-11.36 10.169-11.36 18.154 0 7.962 4.614 14.83 11.305 18.126l.166-.58zM14.434 4.675c1.006 2.89 3.378 5.474 6.87 7.497 3.489-2.023 5.861-4.606 6.867-7.495.08-.23.142-.45.201-.667-2.204-.824-4.58-1.296-7.071-1.296-2.49 0-4.865.472-7.068 1.295.059.217.121.436.201.666zM14.004 27.044l7.295 4.209 7.29-4.209v-8.421l-7.29-4.213-7.295 4.213zM30.165 4.781l-.117.404c-.749 2.227-2.551 5.343-6.835 8.091l7.316 4.228v10.659l-7.318 4.225c4.282 2.751 6.084 5.864 6.83 8.091l.168.583c6.693-3.295 11.311-10.164 11.311-18.129 0-7.984-4.638-14.866-11.355-18.152zM28.166 40.984c-1.007-2.884-3.377-5.465-6.865-7.491h-.004c-3.488 2.026-5.86 4.607-6.866 7.491-.103.295-.185.578-.254.851 2.219.837 4.614 1.316 7.125 1.316 2.509 0 4.901-.479 7.118-1.313-.069-.274-.152-.558-.254-.854z"/></g><g><path fill="#E5E5E5" d="M21.3 14.366l-7.334 4.233 7.334 4.235 7.335-4.235z"/><path fill="#fff" d="M13.967 27.066l7.333 4.236v-8.468l-7.334-4.235z"/><path fill="#C7C8CA" d="M28.633 27.066l.002-8.467-7.335 4.235v8.468z"/></g><g fill="#fff"><path d="M48.902 22.414c0-6.662 4.719-11.551 11.416-11.551 6.661 0 11.416 4.889 11.416 11.551 0 3.212-1.105 5.993-2.979 8.031l1.839 2.074-2.712 2.279-1.941-2.177c-1.64.871-3.547 1.341-5.622 1.341-6.699 0-11.417-4.887-11.417-11.548zm14.562 7.397l-2.778-3.146 2.744-2.276 2.78 3.146c.97-1.37 1.506-3.146 1.506-5.12 0-4.62-2.911-8.069-7.397-8.069-4.52 0-7.398 3.449-7.398 8.069 0 4.584 2.878 8.065 7.398 8.065 1.17-.001 2.206-.235 3.145-.669zM75.485 24.723v-13.49h3.984v13.389c0 3.549 1.976 5.857 5.69 5.857 3.715 0 5.691-2.309 5.691-5.857v-13.389h3.984v13.49c0 5.521-3.181 9.239-9.675 9.239-6.46 0-9.674-3.717-9.674-9.239zM99.591 33.56v-22.327h10.978c4.117 0 6.361 2.543 6.361 5.69 0 2.778-1.807 4.688-3.882 5.123 2.411.369 4.316 2.744 4.316 5.458 0 3.48-2.272 6.059-6.492 6.059h-11.281v-.003zm13.323-16.002c0-1.64-1.139-2.879-3.111-2.879h-6.296v5.758h6.296c1.972.001 3.111-1.17 3.111-2.879zm.433 9.441c0-1.672-1.167-3.114-3.382-3.114h-6.459v6.226h6.459c2.113 0 3.382-1.171 3.382-3.112zM121.32 33.56v-22.327h15.295v3.446h-11.38v5.758h11.146v3.447h-11.146v6.226h11.38v3.449h-15.295zM139.197 30.412l2.208-3.045c1.509 1.641 3.952 3.112 7.067 3.112 3.212 0 4.449-1.573 4.449-3.081 0-4.685-12.986-1.771-12.986-9.973 0-3.717 3.215-6.562 8.133-6.562 3.449 0 6.293 1.138 8.334 3.145l-2.207 2.913c-1.775-1.773-4.151-2.577-6.495-2.577-2.273 0-3.751 1.139-3.751 2.779 0 4.184 12.989 1.607 12.989 9.908 0 3.717-2.642 6.928-8.637 6.928-4.116.003-7.095-1.47-9.104-3.547z"/></g></svg>
						<p>Copyright <?php echo date('Y'); ?> QUBES <span>Powered by HUBzero, a <a href="http://www.purdue.edu" target="_blank">Purdue</a> project</span></p>
					</div>
					<div class="col span4 omega">
						<a href="http://www.nsf.gov/" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="50" height="50.057" viewBox="5.002 5.007 50 50.057" enable-background="new 5.002 5.007 50 50.057"><g fill="#CECECE"><path d="M22.58 24.948l.042.003c1.253.094 1.475.58 1.475 1.57v4.85l-5.609-6.902-.014-.017h-3.507v.499h.045c.173 0 .408 0 1.339 1.145v7.712c0 1.05-.453 1.149-1.395 1.259l-.041.005v.495h3.793v-.495l-.041-.005c-1.213-.13-1.508-.438-1.508-1.568v-6.435l7.222 8.796h.523v-9.65c0-.785.111-1.119 1.233-1.259l.039-.005v-.494h-3.597l.001.496zM33.062 29.381l-2.396-1.19c-1.345-.671-1.655-1.024-1.655-1.881 0-1.033.88-1.503 1.699-1.503 2.317 0 3.073 2.677 3.15 2.981l.011.035h.538v-3.63h-.572l-.008.035c-.088.324-.161.52-.494.52-.17 0-.385-.073-.654-.168-.451-.155-1.066-.369-1.889-.369-3.474 0-3.649 2.713-3.649 3.257 0 .762 0 2.18 2.814 3.531l1.092.522c1.117.532 1.8.912 1.8 1.947 0 .182-.055 1.78-1.992 1.78-2.357 0-3.091-2.415-3.296-3.452l-.007-.038h-.556v4.12h.554l.008-.035c.074-.293.152-.521.445-.521.175 0 .387.072.654.164.482.164 1.14.392 2.134.392 2.809 0 4.09-1.791 4.09-3.455-.001-2.069-1.636-2.949-1.821-3.042zM35.75 24.948l.066.004c.763.05 1.367.089 1.367 1.11v7.892c0 .966-.463 1.016-1.384 1.111l-.052.006v.495h5.962v-.498l-.045-.002c-1.127-.049-1.748-.075-1.748-1.112v-3.752c1.917.017 2.215 1.088 2.402 2.489l.006.039h.493v-5.604h-.492l-.007.038c-.216 1.23-.419 2.393-2.367 2.393h-.035v-3.738c0-.65.227-.721 1.126-.721 2.79 0 3.205 1.017 3.56 2.687l.007.037h.476v-3.369h-9.337v.496h.002zM50.284 27.391c-.152-.402-.293-.806-.422-1.216-.076-.775-.079-1.538-.063-2.207.278-1.438 1.316-2.197 2.562-2.667l-.451-1.1c-1.343.652-2.959.769-4.176-.38-.259-.274-.512-.554-.757-.842-.404-.733-.728-1.496-.984-2.167-.293-1.433.375-2.532 1.345-3.444l-.836-.844c-.993 1.116-2.441 1.842-4.006 1.247-.299-.134-.596-.274-.887-.42-.685-.537-1.299-1.141-1.813-1.682-.818-1.211-.622-2.482-.072-3.694l-1.095-.46c-.49 1.411-1.553 2.635-3.225 2.685-.51-.015-1.019-.043-1.527-.097-.651-.212-1.265-.466-1.807-.706-1.222-.805-1.526-2.054-1.482-3.386l-1.188-.004c.087 1.49-.425 3.029-1.949 3.713-.353.134-.708.26-1.068.376-.831.093-1.655.097-2.373.081-1.436-.277-2.195-1.316-2.666-2.561l-1.098.448c.651 1.344.768 2.959-.38 4.177-.308.291-.623.573-.949.844-.715.39-1.455.701-2.109.953-1.433.294-2.532-.374-3.442-1.346l-.844.835c1.116.993 1.842 2.442 1.247 4.005-.146.327-.298.649-.46.969-.515.641-1.085 1.218-1.599 1.705-1.21.818-2.481.622-3.694.073l-.459 1.095c1.41.49 2.637 1.551 2.685 3.223-.013.444-.036.887-.078 1.33-.225.728-.507 1.419-.773 2.021-.805 1.221-2.055 1.524-3.386 1.481l-.005 1.19c1.49-.086 3.03.426 3.714 1.951.138.362.268.727.386 1.096.091.826.095 1.646.078 2.359-.277 1.436-1.315 2.196-2.561 2.666l.448 1.099c1.345-.651 2.961-.768 4.178.382.287.301.564.611.832.932.388.712.697 1.449.949 2.099.293 1.433-.376 2.532-1.348 3.442l.836.844c.993-1.117 2.442-1.843 4.005-1.247.349.156.693.321 1.035.496.636.511 1.208 1.079 1.691 1.586.82 1.211.623 2.482.073 3.694l1.095.462c.49-1.411 1.551-2.638 3.224-2.685.427.012.854.033 1.281.072.765.228 1.493.524 2.122.803 1.22.806 1.524 2.057 1.481 3.387l1.188.006c-.087-1.492.424-3.029 1.95-3.714.402-.154.811-.297 1.225-.427.772-.075 1.534-.078 2.202-.062 1.436.277 2.194 1.314 2.665 2.562l1.098-.451c-.649-1.344-.766-2.961.382-4.179.309-.29.623-.571.948-.844.682-.363 1.379-.656 2.001-.896 1.431-.293 2.531.375 3.442 1.346l.843-.835c-1.115-.993-1.842-2.442-1.247-4.006.195-.435.401-.864.629-1.286.479-.577.998-1.101 1.468-1.547 1.212-.82 2.482-.623 3.695-.075l.46-1.095c-1.411-.489-2.637-1.549-2.686-3.223.014-.469.04-.938.086-1.407.218-.689.487-1.344.74-1.913.807-1.22 2.055-1.525 3.386-1.481l.006-1.19c-1.491.088-3.03-.424-3.716-1.949zm-5.771-9.719c.554.141 1.057.679 1.464 1.17.392.615.858 1.265 1.056 2.026l-.086.318c-.223.518-.869.643-1.368.804l-.661.104c-.562-1.05-1.229-2.034-1.99-2.937.167-.266.339-.528.52-.786.279-.345.591-.716 1.065-.699zm-14.512 28.832c-9.082 0-16.471-7.388-16.471-16.469 0-9.083 7.389-16.471 16.471-16.471s16.468 7.389 16.468 16.471c.002 9.081-7.386 16.469-16.468 16.469zm8.757-33.472c.513-.076 1.111.168 1.64.437.62.442 1.349.876 1.851 1.54l.042.327c-.007.563-.557.925-.957 1.267l-.612.376c-.887-.729-1.848-1.368-2.871-1.907.055-.33.117-.657.189-.984.13-.429.273-.893.718-1.056zm-7.127-1.98c.612-.364 1.604-.234 2.312-.163l.444.065c.433.108.867.25 1.26.481l.164.287c.209.522-.158 1.068-.4 1.537l-.448.618c-1.093-.337-2.232-.564-3.406-.672-.068-.299-.13-.601-.184-.903-.046-.445-.089-.929.258-1.25zm-7.391.82c.343-.458 1.034-.716 1.644-.905.714-.126 1.488-.305 2.264-.198l.262.203c.392.402.26 1.047.217 1.573l-.157.658c-1.2.108-2.364.341-3.478.688-.178-.25-.35-.504-.516-.764-.209-.394-.434-.825-.236-1.255zm-5.258 2.067c.586-.377 1.208-.807 1.931-.994l.318.087c.516.222.642.868.802 1.369l.107.676c-1.075.566-2.081 1.246-3.002 2.021-.26-.163-.518-.333-.771-.509-.345-.284-.718-.597-.698-1.069.154-.608.788-1.154 1.313-1.581zm-5.403 5.534c.403-.581.818-1.23 1.425-1.69l.328-.042c.562.007.925.556 1.266.958l.364.594c-.727.88-1.364 1.836-1.904 2.853-.311-.052-.621-.112-.93-.181-.426-.129-.891-.274-1.054-.719-.082-.556.212-1.212.505-1.773zm-2.702 6.662c.128-.598.276-1.225.6-1.777l.286-.165c.523-.208 1.069.16 1.536.4l.564.41c-.347 1.112-.582 2.272-.691 3.468-.292.066-.584.125-.878.176-.444.045-.927.088-1.249-.259-.354-.596-.24-1.552-.168-2.253zm.996 9.662c-.467-.35-.726-1.061-.915-1.678-.126-.708-.301-1.477-.195-2.246l.202-.26c.403-.395 1.048-.262 1.572-.219l.631.151c.108 1.215.344 2.392.695 3.518-.242.17-.485.337-.736.496-.392.211-.822.435-1.254.238zm3.629 6.545c-.604-.154-1.145-.777-1.57-1.3-.376-.583-.799-1.201-.985-1.917l.086-.317c.222-.518.868-.644 1.369-.805l.645-.102c.566 1.072 1.244 2.075 2.018 2.995-.159.252-.322.501-.494.746-.283.347-.595.72-1.069.7zm5.779 4.619c-.581.086-1.273-.239-1.848-.545-.575-.399-1.212-.812-1.667-1.41l-.041-.327c.007-.562.556-.926.958-1.267l.566-.347c.902.743 1.883 1.397 2.926 1.945-.051.3-.108.6-.175.897-.129.427-.274.891-.719 1.054zm7.193 2.055c-.554.33-1.423.255-2.107.185-.655-.141-1.36-.281-1.975-.642l-.163-.287c-.208-.523.159-1.067.399-1.536l.398-.547c1.13.35 2.311.581 3.528.685.068.297.128.594.181.894.043.442.087.927-.261 1.248zm7.38-.854c-.396.533-1.264.794-1.929.991-.63.117-1.298.236-1.967.145l-.258-.203c-.396-.4-.263-1.047-.22-1.57l.16-.669c1.193-.117 2.354-.357 3.462-.708.178.251.35.502.515.762.21.391.437.825.237 1.252zm5.073-1.944c-.531.345-1.097.705-1.742.873l-.318-.085c-.519-.224-.644-.869-.806-1.37l-.107-.69c1.031-.552 1.998-1.208 2.889-1.955.261.166.521.338.775.517.346.283.72.595.7 1.067-.163.638-.854 1.206-1.391 1.643zm5.292-5.335l-.031.055c-.339.49-.708.995-1.203 1.371l-.328.042c-.562-.006-.927-.558-1.268-.958l-.365-.596c.766-.925 1.432-1.931 1.988-3.003.297.051.593.107.888.174.427.128.891.274 1.054.72.101.704-.399 1.571-.735 2.195zm2.897-7l-.018.134c-.119.53-.267 1.072-.552 1.559l-.286.165c-.522.208-1.068-.161-1.537-.4l-.549-.398c.336-1.108.562-2.264.664-3.454.286-.063.574-.123.866-.174.444-.045.927-.086 1.249.26.365.61.236 1.603.163 2.308zm.12-5.795l-.201.262c-.403.393-1.048.261-1.573.218l-.639-.153c-.114-1.202-.354-2.368-.709-3.483.242-.171.486-.338.736-.498.394-.212.825-.435 1.255-.239.526.394.789 1.247.986 1.91.116.634.239 1.309.145 1.983z"/></g></svg></a>

						<p>QUBES is supported by the <a href="http://www.nsf.gov/" target="_blank">National Science Foundation</a> and&nbsp;<a href="/community/partners">other&nbsp;funding&nbsp;agencies</a></p>
					</div>
				</div>
			</footer><!-- / #footer -->
		</div><!-- / #wrap -->

		<jdoc:include type="modules" name="endpage" />
	</body>
</html>