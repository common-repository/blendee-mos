(function ($) {
	'use strict';

	$(document).ready(function () {
		// Aggiorna sync options
		$('#updateOptions').submit(function (event) {
			$('#blendee-loader-container').css('display', 'flex');
			event.preventDefault();

			let adapterOptions_64 = {};
			let active = document.getElementById('active').checked;
			let ref = document.getElementById('ref').value;
			let blendee_nonce = document.getElementById('blendee_nonce').value;
			let syncProds = document.getElementById('products') ? document.getElementById('products').checked : null;
			let syncCollections = document.getElementById('categories') ? document.getElementById('categories').checked : null;
			let syncOrders = document.getElementById('orders') ? document.getElementById('orders').checked : null;
			let syncCustomers = document.getElementById('users') ? document.getElementById('users').checked : null;

			let formData = new FormData();
			formData.append('action', 'blendee_mos_update_options');
			formData.append('ref', ref);
			formData.append('blendee_nonce', blendee_nonce);
			adapterOptions_64.enableTracking =  active;
			if (syncProds != null) adapterOptions_64.syncProds = syncProds;
			if (syncCollections != null) adapterOptions_64.syncCollections = syncCollections;
			if (syncOrders != null) adapterOptions_64.syncOrders = syncOrders;
			if (syncCustomers != null) adapterOptions_64.syncCustomers = syncCustomers;
			formData.append('adapterOptions_64', btoa(JSON.stringify(adapterOptions_64)));

			fetch(updateOptions.ajaxurl, {
				method: 'POST',
				body: formData
			})
				.then(response => response.text())
				.then(data => {
					console.log(data);
					$('#blendee-loader-container').hide();
				})
				.catch(error => {
					console.error('Errore durante la richiesta:', error);
					$('#blendee-loader-container').hide();
				});
		});

		$('#paste_ck').click(function (event) {
			navigator.clipboard.readText()
				.then(function (text) {
					$('#ck').val(text)
				})
				.catch(function (err) {
					console.error('Impossibile leggere il testo copiato', err);
				});
		})
		$('#paste_cs').click(function (event) {
			navigator.clipboard.readText()
				.then(function (text) {
					$('#cs').val(text)
				})
				.catch(function (err) {
					console.error('Impossibile leggere il testo copiato', err);
				});
		})

		$('#copy_button').click((event) => {
			var textToCopy = document.getElementById('textToCopy').innerText;
			navigator.clipboard.writeText(textToCopy);

			$('#copy_button').text('Copiato').addClass('copiato');
		});


	});

})(jQuery);