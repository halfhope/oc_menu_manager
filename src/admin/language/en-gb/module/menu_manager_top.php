<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

// Heading
$_['heading_title']     = 'Menu manager | Top menu';

// Text
$_['text_extension']    = 'Extensions';
$_['text_edit']         = 'Edit top menu';

$_['text_edit_item']    = 'Edit';
$_['text_view_item']    = 'View';
$_['text_remove_item']  = 'Delete';
$_['text_add_sub_item'] = 'Add';
$_['text_copy']         = 'Copy';
$_['text_target']       = 'Open in a new tab';
$_['text_js']           = 'Execute JavaScript';

$_['text_top_menu']     = 'Current state';
$_['text_preset_menu']  = 'Scanned sections';

// Placeholder
$_['placeholder_name']  = 'Name';
$_['placeholder_link']  = 'Link';
$_['placeholder_icon']  = 'Icon';
$_['placeholder_route'] = 'Route. E.g. catalog/product/edit';

// Modal
$_['modal_js_title']    = 'JavaScript';
$_['modal_js_save']     = 'Save';
$_['modal_js_cancel']   = 'Cancel';
$_['modal_js_clear']    = 'Clear';
$_['modal_fa_title']    = 'Select icon';
$_['modal_fa_search']   = 'Search';

// Button
$_['button_expand']     = 'Expand all';
$_['button_collapse']   = 'Collapse all';
$_['button_add']        = 'Add';
$_['button_reset']      = 'Reset';
$_['button_js']         = 'JS';

// Help
$_['top_menu_help']     = '
In links and JS you can use shortcodes:
<ul>
     <li><b>[product_id]</b>, <b>[user_token]</b> - any $_GET parameters of current page</li>
     <li><b>[config|config_language_id]</b> - store settings</li>
     <li><b>[link|common/home]</b> - generated links</li>
</ul>
In the root elements (marked in blue), separated by commas, you can specify the routes in which their child menu items will be displayed. If you leave this field blank, menu items will be available in all sections. <br />You can also use a separator and title for menu items. To do this, instead of the menu item link, specify:
<ul>
     <li><b>#divider - menu separator</b></li>
     <li><b>#header  - menu header</b></li>
</ul>
Additionally, links can be opened in a pop-up window. Enter the code <b>mmModal(event, true);</b> in JS to accomplish this. It is necessary to include the event parameter. You can cut and remove the top and left menus from the loaded page code by using the second parameter. either true or false.
';

// Error
$_['error_permission']  = 'Warning: You do not have permission to modify module!';

// Success
$_['text_success']      = 'Success: You have modified module!';