<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

class ControllerExtensionModuleMenuManagerTop extends Controller {

	private $_route 		= 'extension/module/menu_manager_top'; 
	private $_model 		= 'model_extension_module_menu_manager'; 
	private $_model_route 	= 'extension/module/menu_manager'; 
	private $_version 		= '1.1.3';

	private $error = [];

	private $_event = [
		[
			'code' 		=> 'menu_manager_top_add_data',
			'trigger'	=> 'admin/controller/common/header/before',
			'action'	=> 'extension/module/menu_manager_top/menuManagerControllerEventHandler'
		],
		[
			'code' 		=> 'menu_manager_top_add_script',
			'trigger'	=> 'admin/view/common/header/after',
			'action'	=> 'extension/module/menu_manager_top/menuManagerViewEventHandler'
		]
	];

	public function install() {
		$this->load->model('extension/event');
		foreach ($this->_event as $key => $_event) {
			if(!$result = $this->model_extension_event->getEvent($_event['code'], $_event['trigger'], $_event['action'])) {
				$this->model_extension_event->addEvent($_event['code'], $_event['trigger'], $_event['action']);
			}
		}
	}

	public function uninstall() {
		$this->load->model('extension/event');
		foreach ($this->_event as $key => $_event) {
			$this->model_extension_event->deleteEvent($_event['code']);
		}

		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('left_menu');
	}
	
	public function menuManagerControllerEventHandler(&$route, &$data) {
		if (!isset($this->request->get['token']) || !isset($this->session->data['token']) || ($this->request->get['token'] != $this->session->data['token'])) {
			return;
		}

		$this->load->model('setting/setting');
		$top_menu = $this->model_setting_setting->getSetting('top_menu');
		$top_menu_data = (isset($top_menu['top_menu_data']) ? json_decode($top_menu['top_menu_data'], true) : []);

		if ($top_menu_data) {
			$result = [];
			foreach ($top_menu_data as $key => $value) {				
				$route_arg = (isset($this->request->get['route']) ? $this->request->get['route'] : '');
				if (empty($route_arg)) {
					$route_arg = (isset($this->request->request['route']) ? $this->request->request['route'] : '');
				}
				if ($value['name'] == $route_arg || empty($value['name'])) {
					$result = array_merge($result, (isset($value['children']) ? $value['children'] : []));
				} else {
					$routes = explode(',', $value['name']);
					if (count($routes) > 1) {
						foreach ($routes as $route_sub) {
							if ($route_arg == trim($route_sub)) {
								$result = array_merge($result, (isset($value['children']) ? $value['children'] : []));								
							}
						}
					}
				}
			}
			if (!empty($result)) {
				$this->registry->set('top_menu_data', $result);
	
				$this->document->addScript('view/javascript/menu_manager/menu_manager.js?v=' . $this->_version);

				$this->document->addStyle('view/javascript/menu_manager/menu_manager.css?v=' . $this->_version);
				$this->document->addScript('view/javascript/menu_manager/menu_manager_top.js?v=' . $this->_version);

				$this->document->addScript('view/javascript/menu_manager/menu_manager.js?v=' . $this->_version);
			}
		}
	}

	public function menuManagerViewEventHandler(&$route, &$data, &$output) {
		if ($this->registry->has('top_menu_data')) {
			$top_menu = $this->registry->get('top_menu_data');
			
			$this->load->model($this->_model_route);
			$top_menu_data = $this->{$this->_model}->recursiveFillVars($top_menu);
			
			$output .= PHP_EOL . '<script>var topMenuData = ' . json_encode($top_menu_data) . ';</script>' . PHP_EOL;
			$output .= '<script>$(document).ready(function(){' . $this->recursiveGenerateJs($top_menu_data) . '});</script>' . PHP_EOL;
		}
	}

	public function recursiveGenerateJs($menu) {
		$result = '';
		foreach ($menu as $key => $value) {
			if (isset($value['js']) && !empty($value['js'])) {
				$result .= "$(document).on('click', '#header_top_menu a[data-js={$value['id']}]', function(event){event.preventDefault();{$value['js']}});";
			}
			if (isset($value['children'])) {
				$result .= $this->recursiveGenerateJs($value['children']);
			}
		}
		return $result;
	}

