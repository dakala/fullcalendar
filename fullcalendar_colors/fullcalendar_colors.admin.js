/**
 *  Bind the colorpicker event to the form element
 */

jQuery(document).ready(function(){

  var farb = jQuery.farbtastic('#colorpicker');

  // loop over each calendar_color type
  jQuery(".colorpicker-input").each(function() {
 
      // set the background colors of all of the textfields appropriately
      farb.linkTo(this);

      // when clicked, they get linked to the farbtastic colorpicker that they are associated with
      jQuery(this).click(function () {
        farb.linkTo(this);
	  });
  });
});
