<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

namespace Opencart\Admin\Controller\Extension\MenuManager\Module;
class MenuManagerTop extends \Opencart\System\Engine\Controller {

	private $_route 		= 'extension/menu_manager/module/menu_manager_top'; 
	private $_model 		= 'model_extension_menu_manager_module_menu_manager'; 
	private $_model_route 	= 'extension/menu_manager/module/menu_manager'; 
	private $_version 		= '1.1.3';

	private $error = [];

	private $_event = [
		[
			'code' 		=> 'menu_manager_top_add_data',
			'trigger'	=> 'admin/controller/common/header/before',
			'action'	=> '|menuManagerControllerEventHandler'
		],
		[
			'code' 		=> 'menu_manager_top_add_script',
			'trigger'	=> 'admin/view/common/header/after',
			'action'	=> '|menuManagerViewEventHandler'
		],
		[
			'code' 		=> 'menu_manager_top_add_data',
			'trigger'	=> 'admin/controller/common/header/before',
			'action'	=> '.menuManagerControllerEventHandler'
		],
		[
			'code' 		=> 'menu_manager_top_add_script',
			'trigger'	=> 'admin/view/common/header/after',
			'action'	=> '.menuManagerViewEventHandler'
		]
	];

	public function install() {
		$this->load->model('setting/event');
		foreach ($this->_event as $key => $_event) {
			if(!$this->model_setting_event->getEventByCode($_event['code'])) {

				$_event = [
					'code' 		=> $_event['code'],
					'trigger'	=> $_event['trigger'],
					'action'	=> $this->_route . $_event['action'],
					'description'	=> '',
					'status' 	=> 1,
					'sort_order' => 1
				];

				$this->model_setting_event->addEvent($_event);
			}
		}
	}

	public function uninstall() {
		$this->load->model('setting/event');
		foreach ($this->_event as $key => $_event) {
			$this->model_setting_event->deleteEventByCode($_event['code']);
		}

		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('top_menu');
	}
	
	public function menuManagerControllerEventHandler(&$route, &$data) {
		if (!isset($this->request->get['user_token']) || !isset($this->session->data['user_token']) || ($this->request->get['user_token'] != $this->session->data['user_token'])) {
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
				$registry_item = new \stdClass();
				$registry_item->data = $result;
				
				$this->registry->set('top_menu_data', $registry_item);
		
				$this->document->addStyle('/extension/menu_manager/admin/view/javascript/menu_manager.css?v=' . $this->_version);
				$this->document->addScript('/extension/menu_manager/admin/view/javascript/menu_manager_top.js?v=' . $this->_version);
				
				$this->document->addScript('/extension/menu_manager/admin/view/javascript/menu_manager.js?v=' . $this->_version);
			}
		}
	}

	public function menuManagerViewEventHandler(&$route, &$data, &$output) {
		if ($this->registry->has('top_menu_data')) {
			$top_menu = $this->registry->get('top_menu_data')->data;
			
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
		$this->response->redirect($this->url->link($this->_route, 'user_token=' . $this->session->data['user_token'], true));
	}

	public function index() {
		// check and install
		$this->install();

		$this->load->language($this->_route);

		$this->load->model($this->_model_route);
		$this->load->model('setting/setting');

		$this->document->setTitle($this->language->get('heading_title'));
		$data['version'] = $this->_version;

		$this->document->addScript('/extension/menu_manager/admin/view/javascript/jquery.nestable.js');
		$this->document->addStyle('/extension/menu_manager/admin/view/javascript/jquery.nestable.css');
		
		$this->document->addStyle('/extension/menu_manager/admin/view/javascript/menu_manager.css?v=' . $this->_version);

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$form_data = [
				'top_menu_data' => html_entity_decode($this->request->post['top_menu_data'], ENT_COMPAT, 'UTF-8')
			];
			$this->model_setting_setting->editSetting('top_menu', $form_data);

			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link($this->_route, 'user_token=' . $this->session->data['user_token'], true));
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
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link($this->_route, 'user_token=' . $this->session->data['user_token'], true)
		];

		$data['action'] = $this->url->link($this->_route, 'user_token=' . $this->session->data['user_token'], true);
		$data['reset'] = $this->url->link($this->_route . '/reset', 'user_token=' . $this->session->data['user_token'], true);
		$data['reset'] = html_entity_decode($data['reset'], ENT_COMPAT, 'UTF-8');
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		
		$data['top_menu'] = $this->model_setting_setting->getSetting('top_menu');
		
		if (!isset($data['top_menu']['top_menu_data'])) {
			$data['top_menu']['top_menu_data'] = '{}';
		}
		
		$data['preset_menu'] = json_encode($this->{$this->_model}->getPresets());

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