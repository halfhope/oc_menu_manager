window.addEventListener('DOMContentLoaded', (event) => {
    
	function makeMenu(menu_data) {
		let html = '';
		html += '<ul id="header_top_menu" class="nav navbar-nav navbar-left">';
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

		if (item.children !== undefined && item.children.length) {
			html += '<li class="dropdown-submenu level' + level + '">';
			html += '<a ' + js + ' href="' + href + '" target="' + target + '" class="dropdown-toggle" data-toggle="dropdown">' + icon + item.name + ' <i class="fa fa-caret-down fa-fw"></i></a>';
			html += '<ul class="dropdown-menu level' + level + '">';
			for (const [sub_index, sub_item] of Object.entries(item.children)) {
				html += makeMenuItem(sub_item, level + 1);
			}
			html += '</ul>';
			html += '</li>';
		} else {
			html += '<li><a ' + js + ' href="' + href + '" target="' + target + '">' + icon + item.name + '</a></li>';
		}
		return html;
	}

	if (topMenuData !== undefined) {
		document.querySelector('#header-logo').insertAdjacentHTML('afterend', makeMenu(topMenuData));
	}

});