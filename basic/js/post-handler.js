/**
 * 
 * 
 * 
 * 
*/

$(document).ready(function() {

	$submitButton	= $("BUTTON[id=form_submit]");
	$form 			= $("#sendicate-form-subscription");
	$responseWrapper= $("#sendicate-response");

	$submitButton.click(function(e,ee){
		e.preventDefault();

		/**
		 * Do some stuffs to validate your data !
		 */ 

		// Clean up response wrapper if we made a previous call...
		$responseWrapper.empty();
		

		$.ajax({
			url: $form.attr("action")
			,type: 'POST'
			,data: {
				list_id: $("#sendicate_list_id").val()
				,subscriber_name: $("#subscriber_name").val()
				,subscriber_email: $("#subscriber_email").val()
				/**
				 * Uncoment this line to add another field
				 * NEW_FIELDNAME = Name of the field which you'll use in $_POST['NEW_FIELDNAME'] to access.
				 * id_of_the_form_field = Id of the field within your index.html form.
				 */
				//,NEW_FIELDNAME: $("#id_of_the_field").val()
			},

		})
		.done(function(response){
			// Show response in frontend
			$responseWrapper.html(response);

			// Reseting input fields
			$form.find('INPUT[type=text]').val('');
		});
		;

	});
});