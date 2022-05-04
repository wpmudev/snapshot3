(function ($) {
	$(function () {

		$('.snapshot-upgrade-prompt').on('click', 'button.notice-dismiss', function () {
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'snapshot_admin_notice_v4_dismiss',
					_wpnonce: $('#_wpnonce-snapshot_admin_notice').val()
				}
			});
			$(this).closest('.notice').hide();
			return false;
		});

		$('.snapshot-upgrade-prompt-install').on('click', function () {
			var notice = $(this).closest('.notice');
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				beforeSend: function () {
					notice.find('.button').prop('disabled', true);
				},
				complete: function () {
					notice.find('.button').prop('disabled', false);
				},
				data: {
					action: 'snapshot_admin_notice_v4_install',
					_wpnonce: $('#_wpnonce-snapshot_admin_notice').val()
				},
				success: function (response) {
					if (response.success) {
						notice.hide();
						if (response.data.redirect_to) {
							window.location.href = response.data.redirect_to;
						}
					} else {
						if (response.data && typeof response.data === 'object') {
							for (var key in response.data) {
								console.error(key, response.data[key]);
							}
						}
					}
				}
			});
			return false;
		});

	});
})(jQuery);
