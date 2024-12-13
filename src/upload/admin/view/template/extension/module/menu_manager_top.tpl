<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="submit" id="btn_save_form" form="form-module" data-toggle="tooltip" title="<?php echo $button_save ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
				<a href="<?php echo $cancel ?>" data-toggle="tooltip" title="<?php echo $button_cancel ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
			</div>
      		<h1><?php echo $heading_title; ?></h1>
			<ul class="breadcrumb">
			<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
			<?php } ?>
			</ul>
		</div>
	</div>
	<div class="container-fluid">
		<?php if ($error_warning) { ?>
			<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		<?php if ($success) { ?>
		<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> <?php echo $success ?>
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<script>
		$(document).ready(function() {
			setTimeout(function() {
				$('.alert.alert-success.alert-dismissible').remove()
			}, 3000);
		});
		</script>
		<?php } ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit ?></h3>
			</div>
			<div class="panel-body">
				<form action="<?php echo $action ?>" method="post" enctype="multipart/form-data" id="form-module" class="form-horizontal">
					<div class="col-sm-12 col-md-6">
					<?php if (isset($top_menu['top_menu_data'])) { ?>
						<input type="hidden" value="<?php echo htmlspecialchars($top_menu['top_menu_data']) ?>" name="top_menu_data">
						<div class="dd menu_manager" id="top_menu">
							<div class="panel mh35">
								<h3 class="pull-left"><?php echo $text_top_menu ?></h3>
								<div class="pull-right pnl">
									<span class="btn btn-success btn-sm btn_add"><?php echo $button_add ?></span>
									<span class="btn btn-primary btn-sm btn_expand"><?php echo $button_collapse ?></span>
									<span class="btn btn-warning btn-sm btn_reset"><?php echo $button_reset ?></span>
								</div>
							</div>
							<ol class="dd-list">
							</ol>
						</div>
					<?php } ?>
					<div class="help_block panel-heading panel-default">
						<?php echo $top_menu_help ?>
					</div>
					</div>
					<div class="col-sm-12 col-md-6">
						<input type="hidden" value="<?php echo htmlspecialchars($preset_menu) ?>" name="preset_data">
						<div class="dd menu_manager" id="preset_menu">
							<div class="panel mh35">
								<h3 class="pull-left"><?php echo $text_preset_menu ?></h3>
								<div class="pull-right pnl">
									<span class="btn btn-primary btn-sm btn_expand"><?php echo $button_expand ?></span>
								</div>
							</div>
							<ol class="dd-list">
							</ol>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function() {
	var topMenu = JSON.parse($('input[name=top_menu_data]').val());
	var presetMenu = JSON.parse($('input[name=preset_data]').val());

	var menu = buildMenu(topMenu);
	$('#top_menu .dd-list').html(menu);
	$.each($('#top_menu .dd-item'), function(index, value){
		assignEvents(value);
	});

	$('#top_menu').nestable({
		handleClass: 'dd-handle-btn',
		maxDepth: 4,
		callback: function(c, e) {
			recursiveUpdateVisiblity(e);
		}
	});

	var menu = buildPresetMenu(presetMenu);
	$('#preset_menu .dd-list').html(menu);
	$.each($('#preset_menu .dd-item'), function(index, value){
		assignEvents(value);
	});

	$('#preset_menu').nestable({
		handleClass: 'dd-handle-btn',
		maxDepth: 5,
		callback: function(c, e) {
			if ($(c).attr('id') == 'top_menu') {
				var item = {
					name: $(e).attr('data-name'),
					href: $(e).attr('data-href'),
					target: 1,
					icon: ''
				}
				var htmlItem = buildMenuItem(item);
				$(c).find(e).replaceWith(htmlItem);
				var newItem = $(c).find('.dd-item[data-id=' + itemCount + ']');
				assignEvents(newItem);
				recursiveUpdateVisiblity(newItem);
			}
			if ($(c).children('.dd-list').length) {
				$(c).children('.dd-list').each(function(index, value){
					if (!$(value).children('.dd-item').length) {
						$(value).remove();
					}
				});
			}
		}
	});

	$('#top_menu .btn_expand, #preset_menu .btn_expand').on('click', function() {
		if ($(this).html() == '<?php echo $button_expand ?>') {
			$(this).html('<?php echo $button_collapse ?>');
			$(this).closest('.menu_manager').find('.dd-expand').trigger('click');
		} else {
			$(this).html('<?php echo $button_expand ?>');
			$(this).closest('.menu_manager').find('.dd-collapse').trigger('click');
		}
	});
	
	$('#top_menu .btn_add').on('click', function() {
		var item = {name:'', href:'', target:1, icon:''};
		var htmlItem = buildMenuItem(item, 1);
		$('#top_menu .dd-list').first().prepend(htmlItem);
		assignEvents($('#top_menu .dd-list .dd-item').first());
	});

	$('#top_menu .btn_reset').on('click', function() {
		window.location.href = '<?php echo $reset ?>';
	});

	$('#form-module').submit(function(event){
		$('input[name=top_menu_data]').val(JSON.stringify($('#top_menu').nestable('serialize')));
		$('input').not('input[name=top_menu_data]').remove();
		return true;
	});
});

