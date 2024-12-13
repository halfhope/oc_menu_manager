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
		'error/not_found',
		'event/compatibility',
		'event/theme'
	];

	public function recursiveReplaceToken($menus) {
		foreach ($menus as $key => $value) {
			$menus[$key]['href'] = preg_replace('/token=[a-zA-Z0-9]{32}/', 'token=[token]', $value['href']);
			if (isset($value['children']) && !empty($value['children'])) {
				$menus[$key]['children'] = $this->recursiveReplaceToken($value['children']);
			}
		}
		return $menus;
	}

	public function recursiveFillVars($menus) {
		foreach ($menus as $key => $value) {
			if ($this->linkHasPremissions($value['href'])) {
				preg_match_all('/\[([^\]]*)\]/', $menus[$key]['href'], $matches);
				if (isset($matches[1])) {
					foreach ($matches[1] as $index => $match_index) {
						$exp = explode('|', $match_index);
						if (count($exp) > 1 && $exp[0] == 'config') {
							$config_index = substr($match_index, 7, strlen($match_index)-1);
							$get_value = $this->config->get($config_index);
							$match_index = str_replace('|', '\|', $match_index);
						} else {
							$get_value = (isset($this->request->get[$match_index]) ? $this->request->get[$match_index] : '');
							if (empty($get_value)) {
								$get_value = (isset($this->request->request[$match_index]) ? $this->request->request[$match_index] : '');
							}
						}
						$menus[$key]['href'] = preg_replace('/\[' . $match_index . '\]/', $get_value, $menus[$key]['href']);
					}
				}
				if (isset($menus[$key]['children']) && !empty($menus[$key]['children'])) {
					$menus[$key]['children'] = $this->recursiveFillVars($menus[$key]['children']);
				} else {
					$menus[$key]['children'] = [];
				}
				if (empty($menus[$key]['href']) && empty($menus[$key]['children'])) {
					unset($menus[$key]);
				}
			} else {
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
				$name = strip_tags($this->language->get('heading_title'));
				$this->language->set('heading_title', '');

				if ($name == 'heading_title' || empty($name) || in_array($route, $this->presets_ignore)) {
					continue;
				}
				$result[] = [
					'name' => $name,
					'href' => $this->url->link($route, 'token=[token]', true),
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
		if (!empty($query)) {
			parse_str($query, $args);
			if (isset($args['route'])) {
				if (!in_array($args['route'], $this->permissions_ignore)) {	
					return $this->user->hasPermission('access', $args['route']);
				}
			}
		}
		return true;
	}

	public function getPresets() {
		return $this->recursiveGlob(DIR_APPLICATION . 'controller/*.php', GLOB_BRACE);
	}
}