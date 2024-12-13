$(document).ready(function() {

	function makeMenu(menu_data) {
		var html = '';
		html += '<ul id="header_top_menu" class="nav navbar-nav navbar-left">';
		$.each(menu_data, function(index, item){
			html += makeMenuItem(item, 1);
		});
		html += '</ul>';
		return html;
	}

	function makeMenuItem(item, level) {
		var html = '';
		if (item.children !== undefined && item.children.length) {
			html += '<li class="dropdown-submenu level' + level + '">';
			html += '<a href="' + ((item.href == '') ? '#' : item.href) + '" target="' + ((item.target) ? '_blank' : '_self') + '" class="dropdown-toggle" data-toggle="dropdown">' + ((item.icon == '') ? '' : '<i class="fa ' + item.icon + ' fa-fw"></i> ') + item.name + ' <i class="fa fa-caret-down fa-fw"></i></a>';
			html += '<ul class="dropdown-menu level' + level + '">';
			$.each(item.children, function(sub_index, sub_item) {
				html += makeMenuItem(sub_item, level + 1);
			});
			html += '</ul>';
			html += '</li>';
		} else {
			html += '<li><a href="' + ((item.href == '') ? '#' : item.href) + '" target="' + ((item.target) ? '_blank' : '_self') + '">' + ((item.icon == '') ? '' : '<i class="fa ' + item.icon + ' fa-fw"></i> ') + item.name + '</a></li>';
		}
		return html;
	}

	if (topMenuData !== undefined) {
		$('#header .navbar-brand').after(makeMenu(topMenuData));
	}
});