var itemCount = 0;
var currentInput;

function recursiveUpdateVisiblity(e) {
	itemLevel = getItemLevel(e);
	if (itemLevel == 1) {
		$(e).find('.input-group').first().addClass('hidden');
		$(e).find('input[name*=icon]').first().addClass('hidden');
		$(e).find('input[name*=name]').first().attr('placeholder', '<?php echo $placeholder_route ?>');
	} else {
		$(e).find('.input-group').first().removeClass('hidden');
		$(e).find('input[name*=icon]').first().removeClass('hidden');
		$(e).find('input[name*=name]').first().attr('placeholder', '<?php echo $placeholder_name ?>');
	}
	if (itemLevel == 5) {
		$(e).find('.btn_add_sub').first().addClass('hidden');
	} else {
		$(e).find('.btn_add_sub').first().removeClass('hidden');
	}
	if ($(e).find('.dd-list').first().length) {
		$(e).find('.dd-list:first > .dd-item').each(function(index, e2){
			recursiveUpdateVisiblity($(e2));
		});
	}
} 

function buildMenu(items) {
	var output = '';
	$.each(items, function (index, item) {
		output += buildMenuItem(item, 1);
	});
	return output;
}

function buildMenuItem(item, parentLevel) {
	itemCount = itemCount + 1;
	var html = '';
	var ddItemData = '';
	item['id'] = itemCount;
	for (var key in item) {
		if (key !== 'children') {
			ddItemData = ddItemData + 'data-' + key + '="' + item[key] + '" ';
		}
	}
	html += '<li class="dd-item" ' + ddItemData + '>';
	html += '  <button class="dd-handle-btn" type="button"></button>';
	html += '  <div class="dd-handle"><span class="caption">' + item.name + '</span>';
	html += '    <div class="pull-right dd-panel">';
	html += '      <span class="label label-success btn_add_sub ' + ((parentLevel == 5) ? 'hidden' : '') + '"><i class="fa fa-plus" data-toggle="tooltip" data-original-title="<?php echo $text_add_sub_item ?>"></i></span>';
	html += '      <span class="label label-primary btn_edit"><i class="fa fa-pencil" data-toggle="tooltip" data-original-title="<?php echo $text_edit_item ?>"></i></span></span>';
	html += '      <span class="label label-danger btn_remove"><i class="fa fa-remove" data-toggle="tooltip" data-original-title="<?php echo $text_remove_item ?>"></i></span>';
	html += '    </div>';
	html += '    <div class="dd-form">';
	var placeholder = (parentLevel == 1) ? '<?php echo $placeholder_route ?>' : '<?php echo $placeholder_name ?>';
	html += '      <input type="text" class="form-control input-sm" name="menus[' + itemCount + '][name]" value="' + item.name + '" title="' + placeholder + '" placeholder="' + placeholder + '">';
	html += '      <div class="input-group input-group-sm ' + ((parentLevel == 1) ? 'hidden' : '') + '">';
	html += '        <span class="input-group-btn">';
	html += '          <button class="btn btn-sm btn-default btn_copy" type="button"><i class="fa fa-copy"></i></button>';
	html += '        </span>';
	html += '        <input type="text" class="form-control input-sm" name="menus[' + itemCount + '][href]" value="' + item.href + '" title="<?php echo $placeholder_link ?>" placeholder="<?php echo $placeholder_link ?>">';
	html += '        <span class="input-group-addon">';
	html += '          <input type="checkbox" name="menus[' + itemCount + '][target]" data-toggle="tooltip" data-title="<?php echo $text_target ?>"' + ((item.target) ? ' checked="checked"' : '') + '>';
	html += '        </span>';
	html += '      </div>';
	html += '    <input type="text" class="form-control input-sm ' + ((item.icon == undefined || parentLevel == 1) ? 'hidden' : '') + '" name="menus[' + itemCount + '][icon]" value="' + ((item.icon == undefined) ? '' : item.icon) + '" title="<?php echo $placeholder_icon ?>" placeholder="<?php echo $placeholder_icon ?>" data-fa-browser >';
	html += '    </div>';
	html += '  </div>';
	if (item.children !== undefined && item.children.length > 0) {
		html += '<ol class="dd-list">';
		parentLevel = parentLevel + 1;
		$.each(item.children, function (index, sub) {
			html += buildMenuItem(sub, parentLevel);
		});
		html += '</ol>';
	}
	html += '</li>';
	return html;
}

