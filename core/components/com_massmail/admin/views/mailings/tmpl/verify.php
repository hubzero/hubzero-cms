<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$row   = $this->row;
$disp  = $this->disposition;
$token = Session::getFormToken();
$base  = 'index.php?option=' . $this->option . '&controller=' . $this->controller;

$fromName  = trim((string) $row->get('from_name'))  ?: Config::get('fromname');
$fromEmail = trim((string) $row->get('from_email')) ?: Config::get('mailfrom');
$replyTo   = trim((string) $row->get('reply_to'));
$fromShown = $fromEmail ? ($fromName ? ($fromName . ' <' . $fromEmail . '>') : $fromEmail) : $fromName;

// Delivery line.
if ($disp['scheduled'])
{
	$deliveryLine = Lang::txt('COM_MASSMAIL_VERIFY_DELIVERY_SCHEDULED', $disp['scheduled_local']);
}
else
{
	$deliveryLine = Lang::txt('COM_MASSMAIL_VERIFY_DELIVERY_NOW');
}

// What accepting will do -- approval takes precedence over hold/schedule/send.
$needsApproval = $disp['needs_general_approval'] || $disp['needs_optout_approval'];

Toolbar::title(Lang::txt('COM_MASSMAIL_VERIFY_TITLE'), 'massmail');
Toolbar::cancel('cancel', 'JTOOLBAR_BACK');
?>
<style>
.mm-verify { max-width:1080px; margin:14px auto 0; padding:0 20px; box-sizing:border-box; }
.mm-verify .mm-grid { display:grid; grid-template-columns:1fr 320px; gap:20px; align-items:start; }
@media (max-width:960px){ .mm-verify .mm-grid{ grid-template-columns:1fr; } }
.mm-verify .mm-intro { color:#4a5568; margin:0 0 16px; }
.mm-verify .mm-card { background:#fff; border:1px solid #e2e8f0; border-radius:10px; }

.mm-pv-head { display:flex; align-items:center; gap:10px; border-bottom:1px solid #edf2f7; padding:12px 18px; }
.mm-pv-head h4 { margin:0; font-size:13px; text-transform:uppercase; letter-spacing:.04em; color:#718096; font-weight:700; flex:1; }
.mm-pv-toggle { display:inline-flex; border:1px solid #cbd5e0; border-radius:6px; overflow:hidden; }
.mm-pv-toggle button { background:#fff; border:none; padding:5px 12px; font-size:12px; color:#4a5568; cursor:pointer; }
.mm-pv-toggle button + button { border-left:1px solid #cbd5e0; }
.mm-pv-toggle button.is-active { background:#3182ce; color:#fff; }

.mm-env { padding:12px 18px; border-bottom:1px solid #edf2f7; font-size:13px; }
.mm-env-row { display:flex; gap:10px; padding:2px 0; }
.mm-env-label { flex:0 0 68px; color:#718096; }
.mm-env-val { color:#1a202c; font-weight:600; word-break:break-word; }

.mm-pv-body { padding:0; }
#mm-pv-html { width:100%; height:520px; border:none; display:block; background:#f4f4f4; }
#mm-pv-text { margin:0; padding:18px; white-space:pre-wrap; word-wrap:break-word; font-family:Menlo,Consolas,monospace; font-size:13px; line-height:1.5; color:#1a202c; background:#fff; }
#mm-pv-text[hidden], #mm-pv-html[hidden] { display:none; }

.mm-sum { padding:16px; margin-bottom:16px; }
.mm-sum h4 { margin:0 0 12px; font-size:12px; text-transform:uppercase; letter-spacing:.04em; color:#718096; font-weight:700; }
.mm-sum-row { display:flex; justify-content:space-between; gap:10px; padding:8px 0; border-top:1px solid #edf2f7; font-size:13px; }
.mm-sum-row:first-of-type { border-top:none; }
.mm-sum-row .k { color:#718096; }
.mm-sum-row .v { color:#1a202c; font-weight:600; text-align:right; }
.mm-sum-count { font-size:15px; }

.mm-disp { border-radius:8px; padding:12px 14px; font-size:13px; line-height:1.5; margin:14px 0 0; }
.mm-disp-approve { background:#fefcbf; border:1px solid #f6e05e; color:#744210; }
.mm-disp-hold    { background:#fed7d7; border:1px solid #fc8181; color:#742a2a; }
.mm-disp-send    { background:#c6f6d5; border:1px solid #9ae6b4; color:#22543d; }
.mm-disp-title { font-weight:700; display:block; margin-bottom:3px; }
.mm-disp-sub { display:block; margin-top:5px; }
.mm-optout-flag { background:#fff5f5; border:1px solid #fc8181; color:#742a2a; border-radius:8px; padding:10px 12px; font-size:12px; margin-top:12px; }

.mm-verify-actions { display:flex; gap:10px; align-items:center; margin:20px 0 30px; padding-top:18px; border-top:1px solid #e2e8f0; }
.mm-verify-actions .spacer { flex:1; }
/* Normalize so the <button> and <a> actions render as the same box. */
.mm-verify-actions .btn { display:inline-block; box-sizing:border-box; min-width:120px; margin:0; padding:8px 16px; font:inherit; font-size:14px; line-height:1.4; text-align:center; text-decoration:none; border-radius:6px; cursor:pointer; vertical-align:middle; }
.mm-btn-accept { background:#2f855a; border:1px solid #276749; color:#fff; }
.mm-btn-accept:hover, .mm-btn-accept:focus { background:#276749; border-color:#276749; color:#fff; }
.mm-btn-neutral { background:#edf2f7; border:1px solid #cbd5e0; color:#4a5568; }
.mm-btn-neutral:hover, .mm-btn-neutral:focus { background:#e2e8f0; color:#2d3748; }
</style>

<form action="<?php echo Route::url($base); ?>" method="post" name="adminForm" id="adminForm" class="mm-verify">
	<p class="mm-intro"><?php echo Lang::txt('COM_MASSMAIL_VERIFY_INTRO'); ?></p>

	<div class="mm-grid">
		<div class="mm-main">
			<div class="mm-card">
				<div class="mm-pv-head">
					<h4><?php echo Lang::txt('COM_MASSMAIL_PREVIEW_HEADING'); ?></h4>
					<span class="mm-pv-toggle">
						<button type="button" id="mm-pv-btn-html" class="is-active"><?php echo Lang::txt('COM_MASSMAIL_PREVIEW_HTML'); ?></button>
						<button type="button" id="mm-pv-btn-text"><?php echo Lang::txt('COM_MASSMAIL_PREVIEW_TEXT'); ?></button>
					</span>
				</div>
				<div class="mm-env">
					<div class="mm-env-row"><span class="mm-env-label"><?php echo Lang::txt('COM_MASSMAIL_FROM'); ?></span><span class="mm-env-val"><?php echo $this->escape($fromShown); ?></span></div>
					<?php if ($replyTo) { ?>
					<div class="mm-env-row"><span class="mm-env-label"><?php echo Lang::txt('COM_MASSMAIL_REPLY_TO'); ?></span><span class="mm-env-val"><?php echo $this->escape($replyTo); ?></span></div>
					<?php } ?>
					<div class="mm-env-row"><span class="mm-env-label"><?php echo Lang::txt('COM_MASSMAIL_SUBJECT'); ?></span><span class="mm-env-val"><?php echo $this->escape(stripslashes((string) $row->get('subject'))); ?></span></div>
				</div>
				<div class="mm-pv-body">
					<iframe id="mm-pv-html" sandbox="" title="<?php echo $this->escape(Lang::txt('COM_MASSMAIL_PREVIEW_HTML')); ?>"></iframe>
					<pre id="mm-pv-text" hidden><?php echo $this->escape($this->previewText); ?></pre>
				</div>
			</div>
		</div>

		<div class="mm-side">
			<div class="mm-card mm-sum">
				<h4><?php echo Lang::txt('COM_MASSMAIL_VERIFY_SUMMARY'); ?></h4>
				<div class="mm-sum-row">
					<span class="k"><?php echo Lang::txt('COM_MASSMAIL_VERIFY_RECIPIENTS'); ?></span>
					<span class="v mm-sum-count"><?php echo number_format($this->preview); ?></span>
				</div>
				<div class="mm-sum-row">
					<span class="k"><?php echo Lang::txt('COM_MASSMAIL_VERIFY_DELIVERY'); ?></span>
					<span class="v"><?php echo $this->escape($deliveryLine); ?></span>
				</div>

				<?php if ($needsApproval) { ?>
					<div class="mm-disp mm-disp-approve">
						<span class="mm-disp-title"><?php echo Lang::txt('COM_MASSMAIL_VERIFY_WILL_APPROVE'); ?></span>
						<?php if ($disp['needs_general_approval']) { ?>
							<span class="mm-disp-sub"><?php echo Lang::txt('COM_MASSMAIL_VERIFY_WILL_APPROVE_GENERAL'); ?></span>
						<?php } ?>
						<?php if ($disp['needs_optout_approval']) { ?>
							<span class="mm-disp-sub"><?php echo Lang::txt('COM_MASSMAIL_VERIFY_WILL_APPROVE_OPTOUT'); ?></span>
						<?php } ?>
					</div>
				<?php } elseif ($disp['held']) { ?>
					<div class="mm-disp mm-disp-hold">
						<span class="mm-disp-title"><?php echo Lang::txt('COM_MASSMAIL_STATUS_HELD'); ?></span>
						<span class="mm-disp-sub"><?php echo Lang::txt('COM_MASSMAIL_VERIFY_WILL_HOLD'); ?></span>
					</div>
				<?php } elseif ($disp['scheduled']) { ?>
					<div class="mm-disp mm-disp-send">
						<span class="mm-disp-title"><?php echo Lang::txt('COM_MASSMAIL_STATUS_SCHEDULED'); ?></span>
						<span class="mm-disp-sub"><?php echo Lang::txt('COM_MASSMAIL_VERIFY_WILL_SCHEDULE'); ?></span>
					</div>
				<?php } else { ?>
					<div class="mm-disp mm-disp-send">
						<span class="mm-disp-title"><?php echo Lang::txt('COM_MASSMAIL_STATUS_SENDING'); ?></span>
						<span class="mm-disp-sub"><?php echo Lang::txt('COM_MASSMAIL_VERIFY_WILL_SEND'); ?></span>
					</div>
				<?php } ?>

				<?php if ($disp['needs_optout_approval']) { ?>
					<div class="mm-optout-flag">&#9888; <?php echo Lang::txt('COM_MASSMAIL_VERIFY_OPTOUT_FLAG'); ?></div>
				<?php } ?>
			</div>
		</div>
	</div>

	<div class="mm-verify-actions">
		<button type="button" class="btn mm-btn-accept" onclick="Joomla.submitbutton('launch');"><?php echo Lang::txt('COM_MASSMAIL_ACCEPT_SEND'); ?></button>
		<a class="btn mm-btn-neutral" href="<?php echo Route::url($base . '&task=edit&id=' . (int) $row->get('id')); ?>"><?php echo Lang::txt('COM_MASSMAIL_EDIT_MESSAGE'); ?></a>
		<span class="spacer"></span>
		<a class="btn mm-btn-neutral" href="<?php echo Route::url('index.php'); ?>"><?php echo Lang::txt('COM_MASSMAIL_BACK_HOME'); ?></a>
	</div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo (int) $row->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<?php echo Html::input('token'); ?>
</form>

<script type="text/javascript">
(function () {
	var iframe = document.getElementById('mm-pv-html');
	if (iframe) { iframe.srcdoc = <?php echo json_encode($this->previewHtml, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>; }

	var bHtml = document.getElementById('mm-pv-btn-html');
	var bText = document.getElementById('mm-pv-btn-text');
	var pHtml = document.getElementById('mm-pv-html');
	var pText = document.getElementById('mm-pv-text');
	function show(mode) {
		var isHtml = (mode === 'html');
		pHtml.hidden = !isHtml; pText.hidden = isHtml;
		bHtml.classList.toggle('is-active', isHtml);
		bText.classList.toggle('is-active', !isHtml);
	}
	if (bHtml && bText) {
		bHtml.addEventListener('click', function () { show('html'); });
		bText.addEventListener('click', function () { show('text'); });
	}
})();
</script>
