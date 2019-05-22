/**
 * Color picker
 * @package SCREENREADER::plugins
 * @author JExtensions Store
 * @copyright (C) 2016 - JExtensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
jQuery(document).ready(function($) {
	if (typeof ($.fn.wpColorPicker) !== 'undefined') {
		var $targetField = $('.my-color-field');
		var block = $targetField.parents('div.gppro-input');

		$targetField.wpColorPicker({
			palettes : true,
			change : function(event, ui) {
				var hexcolor = $(this).wpColorPicker('color');
				$(block).find('input.gppro-color-value').val(hexcolor);
			}
		});
	}
});