function buildPresetMenuItem(item, parentLevel) {
	itemCount = itemCount + 1;
	var html = '';
	var ddItemData = '';
	for (var key in item) {
		if (key !== 'children') {
			ddItemData = ddItemData + 'data-' + key + '="' + item[key] + '" ';
		}
	}
	html += '<li class="dd-item ' + ((item['children'] !== undefined && item['children'].length > 0) ? 'dd-collapsed' : '') + '" ' + ddItemData + '>';
	html += '  <button class="dd-handle-btn" type="button"></button>';
	html += '  <div class="dd-handle"><span class="caption">' + item.name + '</span>';
	html += '    <div class="pull-right dd-panel">';
	html += '      <span class="label label-primary btn_edit ' + ((item.href == '') ? 'hidden' : '') + '"><i class="fa fa-search" data-toggle="tooltip" data-original-title="<?php echo $text_view_item ?>"></i></span></span>';
	html += '    </div>';
	html += '    <div class="dd-form">';
	html += '      <input type="text" class="form-control input-sm" name="menus[' + itemCount + '][name]" value="' + item.name + '" title="<?php echo $placeholder_name ?>" placeholder="<?php echo $placeholder_name ?>">';
	html += '      <div class="input-group input-group-sm ' + ((item.href == '') ? 'hidden' : '') + '">';
	html += '        <span class="input-group-btn">';
	html += '          <span class="btn btn-sm btn-default btn_copy"><i class="fa fa-copy"></i></span>';
	html += '        </span>';
	html += '        <input type="text" class="form-control input-sm" name="menus[' + itemCount + '][href]" value="' + item.href + '" title="<?php echo $placeholder_link ?>" placeholder="<?php echo $placeholder_link ?>">';
	html += '      </div>';
	html += '    <input type="text" class="form-control input-sm hidden" name="menus[' + itemCount + '][icon]" value="' + ((item.icon == undefined) ? '' : item.icon) + '" title="<?php echo $placeholder_icon ?>" placeholder="<?php echo $placeholder_icon ?>" data-fa-browser >';
	html += '    </div>';
	html += '  </div>';
	if (item.children !== undefined && item.children.length > 0) {
		html += '<ol class="dd-list">';
		parentLevel = parentLevel + 1;
		$.each(item.children, function (index, sub) {
			html += buildPresetMenuItem(sub, parentLevel);
		});
		html += '</ol>';
	}
	html += '</li>';
	return html;
}

function buildPresetMenu(items) {
	var output = '';
	$.each(items, function (index, item) {
		output += buildPresetMenuItem(item, 1);
	});
	return output;
}

function getItemLevel(item){
	return $(item).parents('.dd-item').length + 1;
}

