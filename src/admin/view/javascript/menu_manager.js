let isModalCreated = false;

function mmCreateModal() {

	let html = '<div class="modal" id="mm_modal" tabindex="-1">';
	html += '  <div class="modal-dialog modal-xl">';
	html += '    <div class="modal-content">';
	html += '      <div class="modal-header">';
	html += '        <h5 class="modal-title"></h5>';
	html += '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
	html += '      </div>';
	html += '      <div class="modal-body">';
	html += '           <div class="d-flex justify-content-center">';
	html += '           	<div class="spinner-border loading-progress d-none" role="status">';
	html += '           	    <span class="visually-hidden">Loading...</span>';
	html += '           	</div>';
	html += '           </div>';
	html += '		    <iframe id="mm_iframe" frameborder="0" marginwidth="0" marginheight="0" allowfullscreen></iframe>';
	html += '      </div>';
	html += '    </div>';
	html += '  </div>';
	html += '</div>';

	if (!isModalCreated) {
		document.body.insertAdjacentHTML('beforeend', html);
		isModalCreated = true;
	}

	return document.querySelector('#mm_modal');
}

function onLoadIframe(event, cropMenus) {
	if (cropMenus) {
		"header#header,nav#column-left".split(',').forEach((selector) => {
			event.target.contentWindow.document.querySelector(selector).remove();
		});
	}

	event.target.classList.remove('d-none');
	document.querySelector('#mm_modal .loading-progress').classList.add('d-none');
	
	let iframeContentHeight = event.target.contentWindow.document.body.scrollHeight;
	let modalMaxHeight = Math.round((window.innerHeight/100) * 75);
	let iframeHeight = (iframeContentHeight >= modalMaxHeight) ? modalMaxHeight : iframeContentHeight;

	event.target.style.height = iframeHeight + 'px';
	
	document.querySelector('#mm_modal h5.modal-title').innerHTML = event.target.contentWindow.document.title;
}

function mmModal(event, cropMenus = false) {
	event.stopPropagation();

	let modal = mmCreateModal();
	let iframe = document.querySelector('#mm_modal #mm_iframe');

	iframe.setAttribute('src', event.target.getAttribute('href'));
	
	iframe.classList.add('d-none');
	modal.querySelector('.loading-progress').classList.remove('d-none');

	iframe.addEventListener('load', (event) => {
		onLoadIframe(event, cropMenus);
	});

	document.querySelector('#mm_modal h5.modal-title').innerHTML = '&nbsp;';

	new bootstrap.Modal(modal).show();
}

