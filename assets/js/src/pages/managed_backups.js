(function ($) {

	// page ID or "slug"
	window.SS_PAGES.snapshot_page_snapshot_pro_managed_backups = function () {

		// Menu options
		$('.wps-menu-dots').each(function () {
			var $dots = $(this),
				$menu = $dots.parent('.wps-menu'),
				$all_menu = $('.wps-menu').not($menu);

			$dots.on('click', function () {
				$all_menu.removeClass('open');
				$menu[$menu.hasClass('open') ? 'removeClass' : 'addClass']('open');
			});
		});

		// Fixing dots menu z-index on backup list

		$("#snapshot-edit-listing table tr").each(function(index, el) {
			$(el).find('.msc-info').css('z-index', 1000 - index);
		});


		$('input[name="wps-managed-backups-menu"]').on('change', function (e) {
			var value = $(this).filter(':checked').val();
			$('select[name="wps-managed-backups-menu-mobile"]').val(value).trigger('change');
			$('.wps-managed-backups-pages > .wpmud-box').addClass('hidden').filter('.' + value).removeClass('hidden');
		});

		$('select[name="wps-managed-backups-menu-mobile"]').on('change', function (e) {
			var value = $(this).val();
			$('input[name="wps-managed-backups-menu"][value="' + value + '"]').prop('checked', true);
			$('.wps-managed-backups-pages > .wpmud-box').addClass('hidden').filter('.' + value).removeClass('hidden');
		});

		$("#wps-managed-backups-configure").on("click", function (e) {
			e.preventDefault();
			$("[for='wps-managed-backups-menu-config']").trigger('click');
			$('html,body').animate({
				scrollTop: $(".wps-managed-backups-configs").offset().top
			}, 'slow');
		});

		$("input[name='managed-backup-exclusions']").on('change', function () {
			var global_exclusions = jQuery("input[name='managed-backup-exclusions']:checked").val();
			if (global_exclusions === "global") {
				$('div#managed-backup-exclusions-config').slideUp();
			} else {
				$('div#managed-backup-exclusions-config').slideDown();

			}
		}).trigger('change');

		$("#my-backup-all").on('change', function () {
			$('input[id^="my-backup"]').prop('checked', $(this).is(':checked'));
		});

		$("#wps-managed-backups-onoff").on('change', function (e) {
			var form = $(this).parents('form');
			var enable = $(this).is(":checked");
			var hidden = $("#wps-managed-backups-onoff-hidden", form);
			if (enable) {
				hidden.attr('name', 'snapshot-enable-cron');
				$('.wps-managed-backups-schedule-form').removeClass('hidden');

			} else {
				hidden.attr('name', 'snapshot-disable-cron');
				$('.wps-managed-backups-schedule-form').addClass('hidden');
			}

			var data = form.serialize();

			//Save new backup setting using ajax
			jQuery.ajax({
				type: 'POST',
				url: form.attr('src'),
				data: data,
				success: function () {
				}
			});
		});

        /**
         * Handle managed backups schedule offsets
         *
         * Switches offset selection appearance based on frequency state.
         * Also toggles their respective enabled state.
         */
        function toggle_offset_visibility() {
            var $freq = $('#managed-backup-update select[name="frequency"]');
            if (!$freq.length) return false;

            $(".select-container.offset").hide().find("select").prop("disabled", true);
            $(".select-container #frequency").prop("disabled", false);
            $(".select-container #schedule_time").prop("disabled", false);
            var $el = $(".select-container.offset." + $freq.val());
            if ($el.length) $el.show().find("select").prop("disabled", false);

            // Handle the day label toggling.
            if ($freq.val() === 'weekly') {
                $(".offset-monthly-label").hide();
                $(".offset-weekly-label").show();
            } else if ($freq.val() === 'monthly') {
                $(".offset-weekly-label").hide();
                $(".offset-monthly-label").show();
            } else {
                $(".offset-monthly-label").hide();
                $(".offset-weekly-label").hide();
            }
        }

        $(window).on('load', toggle_offset_visibility);
        $(document).on('change', 'select[name="frequency"]', toggle_offset_visibility);

		window.SS_PAGES.snapshot_page_snapshot_pro_managed_backups_create();
		window.SS_PAGES.snapshot_page_snapshot_pro_managed_backups_upload();
	};

})(jQuery);
