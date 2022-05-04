<style>
	.snapshot-upgrade-prompt p a {
		color: #1e8cbe;
		font-weight: 500;
		text-decoration: none;
		white-space: nowrap;
	}
</style>
<div class="notice notice-info is-dismissible snapshot-upgrade-prompt" style="padding: 20px 15px;">
	<form>
		<?php wp_nonce_field( 'snapshot_admin_notice_v4', '_wpnonce-snapshot_admin_notice' ); ?>

		<div style="position: relative;">
			<div style="position: absolute; width: 100px; height: 125px; background-color: #f2edff; background-repeat: no-repeat; background-position: center; background-image: url('<?php echo esc_attr( $bg_image_url ); ?>');"></div>
			<div style="padding-left: 120px; padding-right: 30px;">
				<div style="min-height: 80px;">
					<p style="margin: 0; padding: 0; font-size: 16px; font-weight: bold; line-height: 30px; color: #333333;"><?php esc_html_e( 'Introducing our New Snapshot Engine with Incremental Backups!', SNAPSHOT_I18N_DOMAIN ); ?></p>
					<?php
						$href_about_v4 = 'https://wpmudev.com/docs/wpmu-dev-plugins/snapshot-4-0/?utm_source=snapshot&utm_medium=plugin-upgrade&utm_campaign=snapshot-plugin-upgrade#faq';
					?>
					<p style="margin: 0; padding: 0; font-size: 13px; line-height: 22px; color: #23282d;"><?php esc_html_e( 'Snapshot 4 has been rebuilt from the ground up and features a brand new incremental backup engine. Snapshot 4 also integrates with more third-party storage solutions, including Google Drive, Dropbox, FTP/SFTP, and Amazon S3, as well as S3 Compatible Storage options such as Backblaze, Google Cloud, Digital Ocean, and more.', SNAPSHOT_I18N_DOMAIN ); ?></p>
					<p style="margin: 0; padding: 0; font-size: 13px; line-height: 22px; color: #23282d;"><?php esc_html_e( 'Upgrade to Snapshot 4 today, and let us know if you have any feature requests. Please note that while Snapshot v3 and v4 can be used side-by-side, Snapshot 4 is now our only focus.', SNAPSHOT_I18N_DOMAIN ); ?></p>
					<p style="margin: 0; padding: 0; font-size: 13px; line-height: 22px; color: #23282d;"><?php echo wp_kses_post( sprintf( __( 'Unsure about upgrading? Learn more about the new engine in our <a href="%s" target="_blank">Snapshot 4 migration Q&A</a>.', SNAPSHOT_I18N_DOMAIN ), $href_about_v4 ) ); ?></p>
				</div>
				<div style="margin-top: 15px;">
					<button class="button button-primary snapshot-upgrade-prompt-install"><?php esc_html_e( 'Upgrade', SNAPSHOT_I18N_DOMAIN ); ?></button>
				</div>
			</div>
		</div>

	</form>
</div>
