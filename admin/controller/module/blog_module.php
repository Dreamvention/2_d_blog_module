<?php

namespace Opencart\Admin\Controller\Extension\BlogModule\Module;

class BlogModule extends \Opencart\System\Engine\Controller {
    private $codename = 'blog_module';
    private $route = 'extension/blog_module/module/blog_module';
    private $sub_versions = array('lite', 'light', 'free');
    private $config_file = '';
    private int $store_id = 0;
    private $error = [];

    private $opencart_patch;

	private $extension;

	private $pro = false;

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->load->language($this->route);
        $this->load->model($this->route);
        $this->load->model('setting/setting');
        $this->load->model('extension/blog_module/blog_module/category');

		$this->pro = (is_file(DIR_EXTENSION . 'blog_module_pack/install.json'));
        $this->extension = json_decode(file_get_contents(DIR_EXTENSION.'blog_module/install.json'), true);
        $this->opencart_patch = is_file(DIR_EXTENSION . 'dv_opencart_patch/install.json');

        if (isset($this->request->get['store_id'])) {
            $this->store_id = $this->request->get['store_id'];
            $this->session->data['blog']['store_id'] = $this->request->get['store_id'];
        } else {
			$this->store_id = 0;
            $this->session->data['blog']['store_id'] = 0;
		}
        $this->model_extension_blog_module_module_blog_module->loadDependencies();
        // give some permissions
        $this->permission_handler('main');

