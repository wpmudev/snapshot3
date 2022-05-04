<style>
	.upgrade-to-v4-notice p a {
		font-weight: 500;
		text-decoration: none;
		white-space: nowrap;
	}

	.upgrade-to-v4-notice .snapshot-upgrade-to-v4-notice-dismiss {
		position: absolute;
		top: 10px;
		right: 10px;
		width: 30px;
		height: 30px;
		background: #f8f8f8;
		border-radius: 4px;
		cursor: pointer;
		text-align: center;
		transition: all 0.3s ease;
	}
	.upgrade-to-v4-notice .snapshot-upgrade-to-v4-notice-dismiss:hover {
		background: #f2f2f2;
	}
	.upgrade-to-v4-notice .snapshot-upgrade-to-v4-notice-dismiss .wps-icon {
		color: #aaaaaa;
		font-size: 7px;
		line-height: 32px !important;
		transition: all 0.3s ease;
	}
	.upgrade-to-v4-notice .snapshot-upgrade-to-v4-notice-dismiss:hover .wps-icon {
		color: #888888;
	}
</style>
<section class="wpmud-box upgrade-to-v4-notice" style="border-left: 2px solid #17a8e3; position: relative;">
	<div class="wpmud-box-content" style="min-height: 200px;">
		<form>
			<?php wp_nonce_field( 'snapshot_admin_notice_v4', '_wpnonce-snapshot_admin_notice' ); ?>
			<div class="row">
				<div style="position: relative;">
					<div style="position: absolute; left: 15px; width: 118px; height: 111px; border-radius: 4px; background-color: #c7c2f2; background-repeat: no-repeat; background-position: center 6px; background-image: url('<?php echo esc_attr( $bg_image_url ); ?>');"></div>
					<div style="padding-left: 150px; padding-right: 30px;">
						<div style="min-height: 110px;">
							<p style="font-size: 15px; font-weight: bold; line-height: 22px; color: #333333; padding-right: 45px;"><?php esc_html_e( 'Introducing our New Snapshot Engine with Incremental Backups!', SNAPSHOT_I18N_DOMAIN ); ?></p>
							<p style="font-size: 13px; line-height: 22px; color: #333333;"><?php esc_html_e( 'Snapshot 4 has been rebuilt from the ground up and features a brand new incremental backup engine. Snapshot 4 also integrates with more third-party storage solutions, including Google Drive, Dropbox, FTP/SFTP, and Amazon S3, as well as S3 Compatible Storage options such as Backblaze, Google Cloud, Digital Ocean, and more.', SNAPSHOT_I18N_DOMAIN ); ?></p>
							<p style="font-size: 13px; line-height: 22px; color: #333333;"><?php esc_html_e( 'Upgrade to Snapshot 4 today, and let us know if you have any feature requests. Please note that while Snapshot v3 and v4 can be used side-by-side, Snapshot 4 is now our only focus.', SNAPSHOT_I18N_DOMAIN ); ?></p>
							<?php
								$href_about_v4 = 'https://wpmudev.com/docs/wpmu-dev-plugins/snapshot-4-0/?utm_source=snapshot&utm_medium=plugin-upgrade&utm_campaign=snapshot-plugin-upgrade#faq';
							?>
							<p style="font-size: 13px; line-height: 22px; color: #333333;"><?php echo wp_kses_post( sprintf( __( 'Unsure about upgrading? Learn more about the new engine in our <a href="%s" target="_blank">Snapshot 4 migration Q&A</a>.', SNAPSHOT_I18N_DOMAIN ), $href_about_v4 ) ); ?></p>
						</div>
						<div style="margin-top: 10px;">
							<button class="button button-blue button-small snapshot-install-v4-button"><?php esc_html_e( 'Upgrade', SNAPSHOT_I18N_DOMAIN ); ?></button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>

	<div class="snapshot-upgrade-to-v4-notice-dismiss">
		<i class="wps-icon i-check"></i>
	</div>
</section>
