/**
 * Screen Reader main controller class
 * @package SCREENREADER::plugins
 * @author JExtensions Store
 * @copyright (C) 2016 - JExtensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
//'use strict';
(function($) {
	var MainController = function(options) {
		/**
		 * Plugin options
		 * 
		 * @access private
		 * @var Object
		 */
		var screenReaderOptions = {};
		
		/**
		 * TTS instance
		 * 
		 * @access private
		 * @var Object
		 */
		var frTTSEngineInstance = {};
		
		/**
		 * Font size default as starting point
		 * 
		 * @access private
		 * @var Int
		 */
		var fontSizeDefault = 100;
		
		/**
		 * Font size currently set
		 * 
		 * @access private
		 * @var Int
		 */
		var fontSizeCurrent = 80;
		
		/**
		 * Set a variable for the current state of high contrast
		 * 
		 * @access private
		 * @var Int
		 */
		var highContrast = 0;
		
		/**
		 * Set a variable for the current state of alternate contrast
		 * 
		 * @access private
		 * @var Int
		 */
		var highContrastAlternate = 0;
		
		/**
		 * Keep track of the original HTML element styles if any
		 * 
		 * @accee private
		 * @var String
		 */
		var originalHTMLStyles = '';
		
		/**
		 * Manage the appliance of the font resizing for headers element with an increment to preserve proportions
		 * 
		 * @access private
		 * @return Void
		 */
		var fontResizeHeaders = function() {
			// Filter header elements h1, h2, h3, h4
    		$(screenReaderOptions.fontsizeSelector).filter(function(index){
    			var elementNodeName = this.nodeName.toLowerCase();
    			var enlargedElement = false;
    			var enlargementValue = 0;
    			var headersReduction = 1;
    			switch (elementNodeName) {
    				case 'h1':
    					enlargementValue = 100;
    					enlargedElement = true;
    					break;
    				
    				case 'h2':
    					enlargementValue = 80;
    					headersReduction = 0.8;
    					enlargedElement = true;
	    				break;
	    				
    				case 'h3':
    					enlargementValue = 40;
    					headersReduction = 0.6;
    					enlargedElement = true;
	    				break;
	    				
    				case 'h4':
    					enlargementValue = 20;
    					headersReduction = 0.4;
    					enlargedElement = true;
	    				break;
    			}
    			// Found a header element to enlarge?
    			if(enlargedElement) {
    				if(screenReaderOptions.fontSizeOverride) {
		    			$(this).attr('style', 'font-size:' + (parseInt(fontSizeCurrent) + (screenReaderOptions.fontSizeHeadersIncrement * headersReduction) + enlargementValue) + '%');
		    		} else {
		    			// Append to the current inline style of the element
		    			var currentInlineStyles = $(this).attr('style') || '';
		    			$(this).attr('style', currentInlineStyles + ';font-size:' + (parseInt(fontSizeCurrent) + (screenReaderOptions.fontSizeHeadersIncrement * headersReduction) + enlargementValue) + '%');
		    		}
    			}
    		}); 
		};
		
		/**
		 * Manage the appliance of the alternate high contrast mode
		 * 
		 * @access private
		 * @param Object jqEvent
		 * @return Void
		 */
		var applyAlternateHighContrast = function(jqEvent){
			// Get alternate ig contrast value
			highContrastAlternate = parseInt(window.sessionStorage.getItem('scr_highcontrast_alternate')) || 0;
			
			// Appliance function
			var applianceFunction = function() {
				// Always reset previous default states
				$('html').removeClass('scr_highcontrast');
				highContrast = 0;
				window.sessionStorage.setItem('scr_highcontrast', highContrast);
				
				// Get original styles
				if(!originalHTMLStyles) {
					originalHTMLStyles = $('html').attr('style');
				}
				
				// Get a hue rotate value if any
				var hueRotate = '';
				if($(this).data('rotate')) {
					hueRotate = ' hue-rotate(' + parseInt($(this).data('rotate')) + 'deg)';
				}
				
				// Get a brightness value if any
				var brightness = '';
				if($(this).data('brightness')) {
					brightness = ' brightness(' + parseInt($(this).data('brightness')) + ')';
				}
				
				// Apply stylesheets programmatically
				$('html').attr('style', originalHTMLStyles + ';-webkit-filter: invert(100%)' + hueRotate + brightness + ';filter: invert(100%)' + hueRotate + brightness);
			}
			
			if(!highContrastAlternate) {
				applianceFunction.bind(this)();
				highContrastAlternate = $(this).data('alternate');
			} else if(highContrastAlternate && typeof(jqEvent) !== 'undefined'){
				// Is the same alternate high contrast or the other one?
				var currentClickedHighContrastAlternate = $(this).data('alternate');
				if(currentClickedHighContrastAlternate == highContrastAlternate) {
					// Disable it, exit from state
					// Recover original stylesheets
					$('html').attr('style', originalHTMLStyles);
					highContrastAlternate = 0;
				} else {
					highContrastAlternate = currentClickedHighContrastAlternate;
					applianceFunction.bind(this)();
				}
			} else if(highContrastAlternate && typeof(jqEvent) === 'undefined') {
				applianceFunction.bind(this)();
			}
			
			if(window.sessionStorage) { 
		    	window.sessionStorage.setItem('scr_highcontrast_alternate', highContrastAlternate);
			}
		}
		
		/**
		 * Build Screen Reader interface
		 * 
		 * @access private
		 * @param String hexnum
		 * @return String
		 */
		var applyColorsInversion = function(hexnum, isForeGround) {

			hexnum = hexnum.toUpperCase();
			var splitnum = hexnum.split("");
			var resultnum = "";
			var simplenum = "FEDCBA9876".split("");
			var complexnum = new Array();
			complexnum.A = "5";
			complexnum.B = "4";
			complexnum.C = "3";
			complexnum.D = "2";
			complexnum.E = "1";
			complexnum.F = "0";

			for (i = 0; i < 6; i++) {
				if (!isNaN(splitnum[i])) {
					resultnum += simplenum[splitnum[i]];
				} else if (complexnum[splitnum[i]]) {
					resultnum += complexnum[splitnum[i]];
				} else {
					//console.log(hexnum + ' no conversion');
					return false;
				}
			}
			
			if(isForeGround && screenReaderOptions.ieHighContrast) {
				var originalIntColor = parseInt(hexnum, 16);
				var newIntColor = parseInt(resultnum, 16);
				if(newIntColor < originalIntColor)
				return hexnum;
			}

			return resultnum;

		};
		
		/**
		 * Convert rgb color to hex
		 * 
		 * @access private
		 * @param String
		 *            $rgbcolor
		 * @return String
		 */
		var convertRGBDecimalToHex = function(rgb) {
		    var regex = /rgba? *\( *([0-9]{1,3}) *, *([0-9]{1,3}) *, *([0-9]{1,3})(, *[0-9]{1,3}[.0-9]*)? *\)/;
		    var values = regex.exec(rgb);
		    if(!values) {
		        return 'FFFFFF'; // fall back to white   
		    }
		    var r = Math.round(parseFloat(values[1]));
		    var g = Math.round(parseFloat(values[2]));
		    var b = Math.round(parseFloat(values[3]));
		    var hexColor = (r + 0x10000).toString(16).substring(3).toUpperCase() 
				         + (g + 0x10000).toString(16).substring(3).toUpperCase()
				         + (b + 0x10000).toString(16).substring(3).toUpperCase();
		    
		    return hexColor;
		};
		
		/**
		 * Detect if browser has native support for CSS3 filters
		 * 
		 * @access private
		 * @param String selector
		 * @return String
		 */
		var css3FilterFeatureDetect = function(enableWebkit) {
			var prefixes = ' -webkit- -moz- -o- -ms- '.split(' ');
		    var el = document.createElement('div');
		    el.style.cssText = prefixes.join('filter' + ':invert(100%); ');
		    return !!el.style.length && ((document.documentMode === undefined || document.documentMode > 9));
		};
		
		/**
		 * Start the legacy IE color inversion
		 * 
		 * @access private
		 * @param String selector
		 * @return String
		 */
		var startLegacyHighcontrast = function(selector) {
			var collectionOfElements = $(selector);
			
			$.fn.skipElement = function(style) {
			    var original = this.css(style);
			    this.css(style, 'inherit');
			    var inherited = this.css(style);
			    if (original != inherited && convertRGBDecimalToHex(inherited) != '000000') {
			    	this.css(style, inherited);
			    	return true;
			    } else {
			    	this.css(style, original);
			    	return false;
			    }
			}
			
			$(selector).each(function(index, HTMLElement){
				// Skip untraslated elements
				if($.inArray(HTMLElement.nodeName.toLowerCase(), ['script', 'embed', 'object']) == -1) {
					// Advanced mode for !important inline styling without the skipElemen algo
					if(screenReaderOptions.ieHighContrastAdvanced) {
						// Get rgb original color
						var backgroundColor = $(HTMLElement).css('background-color');
						var foregroundColor = $(HTMLElement).css('color');
						
						if(backgroundColor && foregroundColor) {
							// Invert to hex colors
							var invertedBackgroundColor = applyColorsInversion(convertRGBDecimalToHex(backgroundColor));
							var invertedForegroundColor = applyColorsInversion(convertRGBDecimalToHex(foregroundColor), true);
							
							// Now apply calculated inverted colors to the target element
							var currentStyle = $(HTMLElement).attr('style') || '';
							var newStyle = currentStyle + ' background-color:#' + invertedBackgroundColor + ' !important; color:#' + invertedForegroundColor + ' !important';
							$(HTMLElement).attr('style', newStyle);
						}
					} else {
						if(!$(HTMLElement).skipElement('background-color')) {
							// Get rgb original color
							var backgroundColor = $(HTMLElement).css('background-color');
							var foregroundColor = $(HTMLElement).css('color');
							
							if(backgroundColor && foregroundColor) {
								// Invert to hex colors
								var invertedBackgroundColor = applyColorsInversion(convertRGBDecimalToHex(backgroundColor));
								var invertedForegroundColor = applyColorsInversion(convertRGBDecimalToHex(foregroundColor), true);
								
								// Now apply calculated inverted colors to the target element
								$(HTMLElement).css('background-color', '#' + invertedBackgroundColor);
								$(HTMLElement).css('color', '#' + invertedForegroundColor);
							}
						}
					}
				}
			});
		};
		
		/**
		 * Build Screen Reader interface
		 * 
		 * @access public
		 * @return Void
		 */
		this.buildInterface = function() {
			// Inject del container
			var mainContainer =  $('<div/>').attr('id', 'accessibility-links')
											.addClass(screenReaderOptions.position + ' ' + screenReaderOptions.scrolling);

			// Switch the target append mode
			if(screenReaderOptions.targetAppendMode == 'bottom') {
				mainContainer.appendTo(screenReaderOptions.targetAppendto);
			} else if(screenReaderOptions.targetAppendMode == 'top') {
				mainContainer.prependTo(screenReaderOptions.targetAppendto);
			}
											
			// Inject del left container per il testo
			var textDiv = $('<div/>').attr('id', 'text_plugin')
									 .attr('title', fr_screenreader)
					   				 .addClass('scbasebin screenreader text');
			if(!parseInt(screenReaderOptions.showlabel)) {
				textDiv.addClass('scr_nolabel');
			}
			mainContainer.append(textDiv);
			
			// Inject del middle container per il volume controls
			if(parseInt(screenReaderOptions.screenreader)) {
				var volumeDiv = $('<div/>').attr('id', 'volume_plugin')
				.addClass('scbasebin');
				mainContainer.append(volumeDiv);
			}
			
			// Inject del right container per lo switch speaker
			var speakerDiv = $('<div/>').attr('id', 'speaker_plugin')
			.addClass('scbasebin speaker');
			mainContainer.append(speakerDiv);
			
			// Inject del clearer
			var clearerDiv = $('<div/>').css('clear', 'both');
			mainContainer.append(clearerDiv);
			
			// Label
			if(parseInt(screenReaderOptions.showlabel)) {
				var labelScreenReader = $('<label/>').addClass('fr_label startapp')
				.text(fr_screenreader);
				textDiv.append(labelScreenReader);
			}
			
			if(parseInt(screenReaderOptions.screenreader)) {
				// Inject del play
				var playButton = $('<button/>').attr({'id':'fr_screenreader_play',
					'title':'Play',
					'accesskey':screenReaderOptions.accesskey_play})
					.addClass('pinnable');
				speakerDiv.append(playButton);
				
				// Inject del pause
				var pauseButton = $('<button/>').attr({'id':'fr_screenreader_pause',
					'title':'Pause',
					'accesskey':screenReaderOptions.accesskey_pause})
					.addClass('pinnable');
				speakerDiv.append(pauseButton);
				
				// Inject dello stop
				var stopButton = $('<button/>').attr({'id':'fr_screenreader_stop',
					'title':'Stop',
					'accesskey':screenReaderOptions.accesskey_stop})
					.addClass('pinnable');
				speakerDiv.append(stopButton);
			}
			
			if(screenReaderOptions.fontsize) {
				// Inject del font size +
				var increaseButton = $('<button/>').attr({'id':'fr_screenreader_font_increase',
														'title':fr_increase,
														'accesskey':screenReaderOptions.accesskey_increase,
														'data-value':'1'})
														.addClass('sizable');
				speakerDiv.append(increaseButton);
				
				// Inject del font size -
				var decreaseButton = $('<button/>').attr({'id':'fr_screenreader_font_decrease',
														'title':fr_decrease,
														'accesskey':screenReaderOptions.accesskey_decrease,
														'data-value':'-1'})
														.addClass('sizable');
				speakerDiv.append(decreaseButton);
				
				// Inject del font size reset
				var resetButton = $('<button/>').attr({'id':'fr_screenreader_font_reset',
													'title':fr_reset,
													'accesskey':screenReaderOptions.accesskey_reset,
													'data-value':'0'})
													.addClass('resizable');
				speakerDiv.append(resetButton);
			}
			
			var subtractWidthKonst = 0;
			if(!screenReaderOptions.screenreader) {
				subtractWidthKonst = 100;
			}

			if(parseInt(screenReaderOptions.highcontrast)) {
				// Inject del default high contrast
				var highContrast = $('<button/>').attr({'id':'fr_screenreader_highcontrast',
					'title':fr_highcontrast,
					'accesskey':screenReaderOptions.accesskey_highcontrast,
					'data-value':'0'});
				speakerDiv.append(highContrast);
				
				// Check if the alternate high contrast mode is enabled and supported
				var alternateHighContrast = parseInt(screenReaderOptions.highcontrastAlternate) && css3FilterFeatureDetect();
				if(alternateHighContrast) {
					// Init high contrast alternate
			    	highContrastAlternate = parseInt(window.sessionStorage.getItem('scr_highcontrast_alternate')) || 0;
			    	if(highContrastAlternate) {
			    		var targetHightContrastAlternateButtonMode = $('button[data-alternate=' + highContrastAlternate + ']');
			    		applyAlternateHighContrast.call(targetHightContrastAlternateButtonMode);
			    	}
				}
				
				// Rule the bar size width
				if(screenReaderOptions.fontsize && alternateHighContrast) {
					$('#speaker_plugin').css('width', parseInt(310 - subtractWidthKonst) + 'px');
				} else if(screenReaderOptions.fontsize){
					$('#speaker_plugin').css('width', parseInt(250 - subtractWidthKonst) + 'px');
				} else if(!screenReaderOptions.fontsize && alternateHighContrast) {
					$('#speaker_plugin').css('width', parseInt(220 - subtractWidthKonst) + 'px');
				}
			} else {
				if(screenReaderOptions.fontsize) {
					$('#speaker_plugin').css('width', parseInt(220 - subtractWidthKonst) + 'px');
				} else {
					$('#speaker_plugin').css('width', parseInt(100 - subtractWidthKonst) + 'px');
				}
			}
			
			// Manage background color for the toolbar or buttons only based on the template
			if(screenReaderOptions.template == 'custom.css') {
				$('#speaker_plugin button').css('background-color', screenReaderOptions.toolbarBgcolor);
				$('#accessibility-links').css('background', 'transparent');
			} else {
				$('#accessibility-links').css('background-color', screenReaderOptions.toolbarBgcolor);
			}
		};

		/**
		 * Start TTS engine
		 * 
		 * @access public
		 * @return Boolean
		 */
		this.startTTSEngine = function() {
			// Instance TTS
			frTTSEngineInstance = new jQuery.frTTSEngine(screenReaderOptions);  
			
			return true;
		};
		
		/**
		 * Add plugin buttons event listeners
		 * 
		 * @access private
		 * @return Void
		 */
		var addListeners = function() {
			// Register events for sizable
			$(document).on('click', '.sizable', function(jqEvent){
				var increment = parseInt($(this).data('value'));
				fontSizeCurrent = parseInt(fontSizeCurrent) + parseInt(increment * 5);

			    if(fontSizeCurrent > screenReaderOptions.fontsizeMax){
			    	fontSizeCurrent = screenReaderOptions.fontsizeMax;
			    } else if (fontSizeCurrent < screenReaderOptions.fontsizeMin){
			    	fontSizeCurrent = screenReaderOptions.fontsizeMin;
			    }
			    
			    $('body').css('font-size', fontSizeCurrent + '%');  
			    if(screenReaderOptions.fontsizeSelector) {
		    		// If overwrite global mode
		    		if(screenReaderOptions.fontSizeOverride) {
		    			$(screenReaderOptions.fontsizeSelector).attr('style', 'font-size:' + (parseInt(fontSizeCurrent) + 20) + '%');
		    		} else {
		    			// Append to the current inline style of the element
			    		var fontsizingElements = $(screenReaderOptions.fontsizeSelector);
			    		$.each(fontsizingElements, function(index, elem){
			    			var currentInlineStyles = $(elem).attr('style') || '';
			    			$(elem).attr('style', currentInlineStyles + ';font-size:' + (parseInt(fontSizeCurrent) + 20) + '%');
			    		});
		    		}
		    		
		    		// Manage header resize increment
		    		fontResizeHeaders();
		    	}
			    if(window.sessionStorage) { 
			    	window.sessionStorage.setItem('scr_fontsize', fontSizeCurrent);
				} 
			});
				
			// Register events for reset
			$(document).on('click', '.resizable', function(jqEvent){
				fontSizeCurrent = fontSizeDefault;
				$('body').css('font-size', fontSizeDefault + '%');  
				if(screenReaderOptions.fontsizeSelector) {
		    		// If overwrite global mode
		    		if(screenReaderOptions.fontSizeOverride) {
		    			$(screenReaderOptions.fontsizeSelector).attr('style', 'font-size:' + (parseInt(fontSizeCurrent) + 20) + '%');
		    		} else {
		    			// Append to the current inline style of the element
			    		var fontsizingElements = $(screenReaderOptions.fontsizeSelector);
			    		$.each(fontsizingElements, function(index, elem){
			    			var currentInlineStyles = $(elem).attr('style') || '';
			    			$(elem).attr('style', currentInlineStyles + ';font-size:' + (parseInt(fontSizeCurrent) + 20) + '%');
			    		});
		    		}
		    		
		    		// Manage header resize increment
		    		fontResizeHeaders();
		    	}
			    if(window.sessionStorage) { 
			    	window.sessionStorage.setItem('scr_fontsize', fontSizeDefault);
				}
			});
			
			// Register events for high contrast button
			$(document).on('click', '#fr_screenreader_highcontrast', function(jqEvent){
				highContrast = !!parseInt(window.sessionStorage.getItem('scr_highcontrast')) || 0;
				if(!highContrast) {
					if(highContrastAlternate) {
						// Recover original stylesheets
						$('html').attr('style', originalHTMLStyles);
						highContrastAlternate = 0;
						if(window.sessionStorage) { 
							window.sessionStorage.setItem('scr_highcontrast_alternate', highContrastAlternate);
						}
					}
					
					$('html').addClass('scr_highcontrast');  
					
					// If IE detected, fallback on the compatibility inversion color high contrast
					if("ActiveXObject" in window) {
						startLegacyHighcontrast('body, body *:not(#accessibility-links, #accessibility-links *, div.shapes)');
					}
					
					highContrast = 1;
				} else {
					$('html').removeClass('scr_highcontrast');
					
					// If IE detected, fallback on the compatibility inversion color high contrast
					if("ActiveXObject" in window) {
						highContrast = 0;
						window.location.reload();
					}
					
					highContrast = 0;
				}
				
			    if(window.sessionStorage) { 
			    	window.sessionStorage.setItem('scr_highcontrast', highContrast);
				}
			});
			
			// Register events for alternate high contrast buttons, use only native CSS3 filters if available
			$(document).on('click', '#fr_screenreader_highcontrast2, #fr_screenreader_highcontrast3', applyAlternateHighContrast);
		};
		
		/**
		 * Function dummy constructor
		 * 
		 * @access private
		 * @param String
		 *            contextSelector
		 * @method <<IIFE>>
		 * @return Void
		 */
		(function __construct() {
			// Init status on load
			$.extend(screenReaderOptions, options);
			
			// ReInject if overridden
			if(jQuery.frTTSEngine === undefined) {
				jQuery('script[src*="tts.js"]').clone().appendTo('body');
			}
			
			// Ensure that the same background color is applied to both the body and html tag
			var bodyBGColor = $('body').css('background-color');
			$('html').css('background-color', bodyBGColor);

			// Init font sizing
			fontSizeDefault = fontSizeCurrent = screenReaderOptions.fontsizeDefault;
			if(window.sessionStorage) {
				var sessionFontSizeCurrent = window.sessionStorage.getItem('scr_fontsize');
				if(sessionFontSizeCurrent) {
					fontSizeCurrent = sessionFontSizeCurrent;
				}
				
				
				// Only if font sizing is enabled
				if(screenReaderOptions.fontsize) {
					$('body').css('font-size', fontSizeCurrent + '%');
				}
				
		    	if(screenReaderOptions.fontsizeSelector) {
		    		// If overwrite global mode
		    		if(screenReaderOptions.fontSizeOverride) {
		    			$(screenReaderOptions.fontsizeSelector).attr('style', 'font-size:' + (parseInt(fontSizeCurrent) + 20) + '%');
		    		} else {
		    			// Append to the current inline style of the element
			    		var fontsizingElements = $(screenReaderOptions.fontsizeSelector);
			    		$.each(fontsizingElements, function(index, elem){
			    			var currentInlineStyles = $(elem).attr('style') || '';
			    			$(elem).attr('style', currentInlineStyles + ';font-size:' + (parseInt(fontSizeCurrent) + 20) + '%');
			    		});
		    		}
		    		
		    		// Manage header resize increment
		    		fontResizeHeaders();
		    	}
		    	
		    	// Init high contrast
		    	highContrast = !!parseInt(window.sessionStorage.getItem('scr_highcontrast')) || 0;
		    	if(highContrast) {
		    		$('html').addClass('scr_highcontrast');
		    		
		    		// If IE detected, fallback on the compatibility inversion color high contrast
					if("ActiveXObject" in window) {
						startLegacyHighcontrast('body, body *:not(#accessibility-links, #accessibility-links *, div.shapes)');
					}
		    	}
			} 
			
			// Add listeners for UI
			addListeners();
		}).call(this);
	};
		
	//On DOM Ready
	$(function() {
		function scrDetectMobile() {
			var windowOnTouch = !!('ontouchstart' in window);
			var msTouch = !!(navigator.msMaxTouchPoints);
			var hasTouch = windowOnTouch || msTouch;
			return hasTouch;
		}
		if(parseInt(screenReaderConfigOptions.hideOnMobile) && scrDetectMobile()) {
			return;
		}
		
		// Instance controller
		window.ScreenReaderMainController = new MainController(screenReaderConfigOptions);
		
		// Build user interface
		ScreenReaderMainController.buildInterface();
		
		// Start engine once interface rendering has been completed
		ScreenReaderMainController.startTTSEngine();
	});
})(jQuery);