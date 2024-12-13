<?php 
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

class ModelExtensionModuleMenuManager extends Model {

	private $permissions_ignore = [
		'common/dashboard',
		'common/startup',
		'common/login',
		'common/logout',
		'common/forgotten',
		'common/reset',			
		'common/footer',
		'common/header',
		'error/not_found',
		'error/permission'
	];

	private $presets_ignore = [
		'common/developer',
		'common/filemanager',
		'common/forgotten',
		'common/header',
		'common/login',
		'common/profile',
		'common/reset',
		'common/security',
		'error/permission',
		'error/not_found'
	];

	public function recursiveReplaceToken($menus) {
		foreach ($menus as $key => $value) {
			$menus[$key]['href'] = preg_replace('/user_token=[a-zA-Z0-9]{32}/', 'user_token=[user_token]', $value['href']);
			if (isset($value['children']) && !empty($value['children'])) {
				$menus[$key]['children'] = $this->recursiveReplaceToken($value['children']);
			}
		}
		return $menus;
	}
	
	public function shortCode($value) {
		preg_match_all('/\[([^\]]*)\]/', $value, $matches);
		if (isset($matches[1])) {
			foreach ($matches[1] as $index => $match) {
				$exp = explode('|', $match);
				// config
				if (count($exp) > 1 && $exp[0] == 'config') {
					$config_index = substr($match, 7, strlen($match)-1);
					$true_value = $this->config->get($config_index);
					$match = preg_quote($match, '/|');
				// link
				} elseif (count($exp) > 1 && $exp[0] == 'link') {
					$link_index = substr($match, 5, strlen($match)-1);
					$true_value = $this->url->link($link_index, 'user_token=' . $this->session->data['user_token'], true);
					$true_value = htmlspecialchars_decode($true_value, ENT_QUOTES);
					$match = preg_quote($match, '/|');
				// get
				} else {
					$true_value = (isset($this->request->get[$match]) ? $this->request->get[$match] : '');
					if (empty($true_value)) {
						$true_value = (isset($this->request->request[$match]) ? $this->request->request[$match] : '');
					}
				}
				$value = preg_replace('/\[' . $match . '\]/', $true_value, $value);
			}
		}
		return $value;
	}

	public function recursiveFillVars($menus) {
		foreach ($menus as $key => $value) {
			$link_exists = isset($value['href']);
			$href = $link_exists ? $this->shortCode($value['href']) : '';
			$has_permission = $link_exists && $this->linkHasPremissions($href);
			if ($has_permission || !$link_exists) {
				if ($has_permission) {
					$menus[$key]['href'] = $href;
					if (isset($menus[$key]['js'])) {
						$menus[$key]['js'] = $this->shortCode($menus[$key]['js']);
					}
				}
				if (isset($menus[$key]['children']) && !empty($menus[$key]['children'])) {
					$menus[$key]['children'] = $this->recursiveFillVars($menus[$key]['children']);
				} else {
					$menus[$key]['children'] = [];
				}
				if ($link_exists && empty($menus[$key]['href']) && empty($menus[$key]['children'])) {
					unset($menus[$key]);
				}
			} elseif ($link_exists) {
				unset($menus[$key]);
			}
		}
		return $menus;
	}

	public function recursiveGlob($pattern, $flags = 0) {
		$files = glob($pattern, $flags);
		$result = [];
		$controller = DIR_APPLICATION . 'controller/';
		if ($files) {
			$route = str_replace($controller, '', dirname($pattern));
			foreach ($files as $key => $value) {
				$route = str_replace([$controller, '.php'], '', $value);
				$this->load->language($route, 'extension');
				$name = $this->language->get('extension')->get('heading_title');
				if ($name == 'heading_title' || in_array($route, $this->presets_ignore)) {
					continue;
				}
				$result[] = [
					'name' => $name,
					'href' => $this->url->link($route, 'user_token=[user_token]', true),
					'icon' => ''
				];
			}
		}
		foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
			$files = $this->recursiveGlob($dir . '/' . basename($pattern), $flags);
			if ($files) {
				$route = str_replace($controller, '', $dir);
				$result[] = [
					'name' => $route,
					'href' => '',
					'icon' => '',
					'children' => $files
				];
			}
		}
		return $result;
	}

	public function linkHasPremissions($link) {
		$query = parse_url($link, PHP_URL_QUERY);

		$is_external_link = (strripos($link, HTTP_SERVER) === false) && (strripos($link, HTTPS_SERVER) === false);
		if (!$is_external_link) {

			if (!empty($query)) {
				parse_str($query, $args);
				if (isset($args['route'])) {
					if (!in_array($args['route'], $this->permissions_ignore)) {	
						$shorted_route = substr($args['route'], 0, -strlen('/' . basename($args['route'])));
						return $this->user->hasPermission('access', $args['route']) || $this->user->hasPermission('access', $shorted_route);
					} else {
						return true;
					}
				}
			}

		}
		return true;
	}

	public function getPresets() {
		return $this->recursiveGlob(DIR_APPLICATION . 'controller/*.php', GLOB_BRACE);
	}
}