        $this->config_file = $this->model_extension_blog_module_module_blog_module->getConfigFile($this->codename, $this->sub_versions);
    }


    public function index()
    {

        if(!$this->isSetup() || $this->getNonInstalledPackDependencies()){
            $this->setupView();
            return;
        }

        $this->load->model('extension/dv_opencart_patch/library/url');

        $this->model_extension_blog_module_module_blog_module->updateTables();

        // styles and scripts

        $this->document->addStyle(HTTP_CATALOG . 'extension/blog_module/admin/view/stylesheet/d_bootstrap_extra/bootstrap.css');
        // sortable
        $this->document->addScript(HTTP_CATALOG . 'extension/blog_module/admin/view/javascript/d_rubaxa_sortable/sortable.js');
        $this->document->addStyle(HTTP_CATALOG . 'extension/blog_module/admin/view/javascript/d_rubaxa_sortable/sortable.css');

        $this->document->addScript(HTTP_CATALOG . 'extension/blog_module/admin/view/javascript/d_tinysort/tinysort.min.js');
        $this->document->addScript(HTTP_CATALOG . 'extension/blog_module/admin/view/javascript/d_tinysort/jquery.tinysort.min.js');

        $this->document->addScript(HTTP_CATALOG . 'extension/blog_module/admin/view/javascript/d_bootstrap_colorpicker/js/bootstrap-colorpicker.min.js');
        $this->document->addStyle(HTTP_CATALOG . 'extension/blog_module/admin/view/javascript/d_bootstrap_colorpicker/css/bootstrap-colorpicker.min.css');

        $this->document->addScript(HTTP_CATALOG . 'extension/blog_module/admin/view/javascript/d_bootstrap_switch/js/bootstrap-switch.min.js');
        $this->document->addStyle(HTTP_CATALOG . 'extension/blog_module/admin/view/javascript/d_bootstrap_switch/css/bootstrap-switch.css');

		$this->document->addScript(HTTP_CATALOG . 'extension/blog_module/admin/view/javascript/d_alertify/alertify.min.js');
		$this->document->addStyle(HTTP_CATALOG . 'extension/blog_module/admin/view/javascript/d_alertify/css/alertify.min.css');
		$this->document->addStyle(HTTP_CATALOG . 'extension/blog_module/admin/view/javascript/d_alertify/css/themes/default.min.css');

        $url = '';
        if(isset($this->request->get['store_id'])){
            $url .=  '&store_id='.((int)$this->request->get['store_id']);
        }

        if(isset($this->request->get['config'])){
            $url .=  '&config='.$this->request->get['config'];
        }
        if(isset($this->session->data['text_upload'])){
            $text_upload =  $this->session->data['text_upload'];
            unset($this->session->data['text_upload']);
            $data['success']  = $text_upload;
        }
        if(isset($this->session->data['error_upload'])){
            $error_upload =  $this->session->data['error_upload'];
            unset($this->session->data['error_upload']);
            $data['error']['warning']  = $error_upload;
        }

        if(isset($this->session->data['success'])){
            $data['success'] =  $this->session->data['success'];
            unset($this->session->data['success']);

        }


        // Breadcrumbs
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->model_extension_dv_opencart_patch_library_url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_module'),
            'href' => $this->model_extension_dv_opencart_patch_library_url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module')
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->model_extension_dv_opencart_patch_library_url->link('extension/opencart/module/account', 'user_token=' . $this->session->data['user_token'])
        ];
        // Notification
        foreach($this->error as $key => $error){
            $data['error'][$key] = $error;
        }

        // Heading
        $this->document->setTitle($this->language->get('heading_title_main'));
        $data['heading_title'] = $this->language->get('heading_title_main');
        $data['text_edit'] = $this->language->get('text_edit');

        // Variable
        $data['pro'] = $this->pro;
        $data['codename'] = $this->codename;
        $data['route'] = $this->route;
        $data['store_id'] = $this->store_id;
        $data['stores'] = $this->model_extension_blog_module_module_blog_module->getAllStores();
        $data['config'] = $this->config_file;
        $data['version'] = $this->extension['version'];
        $data['token'] = $this->session->data['user_token'];

        $data['menu_post'] = $this->model_extension_dv_opencart_patch_library_url->link('extension/blog_module/blog_module/post', 'user_token=' . $this->session->data['user_token']);
        $data['menu_category'] = $this->model_extension_dv_opencart_patch_library_url->link('extension/blog_module/blog_module/category', 'user_token=' . $this->session->data['user_token']);
        $data['menu_review'] = $this->model_extension_dv_opencart_patch_library_url->link('extension/blog_module/blog_module/review', 'user_token=' . $this->session->data['user_token']);
        $data['menu_author'] = $this->model_extension_dv_opencart_patch_library_url->link('extension/blog_module/blog_module/author', 'user_token=' . $this->session->data['user_token']);

        $data['enabled_ssl_url'] = $this->model_extension_dv_opencart_patch_library_url->link($this->route.'|enabledSslUrl');

        //action
        $data['module_link'] = $this->model_extension_dv_opencart_patch_library_url->link($this->route, 'user_token=' . $this->session->data['user_token']);
        $data['action'] = $this->model_extension_dv_opencart_patch_library_url->link($this->route . '|save', 'user_token=' . $this->session->data['user_token']);
        $data['cancel'] = $this->model_extension_dv_opencart_patch_library_url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');
		$data['get_cancel'] = html_entity_decode($this->model_extension_dv_opencart_patch_library_url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module'));

        $data['ads'] = false;
        $data['extension_id'] = false;

        if (isset($this->request->post[$this->codename.'_status'])) {
            $data[$this->codename.'_status'] = $this->request->post[$this->codename.'_status'];
        } else {
            $data[$this->codename.'_status'] = $this->config->get($this->codename.'_status');
        }

        //get setting
        $data['setting'] = $this->model_extension_blog_module_module_blog_module->getConfigData($this->codename, $this->codename.'_setting', $this->store_id, $this->config_file);

        //demo
        $data['demos'] = $this->model_extension_blog_module_module_blog_module->getDemos();
        foreach($data['demos'] as $key => $demo){
            $data['demos'][$key]['install'] = $this->model_extension_dv_opencart_patch_library_url->link($this->route.'|installDemoData&user_token=' . $this->session->data['user_token'],'config='.$demo['config']);
        }

        $data['cols']  = array(1,2,3,4,6);
        $data['themes'] = $this->model_extension_blog_module_module_blog_module->getThemes();
        $data['layout_types'] = $this->model_extension_blog_module_module_blog_module->getLayouts();
        $data['animations'] = $this->config->get('blog_module_animations');

        //select
        $data['categories'][] = array('category_id' => 0, 'title' => $this->language->get('text_undefined'));
        $data['categories'] = array_merge($data['categories'], $this->model_extension_blog_module_blog_module_category->getCategories());
        $data['radios'] = array('1', '0');
        foreach($data['radios'] as $radio){
            $data['text_radio_'.$radio] = $this->language->get('text_radio_'.$radio);
        }

        $this->load->model('tool/image');
        if (isset($this->request->post[$this->codename.'_setting']['image']) && is_file(DIR_IMAGE . $this->request->post[$this->codename.'_setting']['image'])) {
            $data['image'] = $this->model_tool_image->resize($this->request->post[$this->codename.'_setting']['image'], 100, 100);
        } elseif (isset($data['setting']['image']) && is_file(DIR_IMAGE . $data['setting']['image'])) {
            $data['image'] = $this->model_tool_image->resize($data['setting']['image'], 100, 100);
        } else {
            $data['image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        //get config
        $data['config_files'] = $this->model_extension_blog_module_module_blog_module->getConfigFiles($this->codename);

        //languages
        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();
        foreach ($data['languages'] as $key =>  $language){
            $data['languages'][$key]['flag'] = 'language/'.$language['code'].'/'.$language['code'].'.png';
        }
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view($this->route, $data));
    }

    public function setupView() {
        $url_params = array();

        if (isset($this->request->get['store_id'])) {
            $url_params['store_id'] = (int)$this->request->get['store_id'];
        }

        $url = ((!empty($url_params)) ? '&' : '') . http_build_query($url_params);

        // Breadcrumbs
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
            );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->url->link('marketplace/extension', 'type=module')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->url->link('marketplace/extension', $url)
        );
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');
        // Notification
        foreach ($this->error as $key => $error) {
            $data['error'][$key] = $error;
        }

        // Heading
        $this->document->setTitle($this->language->get('heading_title_main'));
        $data['heading_title'] = $this->language->get('heading_title_main');
        $data['text_edit'] = $this->language->get('text_edit');

        $data['version'] = $this->extension['version'];
		$data['pack_dependencies'] = $this->getNonInstalledPackDependencies();
		$data['user_token'] = $this->session->data['user_token'];

        $data['button_setup'] = $this->language->get('button_setup');
        $data['checkbox_setup'] = $this->language->get('checkbox_setup');
        $data['quick_setup'] = str_replace('&amp;', '&', $this->url->link($this->route.'|setup', 'user_token=' . $this->session->data['user_token']));
        if (VERSION > '4.0.1.1') {
            $data['quick_setup'] = str_replace('|', '.', $data['quick_setup']);
        }
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/blog_module/blog_module/welcome', $data));
    }

    public function setup(){
        $this->permission_handler('all');
        $this->load->model('extension/blog_module/module/blog_module');
		$this->load->model('setting/store');
        $this->model_extension_blog_module_module_blog_module->createTables( );
        $this->model_extension_blog_module_module_blog_module->updateTables();

        $this->load->model('setting/setting');
        $this->config->addPath(DIR_EXTENSION . 'blog_module/system/config/');
        $this->load->config('blog_module');
        $setting = array();

        $setting['blog_module_setting'] = $this->config->get('blog_module_setting');
        $setting['blog_module_status'] = 1;
        $new_setting = array();
        foreach ($setting as $k => $v) {
            $new_setting['module_'.$k] = $v;
        }

		$stores = $this->model_setting_store->getStores();

		$stores = ($stores ? $stores : []);

		$stores[] = [
			'store_id' => 0
		];

		foreach ($stores as $store) {
			$this->model_setting_setting->editSetting('module_'.$this->codename, $new_setting, $store['store_id']);
			$this->model_setting_setting->editSetting($this->codename, $setting, $store['store_id']);
		}
        $this->uninstallEvents();
        $this->installEvents();
		$this->model_extension_blog_module_module_blog_module->addAdminUsersToBlogAuthors();
        if ($this->request->post['demo_data']) {
            $demos = $this->model_extension_blog_module_module_blog_module->getDemos();
            foreach($demos as $demo => $data){
				if (is_file(DIR_EXTENSION.'/blog_module_pack/system/config/blog_module_demo/'.$data['sql'])) {
					$result = $this->model_extension_blog_module_module_blog_module->installDemoData(DIR_EXTENSION.'/blog_module_pack/system/config/blog_module_demo/'.$data['sql']);
				} else {
					$result = $this->model_extension_blog_module_module_blog_module->installDemoData(DIR_EXTENSION.'/blog_module/system/config/blog_module_demo/'.$data['sql']);
				}

                if(!empty($data['permission']) && is_array($data['permission'])){
                    $this->load->model('user/user_group');
                    foreach($data['permission'] as $permission => $routes){
                        foreach($routes as $route){
                            $this->model_user_user_group->addPermission($this->user->getId(), $permission, $route);
                        }
                    }
                }
            }
        }

		if (is_file(DIR_EXTENSION . 'blog_module_pack/install.json')) {
			$this->load->model('setting/extension');

			$this->model_setting_extension->install('module', 'blog_module_pack', 'seo_module_blog');

			$this->load->model('user/user_group');

			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/blog_module_pack/module/seo_module_blog');
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/blog_module_pack/module/seo_module_blog');

			$namespace = str_replace(['_', '/'], ['', '\\'], ucwords('blog_module_pack', '_/'));

			// Register controllers, models and system extension folders
			$this->autoloader->register('Opencart\Admin\Controller\Extension\\' . $namespace, DIR_EXTENSION . 'blog_module_pack/admin/controller/');
			$this->autoloader->register('Opencart\Admin\Model\Extension\\' . $namespace, DIR_EXTENSION . 'blog_module_pack/admin/model/');
			$this->autoloader->register('Opencart\System\Extension\\' . $namespace, DIR_EXTENSION . 'blog_module_pack/system/');

			// Template directory
			$this->template->addPath('extension/blog_module_pack', DIR_EXTENSION . 'blog_module_pack/admin/view/template/');

			// Language directory
			$this->language->addPath('extension/blog_module_pack', DIR_EXTENSION . 'blog_module_pack/admin/language/');

			// Config directory
			$this->config->addPath('extension/blog_module_pack', DIR_EXTENSION . 'blog_module_pack/system/config/');

			// Call install method if it exists
            if (VERSION > '4.0.1.1') {
                $this->load->controller('extension/blog_module_pack/module/seo_module_blog.install');
            } else {
                $this->load->controller('extension/blog_module_pack/module/seo_module_blog|install');
            }
		}

        $this->session->data['success'] = $this->language->get('success_setup');
        $json['redirect'] = $this->url->link($this->route, '&user_token=' . $this->session->data['user_token']);
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

	public function getNonInstalledPackDependencies() {
		if (is_file(DIR_EXTENSION . 'blog_module_pack/install.json')) {
			$non_installed_dependencies = [];
			$extension = json_decode(file_get_contents(DIR_EXTENSION . 'blog_module_pack/install.json'), true);
			$dependencies = (!empty($extension['dependencies']) ? array_keys($extension['dependencies']) : []);
			if ($dependencies) {
				foreach ($dependencies as $dependency) {
					if (!is_file(DIR_EXTENSION . $dependency . '/install.json')) {
						$non_installed_dependencies[] = $dependency;
					}
				}
			}
			return $non_installed_dependencies;
		}
		return false;
	}

	public function installPackDependencies() {
		$this->load->language('marketplace/installer');

		$json = [];

		$code = $this->request->get['code'] ?? '';

		$page = $this->request->get['page'] ?? 1;

		$this->load->model('setting/extension');

		$extension_install_info = $code ? $this->model_setting_extension->getInstallByCode($code) : [];

		if ($extension_install_info) {
			$file = DIR_STORAGE . 'marketplace/' . $extension_install_info['code'] . '.ocmod.zip';

			if (!is_file($file)) {
				$json['error'] = sprintf($this->language->get('error_file'), $extension_install_info['code'] . '.ocmod.zip');
			}

			if ((int)$page > 1 && !is_dir(DIR_EXTENSION . $extension_install_info['code'] . '/')) {
				$json['error'] = sprintf($this->language->get('error_directory'), $extension_install_info['code'] . '/');
			}
		} else {
			$json['error'] = $this->language->get('error_extension');
		}

		if (!$json) {
			// Unzip the files
			$zip = new \ZipArchive();

			if ($zip->open($file)) {
				$total = $zip->numFiles;
				$limit = 200;

				$start = ((int)$page - 1) * $limit;
				$end = $start > ($total - $limit) ? $total : ($start + $limit);

				// Check if any of the files already exist.
				for ($i = $start; $i < $end; $i++) {
					$source = $zip->getNameIndex($i);

					$destination = str_replace('\\', '/', $source);

					// Only extract the contents of the upload folder
					$path = $extension_install_info['code'] . '/' . $destination;
					$base = DIR_EXTENSION;
					$prefix = '';

					// image > image
					if (substr($destination, 0, 6) == 'image/') {
						$path = $destination;
						$base = substr(DIR_IMAGE, 0, -6);
					}

					// We need to store the path differently for vendor folders.
					if (substr($destination, 0, 15) == 'system/storage/') {
						$path = substr($destination, 15);
						$base = DIR_STORAGE;
						$prefix = 'system/storage/';
					}

					// Must not have a path before files and directories can be moved
					$path_new = '';

					$directories = explode('/', dirname($path));

					foreach ($directories as $directory) {
						if (!$path_new) {
							$path_new = $directory;
						} else {
							$path_new = $path_new . '/' . $directory;
						}

						// To fix storage location
						if (!is_dir($base . $path_new . '/') && mkdir($base . $path_new . '/', 0777)) {
							$this->model_setting_extension->addPath($extension_install_info['extension_install_id'], $prefix . $path_new);
						}
					}

					// If check if the path is not directory and check there is no existing file
					if (substr($source, -1) != '/') {
						if (!is_file($base . $path) && copy('zip://' . $file . '#' . $source, $base . $path)) {
							$this->model_setting_extension->addPath($extension_install_info['extension_install_id'], $prefix . $path);
						}
					}
				}

				$zip->close();
			} else {
				$json['error'] = $this->language->get('error_unzip');
			}
		}

		if (!$json) {
			$json['text'] = sprintf($this->language->get('text_progress'), 2, $total);

			$url = '';

			$url .= '&code=' . $code;


			if (((int)$page * 200) <= $total) {
                if (VERSION > '4.0.1.1') {
                    $json['next'] = $this->url->link('extension/blog_module/module/blog_module.installPackDependencies', 'user_token=' . $this->session->data['user_token'] . $url . '&page=' . ((int)$page + 1), true);
                } else {
                    $json['next'] = $this->url->link('extension/blog_module/module/blog_module|installPackDependencies', 'user_token=' . $this->session->data['user_token'] . $url . '&page=' . ((int)$page + 1), true);
                }
			} else {
                if (VERSION > '4.0.1.1') {
                    $this->load->controller('marketplace/installer.vendor');
                } else {
                    $this->load->controller('marketplace/installer|vendor');
                }
				
				$output = json_decode($this->response->getOutput(), 1);

				$json = array_merge($json, $output);

				$extension = json_decode(file_get_contents(DIR_EXTENSION . $code . '/install.json'), 1);
				if (!empty($extension['dependencies'])) {
					$json['dependencies'] = array_keys($extension['dependencies']);
				}
				$this->model_setting_extension->editStatus($extension_install_info['extension_install_id'], 1);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function save() {
		//save post
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$data = [];

            if(!empty($this->session->data['blog']['store_id'])){
                $this->store_id = $this->session->data['blog']['store_id'];
            }

            $new_post = array();
            foreach ($this->request->post as $k => $v) {
                $new_post['module_'.$k] = $v;
            }

            $this->model_setting_setting->editSetting('module_'.$this->codename, $new_post, $this->store_id);
            $this->model_setting_setting->editSetting($this->codename, $this->request->post, $this->store_id);

            $this->uninstallEvents();
            if(!empty($this->request->post[$this->codename.'_status'])){
                $this->installEvents();
            }

            $this->session->data['success'] = $this->language->get('success_modifed');

			$data['error'] = $this->error;

			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];
				unset($this->session->data['success']);
			} else {
				$data['success'] = '';
			}

			$this->response->setOutput(json_encode($data));
		}
	}


    /**
`
    Add Assisting functions here

     **/

    public function install()
    {

    }

    public function uninstall()
    {
        $this->uninstallEvents();
        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting('blog_module');
        $this->model_setting_setting->deleteSetting('module_blog_module');
    }

    public function isSetup() {

        if(!$this->model_extension_blog_module_module_blog_module->isInstalled($this->codename)) {
            return false;
        }

        $this->load->model('setting/setting');

        $setting_module = $this->model_extension_blog_module_module_blog_module->getSetting('module_'.$this->codename, 0);

        if(!$setting_module) {
            return false;
        }
        return true;
    }

     private function validate($permission = 'modify') {

        if (isset($this->request->post['config'])) {
            return false;
        }

        $this->language->load($this->route);

        if (!$this->user->hasPermission($permission, $this->route)) {
            $this->error['warning'] = $this->language->get('error_permission');
            return false;
        }

        return true;
    }

    private function permission_handler($perm = 'main')
    {
        $this->load->model('user/user_group');

        $this->model_user_user_group->addPermission($this->model_extension_blog_module_module_blog_module->getGroupId(), 'access', $this->codename);
        $this->model_user_user_group->addPermission($this->model_extension_blog_module_module_blog_module->getGroupId(), 'modify', $this->codename);

        if ($perm == 'all') {
            $this->model_user_user_group->addPermission($this->model_extension_blog_module_module_blog_module->getGroupId(), 'access', $this->codename.'/category');
            $this->model_user_user_group->addPermission($this->model_extension_blog_module_module_blog_module->getGroupId(), 'modify', $this->codename.'/category');
            $this->model_user_user_group->addPermission($this->model_extension_blog_module_module_blog_module->getGroupId(), 'access', $this->codename.'/post');
            $this->model_user_user_group->addPermission($this->model_extension_blog_module_module_blog_module->getGroupId(), 'modify', $this->codename.'/post');
            $this->model_user_user_group->addPermission($this->model_extension_blog_module_module_blog_module->getGroupId(), 'access', $this->codename.'/review');
            $this->model_user_user_group->addPermission($this->model_extension_blog_module_module_blog_module->getGroupId(), 'modify', $this->codename.'/review');
            $this->model_user_user_group->addPermission($this->model_extension_blog_module_module_blog_module->getGroupId(), 'access', $this->codename.'/author');
            $this->model_user_user_group->addPermission($this->model_extension_blog_module_module_blog_module->getGroupId(), 'modify', $this->codename.'/author');
            $this->model_user_user_group->addPermission($this->model_extension_blog_module_module_blog_module->getGroupId(), 'access', $this->codename.'/author_group');
            $this->model_user_user_group->addPermission($this->model_extension_blog_module_module_blog_module->getGroupId(), 'modify', $this->codename.'/author_group');
        }
    }

    public function installEvents()
    {
        if ($this->opencart_patch) {
            $this->uninstallEvents();
            $this->load->model('extension/dv_opencart_patch/setting/event');
            $this->config->addPath(DIR_EXTENSION . 'blog_module/system/config/');
            $this->config->load('blog_module');
            $events = $this->config->get('blog_module_events');
            foreach ($events as $event) {
                $this->model_extension_dv_opencart_patch_setting_event->addEvent($event);
            }
        }
    }

    public function uninstallEvents()
    {
        if ($this->opencart_patch) {
            $this->load->model('extension/dv_opencart_patch/setting/event');
            $this->model_extension_dv_opencart_patch_setting_event->deleteEventByCode($this->codename);
        }
    }


    /*
    *   Ajax: install demo data
    */
    public function installDemoData()
    {
        $config = 'blog_module';
        if(isset($this->request->get['config'])){
            $config = $this->request->get['config'];
        }
		if (is_file(DIR_EXTENSION.'blog_module_pack/system/config/blog_module_demo/'.$config. '.php')) {
        	$this->config->addPath(DIR_EXTENSION . 'blog_module_pack/system/config/');
		} else {
			$this->config->addPath(DIR_EXTENSION . 'blog_module/system/config/');
		}
        $this->config->load('blog_module_demo/'.$config);
        $data = $this->config->get($config.'_demo');

        $this->load->language($this->route);
        $this->load->model('extension/blog_module/module/blog_module');
        $setting = $this->model_extension_blog_module_module_blog_module->getConfigData($this->codename, $this->codename.'_setting', $this->store_id, $this->config_file);

		if (is_file(DIR_EXTENSION.'blog_module_pack/system/config/blog_module_demo/'.$data['sql'])) {
			$result = $this->model_extension_blog_module_module_blog_module->installDemoData(DIR_EXTENSION.'blog_module_pack/system/config/blog_module_demo/'.$data['sql']);
		} else {
			$result = $this->model_extension_blog_module_module_blog_module->installDemoData(DIR_EXTENSION.'blog_module/system/config/blog_module_demo/'.$data['sql']);
		}

        if(!empty($data['permission']) && is_array($data['permission'])){
            $this->load->model('user/user_group');
            foreach($data['permission'] as $permission => $routes){
                foreach($routes as $route){
                    $this->model_user_user_group->addPermission($this->user->getId(), $permission, $route);
                }
            }
        }

        if($result){
            $json['success'] = $this->language->get('success_install_demo_data');
        }else{
            $json['error'] = $this->language->get('error_install_demo_data');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function enabledSslUrl()
    {
        $this->load->language($this->route);
        $json = array();
        if(isset($this->request->post['ssl_url'])){
            $ssl_url = $this->request->post['ssl_url'];
        }

        if (!$this->user->hasPermission('modify', $this->route) || !isset($ssl_url)) {
            $json['error'] = $this->language->get('error_permission');
        } else {

            $this->model_extension_blog_module_module_blog_module->enabledSSLUrl($ssl_url);

            $json['success'] = 'success';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}
