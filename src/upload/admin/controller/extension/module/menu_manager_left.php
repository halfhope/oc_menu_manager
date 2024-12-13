<?php
class ControllerExtensionModuleMenuManagerLeft extends Controller {
	private $error = array();

	private $_version 		= '1.0';

	private $_event = [
		[
			'code' 		=> 'menu_manager_left_handler',
			'trigger'	=> 'admin/view/common/column_left/before',
			'action'	=> 'extension/module/menu_manager_left/menuManagerEventHandler'
		]
	];

	public function install() {
		$this->load->model('setting/event');
		foreach ($this->_event as $key => $_event) {
			if(!$result = $this->model_setting_event->getEvent($_event['code'], $_event['trigger'], $_event['action'])) {
				$this->model_setting_event->addEvent($_event['code'], $_event['trigger'], $_event['action']);
			}
		}
	}

	public function uninstall() {
		$this->load->model('setting/event');
		foreach ($this->_event as $key => $_event) {
			$this->model_setting_event->deleteEventByCode($_event['code']);
		}

		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('left_menu');
	}

	public function menuManagerEventHandler(&$route, &$data, &$code) {
		$menus = isset($data['menus']) ? $data['menus'] : [];
		
		$this->load->model('setting/setting');
		$this->load->model('extension/module/menu_manager');

		$left_menu = $this->model_setting_setting->getSetting('left_menu');
		
		if (empty($left_menu) || !isset($left_menu['left_menu_data']) || $left_menu['left_menu_data'] == '{}') {
			$menus = $this->model_extension_module_menu_manager->recursiveReplaceToken($menus);
			$form_data = [
				'left_menu_data' => json_encode($menus)
			];
			$this->model_setting_setting->editSetting('left_menu', $form_data);
			$this->response->redirect($this->url->link('extension/module/menu_manager_left', 'user_token=' . $this->session->data['user_token'], true));
		} else {
			$menus = json_decode($left_menu['left_menu_data'], true);
		}
		$data['menus'] = $this->model_extension_module_menu_manager->recursiveFillVars($menus);
	}
	
	public function reset() {
		$this->load->language('extension/module/menu_manager_top');
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'GET') && $this->validate()) {	
			$this->model_setting_setting->editSetting('left_menu', ['left_menu_data' => '{}']);
			$this->session->data['success'] = $this->language->get('text_success');
		}
		$this->response->redirect($this->url->link('extension/module/menu_manager_left', 'user_token=' . $this->session->data['user_token'], true));
	}

	public function index() {
		$this->load->language('extension/module/menu_manager_left');

		$this->load->model('extension/module/menu_manager');
		$this->load->model('setting/setting');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->document->addScript('view/javascript/menu_manager/jquery.nestable.js');
		$this->document->addStyle('view/javascript/menu_manager/jquery.nestable.css');
		
		$this->document->addStyle('view/javascript/menu_manager/menu_manager.css?v=' . $this->_version);

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$form_data = [
				'left_menu_data' => html_entity_decode($this->request->post['left_menu_data'], ENT_COMPAT, 'UTF-8')
			];
			$this->model_setting_setting->editSetting('left_menu', $form_data);

			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('extension/module/menu_manager_left', 'user_token=' . $this->session->data['user_token'], true));
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/menu_manager_left', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/menu_manager_left', 'user_token=' . $this->session->data['user_token'], true);
		$data['reset'] = $this->url->link('extension/module/menu_manager_left' . '/reset', 'user_token=' . $this->session->data['user_token'], true);
		$data['reset'] = html_entity_decode($data['reset'], ENT_COMPAT, 'UTF-8');
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		
		$data['left_menu'] = $this->model_setting_setting->getSetting('left_menu');
		$data['preset_menu'] = json_encode($this->model_extension_module_menu_manager->getPresets());

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/menu_manager_left', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/menu_manager_left')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}