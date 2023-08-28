"use strict";
jQuery(document).ready(function(){
	jQuery(document).on('click', '.wpf-notice-dismis .notice-dismiss', function(){
		jQuery.sendFormWpf({
			data: {mod: 'overview', action: 'dismissNotice', 'slug': jQuery(this).closest('.wpf-notice-dismis').attr('data-disslug')}
		});
	});
});