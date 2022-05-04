<style>
	.upgrade-to-v4-modal p a {
		color: #17a8e3;
		font-weight: 500;
		text-decoration: none;
		white-space: nowrap;
	}
</style>
<div class="snapshot-three wps-popup-modal upgrade-to-v4-modal show">
	<div class="wps-popup-mask"></div>
	<div class="wps-popup-content" style="width: 540px;">
		<div class="wpmud-box" style="border-radius: 0;">
			<form>
				<?php wp_nonce_field( 'snapshot_admin_notice_v4', '_wpnonce-snapshot_admin_notice' ); ?>
				<div class="wpmud-box-title can-close" style="padding: 0;">
					<h3 style="width: 100%; height: 159px; background: url('<?php echo WPMUDEVSnapshot::get_file_url( '/assets/img/modal-upgrade-to-v4-header.svg' ) ?>');"></h3>
					<i class="wps-icon i-close snapshot-upgrade-to-v4-modal-dismiss" style="position: absolute; right: 30px; top: 30px; color: #ffffff; font-size: 8px;"></i>
				</div>

				<div class="wpmud-box-content no-content-after">
					<div class="row">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<h2 style="font-family: Roboto,Arial,sans-serif; font-size: 22px; line-height: 30px; color: #333333; font-weight: bold; text-transform: none;"><?php esc_html_e( 'Introducing our New Snapshot Engine with Incremental Backups!', SNAPSHOT_I18N_DOMAIN ); ?></h2>
							<p style="margin-top: 15px; margin-bottom: 0; text-align: center; font-size: 13px; line-height: 22px; color: #888888;"><?php esc_html_e( 'Snapshot 4 has been rebuilt from the ground up and features a brand new incremental backup engine. Snapshot 4 also integrates with more third-party storage solutions, including Google Drive, Dropbox, FTP/SFTP, and Amazon S3, as well as S3 Compatible Storage options such as Backblaze, Google Cloud, Digital Ocean, and more.', SNAPSHOT_I18N_DOMAIN ); ?></p>
							<p style="margin-top: 15px; margin-bottom: 0; text-align: center; font-size: 13px; line-height: 22px; color: #888888;"><?php esc_html_e( 'Upgrade to Snapshot 4 today, and let us know if you have any feature requests. Please note that while Snapshot v3 and v4 can be used side-by-side, Snapshot 4 is now our only focus.', SNAPSHOT_I18N_DOMAIN ); ?></p>
							<?php
								$href_about_v4 = 'https://wpmudev.com/docs/wpmu-dev-plugins/snapshot-4-0/?utm_source=snapshot&utm_medium=plugin-upgrade&utm_campaign=snapshot-plugin-upgrade#faq';
							?>
							<p style="margin-top: 15px; margin-bottom: 0; text-align: center; font-size: 13px; line-height: 22px; color: #888888;"><?php echo wp_kses_post( sprintf( __( 'Unsure about upgrading? Learn more about the new engine in our <a href="%s" target="_blank">Snapshot 4 migration Q&A</a>.', SNAPSHOT_I18N_DOMAIN ), $href_about_v4 ) ); ?></p>
							<div style="width: 100%; text-align: center; margin-top: 30px;"><button class="button button-blue button-small snapshot-install-v4-button"><?php esc_html_e( 'Upgrade', SNAPSHOT_I18N_DOMAIN ); ?></button></div>
							<p style="margin-top: 13px; font-weight: 500; margin-bottom: 0; text-align: center; font-size: 13px; line-height: 22px;"><a href="#" class="snapshot-upgrade-to-v4-modal-dismiss" style="color: #888888;"><?php esc_html_e( 'I will upgrade later', SNAPSHOT_I18N_DOMAIN ); ?></a></p>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
