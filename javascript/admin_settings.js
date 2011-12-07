;(function($) {
	$(document).ready(function()
	{
		// Match all labels with a title tag and use it as the content (default).
		$('table.form-table label[title]').qtip({
			position : {
				my : "left center",
				at: "right center",
				target: "mouse"
			}
		});
	});
}(jQuery));