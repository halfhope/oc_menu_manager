let isModalCreated = false;

function mmCreateModal() {
	let html =	'<div class="modal" id="mm_modal" tabindex="-1" aria-hidden="true" role="dialog">';
	html +=	'	<div class="modal-dialog moda`l-dialog-centered modal-xl" role="document">';
	html +=	'		<div class="modal-content">';
	html +=	'			<div class="modal-header">';
	html +=	'				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
	html +=	'				<h4 class="modal-title">&nbsp;</h4>';
	html +=	'			</div>';
	html +=	'			<div class="modal-body">';
	html +=	'				<div class="v-center">';
	html +=	'					<div class="loading-progress hidden"></div>';
	html +=	'				</div>';
	html += '				<iframe id="mm_iframe" frameborder="0" marginwidth="0" marginheight="0" allowfullscreen></iframe>';
	html +=	'			</div>';
	html +=	'		</div>';
	html +=	'	</div>';
	html +=	'</div>';

	if (!isModalCreated) {
		document.body.insertAdjacentHTML('afterend', html);
		isModalCreated = true;
	}

	let $modal = document.querySelector('#mm_modal');

	return $modal;
}

function onLoadIframe(event, cropMenus) {
	if (cropMenus) {
		"header#header,nav#column-left".split(',').forEach((selector) => {
			event.target.contentWindow.document.querySelector(selector).remove();
		});
	}
	
	event.target.classList.remove('hidden');
	document.querySelector('#mm_modal .loading-progress').classList.add('hidden');

	let iframeContentHeight = event.target.contentWindow.document.body.scrollHeight;
	let modalMaxHeight = Math.round((window.innerHeight/100) * 75);
	let iframeHeight = (iframeContentHeight >= modalMaxHeight) ? modalMaxHeight : iframeContentHeight;

	event.target.style.height = iframeHeight + 'px';
	
	document.querySelector('#mm_modal h4.modal-title').innerHTML = event.target.contentWindow.document.title;
}

function mmModal(event, cropMenus = false) {
	event.stopPropagation();

	let modal = mmCreateModal();
	let iframe = document.querySelector('#mm_modal #mm_iframe');

	iframe.setAttribute('src', event.target.getAttribute('href'));
	
	iframe.classList.add('hidden');
	modal.querySelector('.loading-progress').classList.remove('hidden');

	iframe.addEventListener('load', (event) => {
		onLoadIframe(event, cropMenus);
	});

	document.querySelector('#mm_modal h4.modal-title').innerHTML = '&nbsp;';

	$(modal).modal();
}