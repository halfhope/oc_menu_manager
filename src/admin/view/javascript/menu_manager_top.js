document.onreadystatechange = function () {
	if (document.readyState == "interactive") {

	function makeMenu(menu_data) {
		let html = '';
		html += '<ul id="header_top_menu" class="nav navbar-nav">';
		for (const [key, value] of Object.entries(menu_data)) {
			html += makeMenuItem(value, 1);
		}
		html += '</ul>';
		return html;
	}

	function makeMenuItem(item, level) {
		let html = '';
		let js = ((item.js == undefined || item.js == '') ? '' : 'data-js="' + item.id + '"');
		let href = ((item.href == '') ? '#' : item.href);
		let target = ((item.target) ? '_blank' : '_self');
		let icon = ((item.icon == '') ? '' : '<i class="fa ' + item.icon + ' fa-fw"></i> ');
		let caret = (level > 1) ? 'right' : 'down';

		if (item.children !== undefined && Object.keys(item.children).length) {
			html += '<li class="nav-item dropdown level' + level + '">';
			html += '<a ' + js + ' href="' + href + '" target="' + target + '" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">' + icon + item.name + ' <i class="fa fa-caret-' + caret + ' fa-fw"></i></a>';
			html += '<ul class="dropdown-menu dropdown-menu-start level' + level + '">';
			for (const [sub_index, sub_item] of Object.entries(item.children)) {
				html += makeMenuItem(sub_item, level + 1);
			}
			html += '</ul>';
			html += '</li>';
		} else if (href == '#divider') {
			html += '<li><hr class="dropdown-divider"></li>';
		} else if (href == '#header') {
			html += '<li><h6 class="dropdown-header">' + icon + item.name + '</h6></li>';
		} else {
			html += '<li class="nav-item"><a ' + js + ' href="' + href + '" target="' + target + '" class="nav-link dropdown-item">' + icon + item.name + '</a></li>';
		}
		return html;
	}

	if (topMenuData !== undefined) {
		document.querySelector('#header a:first-child').insertAdjacentHTML('afterend', makeMenu(topMenuData));
	}
	}
};
