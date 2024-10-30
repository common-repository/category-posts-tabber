/**
 * Plugin Category Posts Tabber
 * jQuery required
 */

jQuery(document).on('click', function(e) {
	var $this = jQuery(e.target);

	if ($this.is('#select-tab-toggle')) {
		
		$this.parent().next('#cpt-select-tab').slideToggle();

	} else if ($this.is('#cpt-option-toggle')) {
		
		$this.parent().next('#cpt-option').slideToggle();

	} else if ($this.is('#add-tab')) {

		var html = $this.closest('#cpt-select-tab').find('#html-tab-wrapper').html();
		html = html.replace(/_sample/g, '');
		
		var $tab_list = $this.closest('#cpt-select-tab').find('#cpt-tab-list');
		$tab_list.append('<li>' + html + '</li>');
	
	} else if ($this.is('.remove-tab')) {

		if ( confirm('Are you sure?') ) {
			// Trigger to active save button
			$this.closest('.widget-content').find('.cpt-post-num').trigger('change');

			$this.closest('li').remove();
		}
		

	}

	return true;
});