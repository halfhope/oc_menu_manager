# OpenCart Admin menu manager
[![License: GPLv3](https://img.shields.io/badge/license-GPL%20V3-green?style=plastic)](LICENSE)

The extension allows you to manage the main menu and adds the ability to create your own top menu. The top menu can be displayed for selected sections. Supported the built-in access control system in opencart to check access for links.

## Other Languages

* [Russian](README_RU.md)

## Change Log

* [CHANGELOG.md](docs/CHANGELOG.md)

## Screenshots

* [SCREENSHOTS.md](docs/SCREENSHOTS.md)

## Advantages

* Uses the event mechanism, works without injection into files.
* Does not replace the current menu, but picks up existing items.
* Doesn't affect performance.

## Features

* Support for HTML code in the names of menu items.
* Support for assigning JavaScript code to menu items*.
* Assign FontAwesome icons to each item*.
* Menu items are hidden if the current user does not have rights to view the contents of the section. The access control system built into OpenCart for sections is used ("System" > "Users" > "User groups").
* Supports link generation by route.
* $_GET variables, $this->config options in headers, links and JS as shortcodes.
* The top menu can be displayed in selected sections.
* In the top menu, you can create separators and group headers.
* Ability to open links in a pop-up window.

* For the main menu on the left, JavaScript, like icons, is only supported for first-level menu items. There is an ocmod file that solves this problem, the file is free, available upon request.

## Compatibility

* OpenCart 2.3, 3.x, 4.x.

## Demo

Admin

* [https://menu-manager.shtt.blog/admin/](https://menu-manager.shtt.blog/admin/) (auto login)

Catalog

* [https://menu-manager.shtt.blog/](https://menu-manager.shtt.blog/)

The demo site has a top menu for quick navigation.

## Installation

* Install the extension through the standard extension installation section.
* Go to the modules section and install the required module.

## Management

* The module is divided into 2 independent modules (main menu, top menu).
* In the right panel of both modules there are automatically scanned sections of your store, from this panel you can transfer ready-made menu items.
* When installing the main menu module, the module menu is automatically populated with the current main menu items.
* For the main menu, JavaScript, like icons, is only supported for first-level menu items. Icons built-in - FontAwesome.
* The top menu can be placed in selected sections of the admin panel by specifying the routes of the required pages, separated by commas, or on all pages if the route is left empty.


#### Shortcodes

* Shortcodes can be used in titles, links and JavaScript code.
* Access to the $_GET variables of the current page [product_id], [module_id].
* Access to $this->config store settings [config|config_language_id].
* Generation of links to admin sections with token [link|catalog/product].
* CSRF token [token], [user_token]

## License

* [GPL v3.0](LICENSE.MD)

## Thank You for Using My Extensions!

I have decided to make all my OpenCart extensions free and open-source to benefit the community. Developing, maintaining, and updating these extensions takes time and effort.

If my extensions have been helpful for your project and you’d like to support my work, any donation is greatly appreciated.

### 💙 You can support me via:

* [PayPal](https://paypal.me/TalgatShashakhmetov?country.x=US&locale.x=en_US)
* [CashApp](https://cash.app/$TalgatShashakhmetov)

Your support inspires me to keep improving and developing these tools. Thank you!
