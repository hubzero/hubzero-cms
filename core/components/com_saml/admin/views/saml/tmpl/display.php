<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SAML') . ': ' . Lang::txt('COM_SAML_MENU_OVERVIEW'), 'saml');

if (User::authorise('core.admin', $this->option))
{
	Toolbar::preferences($this->option, '550');
}

$now = time();
$certWarn = 30 * 24 * 60 * 60;
?>

<section>
	<div class="grid">
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_SAML_OVERVIEW_IDP'); ?></span></legend>

				<table class="meta">
					<tbody>
						<tr>
							<th class="key"><?php echo Lang::txt('COM_SAML_OVERVIEW_ENABLED'); ?>:</th>
							<td><?php echo $this->params->get('IdP_enable', 1) ? Lang::txt('JYES') : ('<strong>' . Lang::txt('JNO') . '</strong>'); ?></td>
						</tr>
						<tr>
							<th class="key"><?php echo Lang::txt('COM_SAML_OVERVIEW_ENTITY_ID'); ?>:</th>
							<td>
								<code><?php echo $this->escape($this->idp->getEntityID()); ?></code>
								<?php if ($this->idp->isIdentityRequestDerived()) { ?>
									<p class="warning"><?php echo Lang::txt('COM_SAML_OVERVIEW_IDENTITY_UNPINNED'); ?></p>
								<?php } ?>
							</td>
						</tr>
						<tr>
							<th class="key"><?php echo Lang::txt('COM_SAML_OVERVIEW_SSO_URL'); ?>:</th>
							<td><code><?php echo $this->escape($this->idp->getLoginUrl()); ?></code></td>
						</tr>
						<tr>
							<th class="key"><?php echo Lang::txt('COM_SAML_OVERVIEW_SLO_URL'); ?>:</th>
							<td><code><?php echo $this->escape($this->idp->getLogoutUrl()); ?></code></td>
						</tr>
						<tr>
							<th class="key"><?php echo Lang::txt('COM_SAML_OVERVIEW_METADATA_URL'); ?>:</th>
							<td>
								<?php if ($this->params->get('enableIdPMetadataEndpoint', 1)) { ?>
									<a href="<?php echo $this->escape($this->idp->getMetadataUrl()); ?>" target="_blank" rel="noopener"><code><?php echo $this->escape($this->idp->getMetadataUrl()); ?></code></a>
								<?php } else { ?>
									<?php echo Lang::txt('COM_SAML_OVERVIEW_METADATA_DISABLED'); ?>
								<?php } ?>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</div>
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_SAML_OVERVIEW_CERTIFICATE'); ?></span></legend>

				<table class="meta">
					<tbody>
						<tr>
							<th class="key"><?php echo Lang::txt('COM_SAML_OVERVIEW_CERT_FILE'); ?>:</th>
							<td>
								<code><?php echo $this->escape($this->certFile); ?></code>
								<?php if (!$this->certReadable) { ?>
									<p class="error"><?php echo Lang::txt('COM_SAML_OVERVIEW_FILE_UNREADABLE'); ?></p>
								<?php } ?>
							</td>
						</tr>
						<?php if ($this->cert) { ?>
							<tr>
								<th class="key"><?php echo Lang::txt('COM_SAML_OVERVIEW_CERT_SUBJECT'); ?>:</th>
								<td><?php echo $this->escape($this->cert['subject']); ?></td>
							</tr>
							<tr>
								<th class="key"><?php echo Lang::txt('COM_SAML_OVERVIEW_CERT_EXPIRES'); ?>:</th>
								<td>
									<time datetime="<?php echo gmdate('Y-m-d\TH:i:s\Z', $this->cert['valid_to']); ?>"><?php echo Date::of($this->cert['valid_to'])->toLocal(); ?></time>
									<?php if ($this->cert['valid_to'] < $now) { ?>
										<p class="error"><?php echo Lang::txt('COM_SAML_OVERVIEW_CERT_EXPIRED'); ?></p>
									<?php } elseif ($this->cert['valid_to'] < ($now + $certWarn)) { ?>
										<p class="warning"><?php echo Lang::txt('COM_SAML_OVERVIEW_CERT_EXPIRING', ceil(($this->cert['valid_to'] - $now) / 86400)); ?></p>
									<?php } ?>
								</td>
							</tr>
						<?php } ?>
						<tr>
							<th class="key"><?php echo Lang::txt('COM_SAML_OVERVIEW_KEY_FILE'); ?>:</th>
							<td>
								<code><?php echo $this->escape($this->keyFile); ?></code>
								<?php if (!$this->keyReadable) { ?>
									<p class="error"><?php echo Lang::txt('COM_SAML_OVERVIEW_FILE_UNREADABLE'); ?></p>
								<?php } ?>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_SAML_OVERVIEW_TRUST'); ?></span></legend>

				<table class="meta">
					<tbody>
						<tr>
							<th class="key"><?php echo Lang::txt('COM_SAML_OVERVIEW_SERVICE_PROVIDERS'); ?>:</th>
							<td>
								<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=serviceproviders'); ?>">
									<?php echo Lang::txt('COM_SAML_OVERVIEW_SP_COUNT', $this->spEnabled, $this->spTotal); ?>
								</a>
							</td>
						</tr>
						<tr>
							<th class="key"><?php echo Lang::txt('COM_SAML_OVERVIEW_ACTIVE_SESSIONS'); ?>:</th>
							<td>
								<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=sessions'); ?>">
									<?php echo $this->sessionsActive; ?>
								</a>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</div>
	</div>
</section>