	public function reset() {
		$this->load->language($this->_route);
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'GET') && $this->validate()) {	
			$this->model_setting_setting->editSetting('top_menu', ['top_menu_data' => '{}']);
			$this->session->data['success'] = $this->language->get('text_success');
		}
		$this->response->redirect($this->url->link($this->_route, 'token=' . $this->session->data['token'], true));
	}

	public function index() {
		// check and install
		$this->install();

		$this->load->language($this->_route);

		$this->load->model($this->_model_route);
		$this->load->model('setting/setting');

		$this->document->setTitle($this->language->get('heading_title'));
		$data['version'] = $this->_version;

		$this->document->addScript('view/javascript/menu_manager/jquery.nestable.js');
		$this->document->addStyle('view/javascript/menu_manager/jquery.nestable.css');
		
		$this->document->addStyle('view/javascript/menu_manager/menu_manager.css?v=' . $this->_version);

		$data['heading_title']		= $this->language->get('heading_title');
		$data['text_extension']		= $this->language->get('text_extension');
		$data['text_edit']			= $this->language->get('text_edit');
		$data['text_edit_item']		= $this->language->get('text_edit_item');
		$data['text_view_item']		= $this->language->get('text_view_item');
		$data['text_remove_item']	= $this->language->get('text_remove_item');
		$data['text_add_sub_item']	= $this->language->get('text_add_sub_item');
		$data['text_copy']			= $this->language->get('text_copy');
		$data['text_js']			= $this->language->get('text_js');
		$data['text_target']		= $this->language->get('text_target');
		$data['text_top_menu']		= $this->language->get('text_top_menu');
		$data['text_preset_menu']	= $this->language->get('text_preset_menu');
		$data['placeholder_name']	= $this->language->get('placeholder_name');
		$data['placeholder_link']	= $this->language->get('placeholder_link');
		$data['placeholder_icon']	= $this->language->get('placeholder_icon');
		$data['placeholder_route']	= $this->language->get('placeholder_route');
		$data['modal_js_title']		= $this->language->get('modal_js_title');
		$data['modal_js_save']		= $this->language->get('modal_js_save');
		$data['modal_js_cancel']	= $this->language->get('modal_js_cancel');
		$data['modal_js_clear']		= $this->language->get('modal_js_clear');
		$data['modal_fa_title']		= $this->language->get('modal_fa_title');
		$data['modal_fa_search']	= $this->language->get('modal_fa_search');
		$data['button_expand']		= $this->language->get('button_expand');
		$data['button_collapse']	= $this->language->get('button_collapse');
		$data['button_add']			= $this->language->get('button_add');
		$data['button_reset']		= $this->language->get('button_reset');
		$data['button_js']			= $this->language->get('button_js');
		$data['top_menu_help']		= $this->language->get('top_menu_help');

		$data['button_save']		= $this->language->get('button_save');
		$data['button_cancel']		= $this->language->get('button_cancel');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$form_data = [
				'top_menu_data' => html_entity_decode($this->request->post['top_menu_data'], ENT_COMPAT, 'UTF-8')
			];
			$this->model_setting_setting->editSetting('top_menu', $form_data);

			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link($this->_route, 'token=' . $this->session->data['token'], true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link($this->_route, 'token=' . $this->session->data['token'], true)
		];

		$data['action'] = $this->url->link($this->_route, 'token=' . $this->session->data['token'], true);
		$data['reset'] = $this->url->link($this->_route . '/reset', 'token=' . $this->session->data['token'], true);
		$data['reset'] = html_entity_decode($data['reset'], ENT_COMPAT, 'UTF-8');
		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);
		
		$data['top_menu'] = $this->model_setting_setting->getSetting('top_menu');
		
		if (!isset($data['top_menu']['top_menu_data'])) {
			$data['top_menu']['top_menu_data'] = '{}';
		}
		
		$data['preset_menu'] = json_encode($this->model_extension_module_menu_manager->getPresets());

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view($this->_route, $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', $this->_route)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}