function assignEvents(item) {

	$(item).find('.btn_add_sub').first().on('click', function() {
		var itemData = {name:'', href:'', target:1, icon:''};
		var parentLevel = getItemLevel($(this).closest('.dd-item'));
		var htmlItem = buildMenuItem(itemData, parentLevel + 1);

		if ($(this).closest('.dd-item').find('.dd-list').length) {
			$(this).closest('.dd-item').find('.dd-list').first().append(htmlItem);
		} else {
			$(this).closest('.dd-handle').after('<ol class="dd-list">' + htmlItem + '</ol>');
		}
		if ($(this).closest('.dd-item').hasClass('dd-collapsed')){
			$(this).closest('.dd-item').removeClass('dd-collapsed');
		}
		$(this).closest('.dd').nestable('setParent', $(this).closest('.dd-item'));

		assignEvents($(this).closest('.dd-item').find('.dd-list').first().find('.dd-item').last());
	});

	var editItem = function(event){
		$(event.target).closest('.dd-handle').find('.dd-form').toggle();
		$(event.target).closest('.dd-handle').find('.btn_edit > span.fa').toggleClass('fa-angle-up fa-angle-down');
	};

	$(item).find('.btn_edit').first().on('click', editItem);
	$(item).find('.caption').first().on('click', editItem);
	$(item).find('.dd-handle').first().on('dblclick', editItem).children().on('dblclick', function(){
		return false;
	});

	$(item).find('.btn_remove').first().on('click', function() {
		var id = $(this).closest('.dd-item').attr('data-id');
		$(this).closest('.dd').nestable('remove', parseInt(id));
		$(parent).closest('.dd').nestable();
	});

	$(item).find('input[name*=name]').first().on('input paste', function() {
		$(this).closest('.dd-handle').find('span.caption').first().html($(this).val());
		$(this).closest('.dd-item').attr('data-name', $(this).val());
	});

	$(item).find('input[name*=href]').first().on('input paste', function() {
		$(this).closest('.dd-item').attr('data-href', $(this).val());
	});

	$(item).find('input[name*=icon]').first().on('input paste', function() {
		$(this).closest('.dd-item').attr('data-icon', $(this).val());
	});

	$(item).find('input[name*=target]').first().on('click', function() {
		$(this).closest('.dd-item').attr('data-target', Number($(this).prop('checked')));
	});

	$(item).find('.btn_copy').first().on('click', function() {
		var self = this;
		if (navigator.clipboard == undefined) {
			console.log('Clipboard API work\'s only over https');
			return false;
		}
		navigator.clipboard.writeText($(self).closest('.input-group').find('input[name*=href]').val());
		var toggleCopyBtn = function() {
			$(self).toggleClass('btn-success btn-default').find('i.fa').toggleClass('fa-copy fa-check');
		}
		toggleCopyBtn();
		setTimeout(toggleCopyBtn, 1000);
	});

	$(item).find('input[data-fa-browser]').first().click(function() {
		currentInput = $(this);
		$('#fa-icon-picker').modal();
		$('#fa-icon-picker .icon.selected').removeClass('selected');
		$('#fa-icon-picker .icon i[class="fa ' + $(currentInput).val() + '"]').closest('.icon').addClass('selected');
		$('#fa-icon-picker input[name=search]').focus();
	});

	$(item).find('[data-toggle="tooltip"]').tooltip({container:'body'});
}
</script>
<script>
$(document).ready(function() {
	// fontawesome
	var icons = ["glass","music","search","envelope-o","heart","star","star-o","user","film","th-large","th","th-list","check","remove","close","times","search-plus","search-minus","power-off","signal","gear","cog","trash-o","home","file-o","clock-o","road","download","arrow-circle-o-down","arrow-circle-o-up","inbox","play-circle-o","rotate-right","repeat","refresh","list-alt","lock","flag","headphones","volume-off","volume-down","volume-up","qrcode","barcode","tag","tags","book","bookmark","print","camera","font","bold","italic","text-height","text-width","align-left","align-center","align-right","align-justify","list","dedent","outdent","indent","video-camera","photo","image","picture-o","pencil","map-marker","adjust","tint","edit","pencil-square-o","share-square-o","check-square-o","arrows","step-backward","fast-backward","backward","play","pause","stop","forward","fast-forward","step-forward","eject","chevron-left","chevron-right","plus-circle","minus-circle","times-circle","check-circle","question-circle","info-circle","crosshairs","times-circle-o","check-circle-o","ban","arrow-left","arrow-right","arrow-up","arrow-down","mail-forward","share","expand","compress","plus","minus","asterisk","exclamation-circle","gift","leaf","fire","eye","eye-slash","warning","exclamation-triangle","plane","calendar","random","comment","magnet","chevron-up","chevron-down","retweet","shopping-cart","folder","folder-open","arrows-v","arrows-h","bar-chart-o","bar-chart","twitter-square","facebook-square","camera-retro","key","gears","cogs","comments","thumbs-o-up","thumbs-o-down","star-half","heart-o","sign-out","linkedin-square","thumb-tack","external-link","sign-in","trophy","github-square","upload","lemon-o","phone","square-o","bookmark-o","phone-square","twitter","facebook-f","facebook","github","unlock","credit-card","feed","rss","hdd-o","bullhorn","bell","certificate","hand-o-right","hand-o-left","hand-o-up","hand-o-down","arrow-circle-left","arrow-circle-right","arrow-circle-up","arrow-circle-down","globe","wrench","tasks","filter","briefcase","arrows-alt","group","users","chain","link","cloud","flask","cut","scissors","copy","files-o","paperclip","save","floppy-o","square","navicon","reorder","bars","list-ul","list-ol","strikethrough","underline","table","magic","truck","pinterest","pinterest-square","google-plus-square","google-plus","money","caret-down","caret-up","caret-left","caret-right","columns","unsorted","sort","sort-down","sort-desc","sort-up","sort-asc","envelope","linkedin","rotate-left","undo","legal","gavel","dashboard","tachometer","comment-o","comments-o","flash","bolt","sitemap","umbrella","paste","clipboard","lightbulb-o","exchange","cloud-download","cloud-upload","user-md","stethoscope","suitcase","bell-o","coffee","cutlery","file-text-o","building-o","hospital-o","ambulance","medkit","fighter-jet","beer","h-square","plus-square","angle-double-left","angle-double-right","angle-double-up","angle-double-down","angle-left","angle-right","angle-up","angle-down","desktop","laptop","tablet","mobile-phone","mobile","circle-o","quote-left","quote-right","spinner","circle","mail-reply","reply","github-alt","folder-o","folder-open-o","smile-o","frown-o","meh-o","gamepad","keyboard-o","flag-o","flag-checkered","terminal","code","mail-reply-all","reply-all","star-half-empty","star-half-full","star-half-o","location-arrow","crop","code-fork","unlink","chain-broken","question","info","exclamation","superscript","subscript","eraser","puzzle-piece","microphone","microphone-slash","shield","calendar-o","fire-extinguisher","rocket","maxcdn","chevron-circle-left","chevron-circle-right","chevron-circle-up","chevron-circle-down","html5","css3","anchor","unlock-alt","bullseye","ellipsis-h","ellipsis-v","rss-square","play-circle","ticket","minus-square","minus-square-o","level-up","level-down","check-square","pencil-square","external-link-square","share-square","compass","toggle-down","caret-square-o-down","toggle-up","caret-square-o-up","toggle-right","caret-square-o-right","euro","eur","gbp","dollar","usd","rupee","inr","cny","rmb","yen","jpy","ruble","rouble","rub","won","krw","bitcoin","btc","file","file-text","sort-alpha-asc","sort-alpha-desc","sort-amount-asc","sort-amount-desc","sort-numeric-asc","sort-numeric-desc","thumbs-up","thumbs-down","youtube-square","youtube","xing","xing-square","youtube-play","dropbox","stack-overflow","instagram","flickr","adn","bitbucket","bitbucket-square","tumblr","tumblr-square","long-arrow-down","long-arrow-up","long-arrow-left","long-arrow-right","apple","windows","android","linux","dribbble","skype","foursquare","trello","female","male","gittip","gratipay","sun-o","moon-o","archive","bug","vk","weibo","renren","pagelines","stack-exchange","arrow-circle-o-right","arrow-circle-o-left","toggle-left","caret-square-o-left","dot-circle-o","wheelchair","vimeo-square","turkish-lira","try","plus-square-o","space-shuttle","slack","envelope-square","wordpress","openid","institution","bank","university","mortar-board","graduation-cap","yahoo","google","reddit","reddit-square","stumbleupon-circle","stumbleupon","delicious","digg","pied-piper","pied-piper-alt","drupal","joomla","language","fax","building","child","paw","spoon","cube","cubes","behance","behance-square","steam","steam-square","recycle","automobile","car","cab","taxi","tree","spotify","deviantart","soundcloud","database","file-pdf-o","file-word-o","file-excel-o","file-powerpoint-o","file-photo-o","file-picture-o","file-image-o","file-zip-o","file-archive-o","file-sound-o","file-audio-o","file-movie-o","file-video-o","file-code-o","vine","codepen","jsfiddle","life-bouy","life-buoy","life-saver","support","life-ring","circle-o-notch","ra","rebel","ge","empire","git-square","git","y-combinator-square","yc-square","hacker-news","tencent-weibo","qq","wechat","weixin","send","paper-plane","send-o","paper-plane-o","history","circle-thin","header","paragraph","sliders","share-alt","share-alt-square","bomb","soccer-ball-o","futbol-o","tty","binoculars","plug","slideshare","twitch","yelp","newspaper-o","wifi","calculator","paypal","google-wallet","cc-visa","cc-mastercard","cc-discover","cc-amex","cc-paypal","cc-stripe","bell-slash","bell-slash-o","trash","copyright","at","eyedropper","paint-brush","birthday-cake","area-chart","pie-chart","line-chart","lastfm","lastfm-square","toggle-off","toggle-on","bicycle","bus","ioxhost","angellist","cc","shekel","sheqel","ils","meanpath","buysellads","connectdevelop","dashcube","forumbee","leanpub","sellsy","shirtsinbulk","simplybuilt","skyatlas","cart-plus","cart-arrow-down","diamond","ship","user-secret","motorcycle","street-view","heartbeat","venus","mars","mercury","intersex","transgender","transgender-alt","venus-double","mars-double","venus-mars","mars-stroke","mars-stroke-v","mars-stroke-h","neuter","genderless","facebook-official","pinterest-p","whatsapp","server","user-plus","user-times","hotel","bed","viacoin","train","subway","medium","yc","y-combinator","optin-monster","opencart","expeditedssl","battery-4","battery-full","battery-3","battery-three-quarters","battery-2","battery-half","battery-1","battery-quarter","battery-0","battery-empty","mouse-pointer","i-cursor","object-group","object-ungroup","sticky-note","sticky-note-o","cc-jcb","cc-diners-club","clone","balance-scale","hourglass-o","hourglass-1","hourglass-start","hourglass-2","hourglass-half","hourglass-3","hourglass-end","hourglass","hand-grab-o","hand-rock-o","hand-stop-o","hand-paper-o","hand-scissors-o","hand-lizard-o","hand-spock-o","hand-pointer-o","hand-peace-o","trademark","registered","creative-commons","gg","gg-circle","tripadvisor","odnoklassniki","odnoklassniki-square","get-pocket","wikipedia-w","safari","chrome","firefox","opera","internet-explorer","tv","television","contao","500px","amazon","calendar-plus-o","calendar-minus-o","calendar-times-o","calendar-check-o","industry","map-pin","map-signs","map-o","map","commenting","commenting-o","houzz","vimeo","black-tie","fa-fonticons"];
	$.each(icons, function(index, value){
		$('#fa-icon-picker .search_container').append('<div class="icon" data-toggle="tooltip" data-original-title="fa-' + value + '"><i class="fa fa-' + value + '"></i></div>');
	});

	$('#fa-icon-picker [data-toggle="tooltip"]').tooltip({container:'body'});

	$('#fa-icon-picker input[name=search]').on('input paste', function() {
		var search = $(this).val();
		if (search !== '') {
			$('#fa-icon-picker .search_container .icon').addClass('hidden');
			$('#fa-icon-picker .search_container .icon i[class*=' + search + ']').closest('.icon').removeClass('hidden');
		} else {
			$('#fa-icon-picker .search_container .icon').removeClass('hidden');
		}
	});

	$('#fa-icon-picker .icon').on('click', function() {
		currentInput.val($(this).find('i').attr('class').replace(/fa /, ''));
		$('#fa-icon-picker').modal('hide');
		currentInput.trigger('input');
		$('#fa-icon-picker input[name=search]').val('').trigger('input');
	});
});
</script>
<div class="modal" id="fa-icon-picker" tabindex="-1" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><?php echo $modal_title ?></h4>
			</div>
			<div class="modal-body">
				<input type="text" class="form-control" value="" placeholder="<?php echo $modal_search ?>" name="search" autocomplete="off">
				<br />
				<div class="search_container"></div>
			</div>
		</div>
	</div>
</div>
<?php echo $footer ?>