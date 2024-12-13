<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

// Heading
$_['heading_title']     = 'Менеджер меню | Верхнее меню';

// Text
$_['text_extension']    = 'Расширения';
$_['text_edit']         = 'Настройки модуля';

$_['text_edit_item']    = 'Редактировать';
$_['text_view_item']    = 'Просмотр';
$_['text_remove_item']  = 'Удалить';
$_['text_add_sub_item'] = 'Добавить';
$_['text_copy']         = 'Копировать';
$_['text_target']       = 'Открывать в новой вкладке';
$_['text_js']           = 'JavaScript';

$_['text_top_menu']     = 'Текущее состояние';
$_['text_preset_menu']  = 'Разделы магазина';

// Placeholder
$_['placeholder_name']  = 'Имя';
$_['placeholder_link']  = 'Ссылка';
$_['placeholder_icon']  = 'Иконка';
$_['placeholder_route'] = 'Роут, например, catalog/product/edit';

// Modal
$_['modal_js_title']    = 'JavaScript';
$_['modal_js_save']     = 'Сохранить';
$_['modal_js_cancel']   = 'Отмена';
$_['modal_js_clear']    = 'Очистить';
$_['modal_fa_title']    = 'Выберите иконку';
$_['modal_fa_search']   = 'Поиск';

// Button
$_['button_expand']     = 'Раскрыть все';
$_['button_collapse']   = 'Закрыть все';
$_['button_add']        = 'Добавить';
$_['button_reset']      = 'Сбросить';
$_['button_js']         = 'JS';

// Help
$_['top_menu_help']     = '
В ссылках и JS вы можете использовать шорткоды:
<ul>
    <li><b>[product_id]</b>, <b>[user_token]</b> - любые $_GET параметры текущей страницы</li>
    <li><b>[config|config_language_id]</b> - конфигурация магазина</li>
    <li><b>[link|common/home]</b> - сгенерированные ссылки</li>
</ul>
В корневых элементах (отмечены голубым), через запятую, можно указывать роуты, в которых будут отображаться их дочерние пункты меню. Если оставить это поле пустым, пункты меню будут доступны во всех раделах. <br />Так же вы можете использовать разделитель пунктов меню и заголовок. Для этого, вместо ссылки пункта меню укажите:
<ul>
    <li><b>#divider - разделитель меню</b></li>
    <li><b>#header  - заголовок меню</b></li>
</ul>
Вы можете также открыть ссылки во всплывающем окне. Укажите код mmModal(event, true) в JS. Параметр event обязателен. Второй параметр позволяет удалить левое и верхнее меню из кода загруженной страницы. Он может принимать значение true или false.
';

// Error
$_['error_permission']  = 'У Вас нет прав для управления данным модулем!';

// Success
$_['text_success']      = 'Настройки успешно сохранены!';