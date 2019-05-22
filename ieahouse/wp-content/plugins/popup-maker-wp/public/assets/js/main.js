jQuery(document).ready(function($){

	var sgpmOptionsPanel = new SGPMOptionsPanel();
	sgpmOptionsPanel.init();

	$('.sgpm-tab-container').pwstabs({
		effect: 'slideleft',
		defaultTab: 1,
		containerWidth: '1000px',
		tabsPosition: 'vertical',
		verticalPosition: 'left'
	});

	jQuery('.refresh-popup-data-btn').on('click', function(event) {
		jQuery('#sgpm-form-api').submit();
	});

	sgpmAddSelectBoxValuesIntoInput();
});

function sgpmChangePopupStatus(popupId, popupStatus)
{
	var popupStatusSpam = jQuery('[data-sgpm-popup-id='+popupId+'] .sgpm-popup-status');
	var popupStatusLink = jQuery('[data-sgpm-popup-id='+popupId+'] a.sgpm-change-popup-status');

	if (popupStatus == 'enabled') {
		popupStatus = 'disabled';
	}
	else {
		popupStatus = 'enabled';
	}

	var data = {
		action: 'sgpm_change_popup_status',
		_ajax_nonce: SGPM_JS_PARAMS.nonce,
		popupId: popupId,
		popupStatus: popupStatus
	};

	jQuery.post(ajaxurl, data, function(response,d) {
		location.reload();
	});
}

function sgpmAddSelectBoxValuesIntoInput()
{
	var selectedPages = [];
	var selectedPosts = [];

	jQuery("#sgpm-form-options-save").submit(function(e) {
		var pages = jQuery("select[data-selectbox='sgpmSelectedPages'] > option:selected");
		var posts = jQuery("select[data-selectbox='sgpmSelectedPosts'] > option:selected");

		for(var i=0; i<pages.length; i++) {
			selectedPages.push(pages[i].value);
		}
		for(var i=0; i<posts.length; i++) {
			selectedPosts.push(posts[i].value);
		}

		jQuery(".sgpm-selected-pages").val(selectedPages);
		jQuery(".sgpm-selected-posts").val(selectedPosts);
	});
}

function sgpmToggle(className, inputValue)
{
	jQuery('.'+className).toggle(inputValue);
}
