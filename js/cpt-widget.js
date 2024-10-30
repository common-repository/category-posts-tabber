/**
 * Plugin Category Posts Tabber
 * jQuery required
 */

jQuery(document).ready(function() {

	var tabItemClass 			= '.cpt-tab-item';
	var contentItemClass 	= '.cpt-tab-content';
	var current_tab 			= 'cpt-current-item';
	var current_content 	= 'cpt-current-content';

	jQuery(tabItemClass).click(function() {

		var me = jQuery(this);
		var widget = me.closest('.cpt-widget');

		if ( ! me.hasClass(current_tab) ) {

			var id_part = me.attr('id').split('-');
			var id = id_part[id_part.length - 1];
			
			widget.find(tabItemClass).removeClass(current_tab);

			me.addClass(current_tab);

			widget.find(contentItemClass).removeAttr('style').removeClass(current_content);

			widget.find('#cpt-content-' + id).addClass(current_content).fadeIn(300);
		}

		return false;

	});

});