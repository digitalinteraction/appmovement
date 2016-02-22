/** Dashboard Javascript **/

function showModal(movement) {
	// Set modal title
	$('#modalTitle').html( 'Promote : ' + $('#movement-title-' + movement).html() );

	// Show modal
	$('#support-modal').modal('show');
}