<?php
/*
 *  location: admin/controller
 */
class ControllerExtensionModuleDBlogModule extends Controller {
    private $codename = 'd_blog_module';
    private $route = 'extension/module/d_blog_module';
    private $sub_versions = array('lite', 'light', 'free');
    private $config_file = '';
    private $store_id = 0;
    private $error = array();

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->load->language($this->route);
        $this->load->model($this->route);
        $this->load->model('setting/setting');
        $this->load->model('extension/d_blog_module/category');
        $this->load->model('extension/d_opencart_patch/load');
        $this->load->model('extension/d_opencart_patch/cache');

        $this->d_shopunity = (is_file(DIR_SYSTEM.'library/d_shopunity/extension/d_shopunity.json'));
        $this->d_blog_module_pack = (is_file(DIR_SYSTEM.'library/d_shopunity/extension/d_blog_module_pack.json'));
        $this->d_opencart_patch = (is_file(DIR_SYSTEM.'library/d_shopunity/extension/d_opencart_patch.json'));
        $this->d_seo_module = (is_file(DIR_SYSTEM.'library/d_shopunity/extension/d_seo_module.json'));
        if($this->d_opencart_patch){
            $this->load->model('extension/d_opencart_patch/url');
            $this->load->model('extension/d_opencart_patch/user');
            $this->load->model('extension/d_opencart_patch/store');
        }
        $this->d_twig_manager = (is_file(DIR_SYSTEM.'library/d_shopunity/extension/d_twig_manager.json'));
        $this->d_event_manager = (is_file(DIR_SYSTEM.'library/d_shopunity/extension/d_event_manager.json'));
        $this->d_admin_style = (is_file(DIR_SYSTEM.'library/d_shopunity/extension/d_admin_style.json'));
        if ($this->d_admin_style){
            $this->load->model('extension/d_admin_style/style');

        }
        $this->extension = json_decode(file_get_contents(DIR_SYSTEM.'library/d_shopunity/extension/d_blog_module.json'), true);

        if (isset($this->request->get['store_id'])) {
            $this->store_id = $this->request->get['store_id'];
            $this->session->data['blog']['store_id'] = $this->request->get['store_id'];
        }

        $this->d_validator = (is_file(DIR_SYSTEM . 'library/d_shopunity/extension/d_validator.json'));

        // give some permissions
        $this->permission_handler('main');

        $this->config_file = $this->model_extension_module_d_blog_module->getConfigFile($this->codename, $this->sub_versions);
    }


    public function index()
    {
        if($this->d_shopunity){
            $this->load->model('extension/d_shopunity/mbooth');
            $this->model_extension_d_shopunity_mbooth->validateDependencies($this->codename);
        }

        if($this->d_twig_manager){
            $this->load->model('extension/module/d_twig_manager');
            $this->model_extension_module_d_twig_manager->installCompatibility();
        }

        $this->model_extension_d_opencart_patch_cache->clearTwig();

        if($this->d_event_manager){
            $this->load->model('extension/module/d_event_manager');
            $this->model_extension_module_d_event_manager->installCompatibility();
        }

        if ($this->d_validator) {
            $this->load->model('extension/d_shopunity/d_validator');
            $this->model_extension_d_shopunity_d_validator->installCompatibility();
        }

        if(!$this->isSetup()){
            $this->setupView();
            return;
        }

        $this->model_extension_module_d_blog_module->updateTables();

        //save post
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

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

            $this->response->redirect($this->model_extension_d_opencart_patch_url->getExtensionLink('module'));
        }

        // styles and scripts

        $this->document->addStyle('view/stylesheet/d_bootstrap_extra/bootstrap.css');
        // sortable
        $this->document->addScript('view/javascript/d_rubaxa_sortable/sortable.js');
        $this->document->addStyle('view/javascript/d_rubaxa_sortable/sortable.css');

        $this->document->addScript('view/javascript/d_tinysort/tinysort.min.js');
        $this->document->addScript('view/javascript/d_tinysort/jquery.tinysort.min.js');

        $this->document->addScript('view/javascript/d_bootstrap_colorpicker/js/bootstrap-colorpicker.min.js');
        $this->document->addStyle('view/javascript/d_bootstrap_colorpicker/css/bootstrap-colorpicker.min.css');

        $this->document->addScript('view/javascript/d_bootstrap_switch/js/bootstrap-switch.min.js');
        $this->document->addStyle('view/javascript/d_bootstrap_switch/css/bootstrap-switch.css');

        $this->document->addScript('view/javascript/d_bootstrap_bootbox/bootbox.min.js');

        $url = '';
        if(isset($this->response->get['store_id'])){
            $url +=  '&store_id='.$this->store_id;
        }

        if(isset($this->response->get['config'])){
            $url +=  '&config='.$this->response->get['config'];
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

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->model_extension_d_opencart_patch_url->link('common/dashboard')
            );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->model_extension_d_opencart_patch_url->getExtensionLink('module')
            );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->model_extension_d_opencart_patch_url->link($this->route, $url)
            );

        // Notification
        foreach($this->error as $key => $error){
            $data['error'][$key] = $error;
        }

        // Heading
        $this->document->setTitle($this->language->get('heading_title_main'));
        $data['heading_title'] = $this->language->get('heading_title_main');
        $data['text_edit'] = $this->language->get('text_edit');

        // Variable
        $data['d_shopunity'] = $this->d_shopunity;
        $data['pro'] = $this->d_blog_module_pack;
        $data['codename'] = $this->codename;
        $data['route'] = $this->route;
        $data['store_id'] = $this->store_id;
        $data['stores'] = $this->model_extension_d_opencart_patch_store->getAllStores();
        $data['config'] = $this->config_file;
        $data['version'] = $this->extension['version'];
        $data['token'] = $this->model_extension_d_opencart_patch_user->getToken();

        // Tab
        $data['tab_setting'] = $this->language->get('tab_setting');
        $data['tab_category'] = $this->language->get('tab_category');
        $data['tab_post_thumb'] = $this->language->get('tab_post_thumb');
        $data['tab_post'] = $this->language->get('tab_post');
        $data['tab_review'] = $this->language->get('tab_review');
        $data['tab_review_thumb'] = $this->language->get('tab_review_thumb');
        $data['tab_author'] = $this->language->get('tab_author');
        $data['tab_design'] = $this->language->get('tab_design');
        $data['tab_demo'] = $this->language->get('tab_demo');

        $data['menu_post'] = $this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/post');
        $data['menu_category'] = $this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/category');
        $data['menu_review'] = $this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/review');
        $data['menu_author'] = $this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/author');
        $data['text_menu_post'] = $this->language->get('text_menu_post');
        $data['text_menu_category'] = $this->language->get('text_menu_category');
        $data['text_menu_review'] = $this->language->get('text_menu_review');
        $data['text_menu_author'] = $this->language->get('text_menu_author');

        $data['tab_support'] = $this->language->get('tab_support');
        $data['text_support'] = $this->language->get('text_support');
        $data['entry_support'] = $this->language->get('entry_support');

        $data['button_support'] = $this->language->get('button_support');
        $data['support_url'] = $this->extension['support']['url'];

        // Button
        $data['button_save'] = $this->language->get('button_save');
        $data['button_save_and_stay'] = $this->language->get('button_save_and_stay');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_clear'] = $this->language->get('button_clear');
        $data['button_add'] = $this->language->get('button_add');
        $data['button_remove'] = $this->language->get('button_remove');
        $data['button_enabled_ssl'] = $this->language->get('button_enabled_ssl');

        //demo data
        $data['entry_install_demo_data'] = $this->language->get('entry_install_demo_data');
        $data['button_install_demo_data'] = $this->language->get('button_install_demo_data');
        $data['help_install_demo_data'] = $this->language->get( 'help_install_demo_data' );
        $data['warning_install_demo_data'] = $this->language->get( 'warning_install_demo_data' );

        //common
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_width'] = $this->language->get('text_width');
        $data['text_height'] = $this->language->get('text_height');

        //help
        $data['help_event_support'] = $this->language->get('help_event_support');
        $data['help_twig_support'] = $this->language->get('help_twig_support');
        $data['help_layout'] = $this->language->get( 'help_layout' );
        $data['help_home_category'] = $this->language->get( 'help_home_category' );
        $data['help_range_type'] = $this->language->get( 'help_range_type' );
        $data['help_review_social_login'] = $this->language->get( 'help_review_social_login' );
        $data['help_style_short_description_display'] = $this->language->get( 'help_style_short_description_display' );

         // Entry
        $data['entry_status'] = $this->language->get('entry_status');

        $data['entry_config_files'] = $this->language->get('entry_config_files');
        $data['entry_category_layout'] = $this->language->get('entry_category_layout');
        $data['entry_category_layout_type'] = $this->language->get('entry_category_layout_type');
        $data['entry_category_main_category_id'] = $this->language->get('entry_category_main_category_id');
        $data['entry_category_post_page_limit'] = $this->language->get('entry_category_post_page_limit');
        $data['entry_category_image_display'] = $this->language->get('entry_category_image_display');
        $data['entry_category_image_size'] = $this->language->get('entry_category_image_size');
        $data['entry_category_sub_category_display'] = $this->language->get('entry_category_sub_category_display');
        $data['entry_category_sub_category_col'] = $this->language->get('entry_category_sub_category_col');
        $data['entry_category_sub_category_image'] = $this->language->get('entry_category_sub_category_image');
        $data['entry_category_sub_category_post_count'] = $this->language->get('entry_category_sub_category_post_count');
        $data['entry_category_sub_category_image_size'] = $this->language->get('entry_category_sub_category_image_size');


        $data['entry_post_image_display'] = $this->language->get('entry_post_image_display');
        $data['entry_post_popup_display'] = $this->language->get('entry_post_popup_display');
        $data['entry_post_image_size'] = $this->language->get('entry_post_image_size');
        $data['entry_post_popup_size'] = $this->language->get('entry_post_popup_size');
        $data['entry_post_author_display'] = $this->language->get('entry_post_author_display');
        $data['entry_post_date_display'] = $this->language->get('entry_post_date_display');
        $data['entry_post_date_format'] = $this->language->get('entry_post_date_format');
        $data['entry_post_review_display'] = $this->language->get('entry_post_review_display');
        $data['entry_post_rating_display'] = $this->language->get('entry_post_rating_display');
        $data['entry_post_tag_display'] = $this->language->get('entry_post_tag_display');
        $data['entry_post_category_label_display'] = $this->language->get('entry_post_category_label_display');
        $data['entry_post_short_description_length'] = $this->language->get('entry_post_short_description_length');
        $data['entry_post_style_short_description_display'] = $this->language->get('entry_post_style_short_description_display');
        $data['entry_post_nav_display'] = $this->language->get('entry_post_nav_display');
        $data['entry_post_nav_same_category'] = $this->language->get('entry_post_nav_same_category');

        $data['entry_post_thumb_image_size'] = $this->language->get('entry_post_thumb_image_size');
        $data['entry_post_thumb_title_length'] = $this->language->get('entry_post_thumb_title_length');
        $data['entry_post_thumb_short_description_length'] = $this->language->get('entry_post_thumb_short_description_length');
        $data['entry_post_thumb_description_length'] = $this->language->get('entry_post_thumb_description_length');
        $data['entry_post_thumb_category_label'] = $this->language->get('entry_post_thumb_category_label');
        $data['entry_post_thumb_category_label_display'] = $this->language->get('entry_post_thumb_category_label_display');
        $data['entry_post_thumb_author_display'] = $this->language->get('entry_post_thumb_author_display');
        $data['entry_post_thumb_date_display'] = $this->language->get('entry_post_thumb_date_display');
        $data['entry_post_thumb_date_format'] = $this->language->get('entry_post_thumb_date_format');
        $data['entry_post_thumb_rating_display'] = $this->language->get('entry_post_thumb_rating_display');
        $data['entry_post_thumb_description_display'] = $this->language->get('entry_post_thumb_description_display');
        $data['entry_post_thumb_tag_display'] = $this->language->get('entry_post_thumb_tag_display');
        $data['entry_post_thumb_views_display'] = $this->language->get('entry_post_thumb_views_display');
        $data['entry_post_thumb_review_display'] = $this->language->get('entry_post_thumb_review_display');
        $data['entry_post_thumb_read_more_display'] = $this->language->get('entry_post_thumb_read_more_display');
        $data['entry_post_thumb_animate'] = $this->language->get('entry_post_thumb_animate');

        $data['entry_review_guest'] = $this->language->get('entry_review_guest');
        $data['entry_review_social_login'] = $this->language->get('entry_review_social_login');
        $data['entry_review_page_limit'] = $this->language->get('entry_review_page_limit');
        $data['entry_review_image_user_display'] = $this->language->get('entry_review_image_user_display');
        $data['entry_review_rating_display'] = $this->language->get('entry_review_rating_display');
        $data['entry_review_customer_display'] = $this->language->get('entry_review_customer_display');
        $data['entry_review_moderate'] = $this->language->get('entry_review_moderate');
        $data['entry_review_image_limit'] = $this->language->get('entry_review_image_limit');
        $data['entry_review_upload_image_size'] = $this->language->get('entry_review_upload_image_size');

        $data['entry_review_thumb_image_size'] = $this->language->get('entry_review_thumb_image_size');
        $data['entry_review_thumb_no_image'] = $this->language->get('entry_review_thumb_no_image');
        $data['entry_review_thumb_date_display'] = $this->language->get('entry_review_thumb_date_display');
        $data['entry_review_thumb_image_display'] = $this->language->get('entry_review_thumb_image_display');
        $data['entry_review_thumb_rating_display'] = $this->language->get('entry_review_thumb_rating_display');
        $data['entry_review_user_image_size'] = $this->language->get('entry_review_user_image_size');
        $data['entry_review_thumb_image_user_display'] = $this->language->get('entry_review_thumb_image_user_display');

        $data['entry_author_layout'] = $this->language->get('entry_author_layout');
        $data['entry_author_layout_type'] = $this->language->get('entry_author_layout_type');
        $data['entry_author_post_page_limit'] = $this->language->get('entry_author_post_page_limit');
        $data['entry_author_image_size'] = $this->language->get('entry_author_image_size');
        $data['entry_author_category_display'] = $this->language->get('entry_author_category_display');
        $data['entry_author_category_col'] = $this->language->get('entry_author_category_col');
        $data['entry_author_category_image'] = $this->language->get('entry_author_category_image');
        $data['entry_author_category_post_count'] = $this->language->get('entry_author_category_post_count');
        $data['entry_author_category_image_size'] = $this->language->get('entry_author_category_image_size');

        //design
        $data['entry_theme'] = $this->language->get('entry_theme');
        $data['entry_design_custom_style'] = $this->language->get('entry_design_custom_style');
        $data['entry_enabled_ssl_url'] = $this->language->get('entry_enabled_ssl_url');
        $data['help_enabled_ssl_url'] = $this->language->get('help_enabled_ssl_url');
        $data['enabled_ssl_url'] = $this->model_extension_d_opencart_patch_url->ajax($this->route.'/enabledSslUrl');
        $data['success_enabled_ssl'] = $this->language->get('success_enabled_ssl');
        $data['success_twig_compatible'] = $this->language->get('success_twig_compatible');

        //action
        $data['module_link'] = $this->model_extension_d_opencart_patch_url->link($this->route);
        $data['action'] = $this->model_extension_d_opencart_patch_url->link($this->route, $url);
        $data['cancel'] = $this->model_extension_d_opencart_patch_url->getExtensionLink('module');

        $data['text_install_twig_support'] = $this->language->get('text_install_twig_support');
        $data['install_twig_support'] = $this->model_extension_d_opencart_patch_url->link($this->route.'/install_twig_support');

        $data['text_install_event_support'] = $this->language->get('text_install_event_support');
        $data['install_event_support'] = $this->model_extension_d_opencart_patch_url->link($this->route.'/install_event_support');

        //instruction
        $data['tab_instruction'] = $this->language->get('tab_instruction');
        $data['text_instruction'] = $this->language->get('text_instruction');
        $data['text_pro'] = $this->language->get('text_pro');
        $data['text_powered_by'] = $this->language->get('text_powered_by');

        $data['ads'] = false;
        $data['extension_id'] = false;
        if(!is_file(DIR_SYSTEM.'library/d_shopunity/extension/d_seo_module_blog.json')){
            $data['ads'] = true;
            $data['extension_id'] = 121;
        }
        if(!is_file(DIR_SYSTEM.'library/d_shopunity/extension/d_blog_module_pack.json')){
            $data['ads'] = true;
            $data['extension_id'] = 119;
        }

        if (isset($this->request->post[$this->codename.'_status'])) {
            $data[$this->codename.'_status'] = $this->request->post[$this->codename.'_status'];
        } else {
            $data[$this->codename.'_status'] = $this->config->get($this->codename.'_status');
        }

        //get setting
        $data['setting'] = $this->model_extension_module_d_blog_module->getConfigData($this->codename, $this->codename.'_setting', $this->store_id, $this->config_file);



        //demo
        $data['demos'] = $this->model_extension_module_d_blog_module->getDemos();
        foreach($data['demos'] as $key => $demo){
            $data['demos'][$key]['install'] = $this->model_extension_d_opencart_patch_url->ajax($this->route.'/installDemoData','config='.$demo['config']);
        }

        $data['cols']  = array(1,2,3,4,6);
        $data['themes'] = $this->model_extension_module_d_blog_module->getThemes();
        $data['layout_types'] = $this->model_extension_module_d_blog_module->getLayouts();
        $data['animations'] = $this->config->get('d_blog_module_animations');

        //select
        $data['categories'][] = array('category_id' => 0, 'title' => $this->language->get('text_undefined'));
        $data['categories'] = array_merge($data['categories'], $this->model_extension_d_blog_module_category->getCategories());
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
        $data['config_files'] = $this->model_extension_module_d_blog_module->getConfigFiles($this->codename);

        $data['twig_support'] = true;
        // $data['twig_support'] = false;
        // if($twig_support){
        //     $this->load->model('extension/d_shopunity/ocmod');
        //     $data['twig_support'] = $this->model_extension_d_shopunity_ocmod->getModificationByName('d_twig_manager');
        // }

        $data['event_support'] = true;
        // $data['event_support'] = false;
        // if($event_support){
        //     $this->load->model('extension/d_opencart_patch/modification');
        //     $data['event_support'] = $this->model_extension_d_opencart_patch_modification->getModificationByName('d_event_manager');
        // }
        //admin_styles
        $data = $this->model_extension_d_admin_style_style->getLanguageText($data);
        $data['admin_styles'] = $this->model_extension_d_admin_style_style->getAvailableThemes();
        $this->model_extension_d_admin_style_style->getStyles($data['setting']['admin_style']);

        //languages
        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();
        foreach ($data['languages'] as $key =>  $language){
            if(VERSION >= '2.2.0.0'){
                $data['languages'][$key]['flag'] = 'language/'.$language['code'].'/'.$language['code'].'.png';
            }else{
                $data['languages'][$key]['flag'] = 'view/image/flags/'.$language['image'];
            }
        }
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->model_extension_d_opencart_patch_load->view($this->route, $data));
    }

    public function setupView() {

        $this->load->model('extension/d_opencart_patch/load');
        $this->load->model('extension/d_opencart_patch/url');

        if($this->d_admin_style){
            $this->load->model('extension/d_admin_style/style');

            $this->model_extension_d_admin_style_style->getAdminStyle('light');
        }

        $url_params = array();

        if (isset($this->response->get['store_id'])) {
            $url_params['store_id'] = $this->store_id;
        }

        $url = ((!empty($url_params)) ? '&' : '') . http_build_query($url_params);

        // Breadcrumbs
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->model_extension_d_opencart_patch_url->link('common/home')
            );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->model_extension_d_opencart_patch_url->link('marketplace/extension', 'type=module')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->model_extension_d_opencart_patch_url->link('marketplace/extension', $url)
        );

        // Notification
        foreach ($this->error as $key => $error) {
            $data['error'][$key] = $error;
        }

        // Heading
        $this->document->setTitle($this->language->get('heading_title_main'));
        $data['heading_title'] = $this->language->get('heading_title_main');
        $data['text_edit'] = $this->language->get('text_edit');

        $data['version'] = $this->extension['version'];

        $data['text_welcome_title'] = $this->language->get('text_welcome_title');
        $data['text_welcome_description'] = $this->language->get('text_welcome_description');

        $data['text_welcome_visual_editor'] = $this->language->get('text_welcome_visual_editor');
        $data['text_welcome_building_blocks'] = $this->language->get('text_welcome_building_blocks');
        $data['text_welcome_mobile_ready'] = $this->language->get('text_welcome_mobile_ready');
        $data['text_welcome_increase_sales'] = $this->language->get('text_welcome_increase_sales');

        $data['button_setup'] = $this->language->get('button_setup');
        $data['checkbox_setup'] = $this->language->get('checkbox_setup');
        $data['quick_setup'] = $this->model_extension_d_opencart_patch_url->ajax($this->route.'/setup');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->model_extension_d_opencart_patch_load->view('extension/'.$this->codename.'/welcome', $data));
    }

    public function setup(){
        $this->load->model('extension/d_opencart_patch/url');
        $this->load->model('extension/module/d_blog_module');
        $this->load->model('setting/setting');
        $this->load->config('d_blog_module');
        $setting = array();
        $setting['d_blog_module_setting'] = $this->config->get('d_blog_module_setting');
        $setting['d_blog_module_status'] = 1;
        $new_setting = array();
        foreach ($setting as $k => $v) {
            $new_setting['module_'.$k] = $v;
        }
        $this->model_setting_setting->editSetting('module_'.$this->codename, $new_setting, $this->store_id);
        $this->model_setting_setting->editSetting($this->codename, $setting, $this->store_id);
        $this->uninstallEvents();
        $this->installEvents();
        if ($this->request->post['demo_data']) {
            $demos = $this->model_extension_module_d_blog_module->getDemos();
            foreach($demos as $demo => $data){
                $result = $this->model_extension_module_d_blog_module->installDemoData(DIR_CONFIG.'d_blog_module_demo/'.$data['sql']);

                if(!empty($data['permission']) && is_array($data['permission'])){
                    $this->load->model('user/user_group');
                    foreach($data['permission'] as $permission => $routes){
                        foreach($routes as $route){
                            $this->model_user_user_group->addPermission($this->user->getId(), $permission, $route);
                        }
                    }
                }

                if(!empty($data['d_seo_module']) && $this->d_seo_module){
                    $this->load->model('extension/module/d_seo_module');
                    $installed_seo_extensions = $this->model_extension_module_d_seo_module->getInstalledSEOExtensions();
                    if (!in_array($data['d_seo_module'], $installed_seo_extensions)) {
                        $info = $this->load->controller('extension/d_seo_module/'.$data['d_seo_module'].'/control_install_extension');
                        $this->load->language('d_seo_module');

                        if ($info) {
                            $data = $info;

                            if ($data['error']) {
                                $json['error'] = $data['error'];

                            } else {
                                $installed_seo_extensions = $this->model_extension_module_d_seo_module->getInstalledSEOExtensions();
                            }
                        } else {
                            $json['warning'] = $this->language->get('error_dependence_d_seo_module');

                        }
                    }
                }
            }
        }

        $this->session->data['success'] = $this->language->get('success_setup');
        $json['redirect'] = $this->model_extension_d_opencart_patch_url->ajax($this->route);
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }


    /**
`
    Add Assisting functions here

     **/

    public function install()
    {
        $this->load->model('extension/module/d_blog_module');
        $this->model_extension_module_d_blog_module->createTables();
        $this->model_extension_module_d_blog_module->updateTables();
        $this->model_extension_module_d_blog_module->addAdminUsersToBlogAuthors();

        if($this->d_shopunity){
            $this->load->model('extension/d_shopunity/mbooth');
            $this->model_extension_d_shopunity_mbooth->installDependencies($this->codename);
        }

        if($this->d_opencart_patch){
            if(VERSION < '2.2.0.0'){
                $this->load->model('extension/d_opencart_patch/modification');
                $this->model_extension_d_opencart_patch_modification->setModification('d_blog_module.xml', 1); 
                $this->model_extension_d_opencart_patch_modification->refreshCache();
            }
        }

        $this->permission_handler('all');
    }

    public function uninstall()
    {
        if($this->d_shopunity && $this->validate()){
            $this->uninstallEvents();
        }
        if($this->d_opencart_patch && VERSION < '2.2.0.0'){
            $this->load->model('extension/d_opencart_patch/modification');
            $this->model_extension_d_opencart_patch_modification->setModification('d_blog_module.xml', 0); 
            $this->model_extension_d_opencart_patch_modification->refreshCache();
        }
        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting('d_blog_module');
        $this->model_setting_setting->deleteSetting('module_d_blog_module');
    }

    public function isSetup() {
        $this->load->model('extension/d_opencart_patch/extension');

        if(!$this->model_extension_d_opencart_patch_extension->isInstalled($this->codename)) {
            return false;
        }

        $this->load->model('setting/setting');

        $setting_module = $this->model_setting_setting->getSetting('module_'.$this->codename);

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

        $this->model_user_user_group->addPermission($this->model_extension_module_d_blog_module->getGroupId(), 'access', 'extension/'.$this->codename);
        $this->model_user_user_group->addPermission($this->model_extension_module_d_blog_module->getGroupId(), 'modify', 'extension/'.$this->codename);

        if ($perm == 'all') {
            $this->model_user_user_group->addPermission($this->model_extension_module_d_blog_module->getGroupId(), 'access', 'extension/'.$this->codename.'/category');
            $this->model_user_user_group->addPermission($this->model_extension_module_d_blog_module->getGroupId(), 'modify', 'extension/'.$this->codename.'/category');
            $this->model_user_user_group->addPermission($this->model_extension_module_d_blog_module->getGroupId(), 'access', 'extension/'.$this->codename.'/post');
            $this->model_user_user_group->addPermission($this->model_extension_module_d_blog_module->getGroupId(), 'modify', 'extension/'.$this->codename.'/post');
            $this->model_user_user_group->addPermission($this->model_extension_module_d_blog_module->getGroupId(), 'access', 'extension/'.$this->codename.'/review');
            $this->model_user_user_group->addPermission($this->model_extension_module_d_blog_module->getGroupId(), 'modify', 'extension/'.$this->codename.'/review');
            $this->model_user_user_group->addPermission($this->model_extension_module_d_blog_module->getGroupId(), 'access', 'extension/'.$this->codename.'/author');
            $this->model_user_user_group->addPermission($this->model_extension_module_d_blog_module->getGroupId(), 'modify', 'extension/'.$this->codename.'/author');
            $this->model_user_user_group->addPermission($this->model_extension_module_d_blog_module->getGroupId(), 'access', 'extension/'.$this->codename.'/author_group');
            $this->model_user_user_group->addPermission($this->model_extension_module_d_blog_module->getGroupId(), 'modify', 'extension/'.$this->codename.'/author_group');
        }
    }

    public function installEvents()
    {
        $this->load->model('extension/module/d_event_manager');
        $this->model_extension_module_d_event_manager->addEvent($this->codename, 'admin/view/common/column_left/before', 'extension/event/d_blog_module/view_common_column_left_before');
        $this->model_extension_module_d_event_manager->addEvent($this->codename, 'admin/view/setting/setting/before', 'extension/event/d_blog_module/view_setting_setting_captcha_before');
        $this->model_extension_module_d_event_manager->addEvent($this->codename, 'catalog/view/common/header/before', 'extension/event/d_blog_module/view_common_header_before');
        $this->model_extension_module_d_event_manager->addEvent($this->codename, 'catalog/view/common/menu/before', 'extension/event/d_blog_module/view_common_menu_before');
        $this->model_extension_module_d_event_manager->addEvent($this->codename, 'catalog/model/extension/module/d_visual_designer/getOptions/after', 'extension/event/d_blog_module/controller_after_d_visual_designer_menu');
        $this->model_extension_module_d_event_manager->addEvent($this->codename, 'admin/model/localisation/language/addLanguage/after', 'extension/event/d_blog_module/model_localisation_language_addLanguage_after');
        $this->model_extension_module_d_event_manager->addEvent($this->codename, 'admin/model/localisation/language/deleteLanguage/after', 'extension/event/d_blog_module/model_localisation_language_deleteLanguage_after');
        $this->model_extension_module_d_event_manager->addEvent($this->codename, 'admin/model/user/user/addUser/after', 'extension/event/d_blog_module/model_user_user_addUser_after');
        $this->model_extension_module_d_event_manager->addEvent($this->codename, 'admin/model/user/user/deleteUser/after', 'extension/event/d_blog_module/model_user_user_deleteUser_after');
        $this->model_extension_module_d_event_manager->addEvent($this->codename, 'catalog/model/design/layout/getLayout/after', 'extension/event/d_blog_module/model_design_layout_getLayout_after');
    }

    public function uninstallEvents()
    {
        $this->load->model('extension/module/d_event_manager');
        $this->model_extension_module_d_event_manager->deleteEvent($this->codename);
    }


    /*
    *   Ajax: install demo data
    */
    public function installDemoData()
    {
        $config = 'd_blog_module';
        if(isset($this->request->get['config'])){
            $config = $this->request->get['config'];
        }

        $this->config->load('d_blog_module_demo/'.$config);
        $data = $this->config->get($config.'_demo');

        $this->load->language($this->route);
        $this->load->model('extension/module/d_blog_module');
        $setting = $this->model_extension_module_d_blog_module->getConfigData($this->codename, $this->codename.'_setting', $this->store_id, $this->config_file);

        $result = $this->model_extension_module_d_blog_module->installDemoData(DIR_CONFIG.'d_blog_module_demo/'.$data['sql']);

        if(!empty($data['permission']) && is_array($data['permission'])){
            $this->load->model('user/user_group');
            foreach($data['permission'] as $permission => $routes){
                foreach($routes as $route){
                    $this->model_user_user_group->addPermission($this->user->getId(), $permission, $route);
                }
            }
        }

        if(!empty($data['d_seo_module']) && $this->d_seo_module){
            $this->load->model('extension/module/d_seo_module');
            $installed_seo_extensions = $this->model_extension_module_d_seo_module->getInstalledSEOExtensions();
            if (!in_array($data['d_seo_module'], $installed_seo_extensions)) {
                $info = $this->load->controller('extension/d_seo_module/'.$data['d_seo_module'].'/control_install_extension');
                $this->load->language('d_seo_module');

                if ($info) {
                    $data = $info;

                    if ($data['error']) {
                        $json['error'] = $data['error'];

                    } else {
                        $installed_seo_extensions = $this->model_extension_module_d_seo_module->getInstalledSEOExtensions();
                    }
                } else {
                    $json['warning'] = $this->language->get('error_dependence_d_seo_module');

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

            $this->model_extension_module_d_blog_module->enabledSSLUrl($ssl_url);

            $json['success'] = 'success';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function install_twig_support()
    {
        if (!$this->user->hasPermission('modify', $this->route)) {
            $this->session->data['error'] = $this->language->get('error_permission');
            $this->response->redirect($this->model_extension_d_opencart_patch_url->link($this->route));
        }

        if(is_file(DIR_SYSTEM.'library/d_shopunity/extension/d_twig_manager.json')){
            $this->load->model('extension/module/d_twig_manager');
            $this->model_extension_module_d_twig_manager->installCompatibility();
        }

        $this->response->redirect($this->model_extension_d_opencart_patch_url->link($this->route));

    }

    public function install_event_support()
    {
        if (!$this->user->hasPermission('modify', $this->route)) {
            $this->session->data['error'] = $this->language->get('error_permission');
            $this->response->redirect($this->model_extension_d_opencart_patch_url->link($this->route));
        }

        if(is_file(DIR_SYSTEM.'library/d_shopunity/extension/d_event_manager.json')){
            $this->load->model('extension/module/d_event_manager');
            $this->model_extension_module_d_event_manager->installCompatibility();
        }

        $this->response->redirect($this->model_extension_d_opencart_patch_url->link($this->route));

    }

}
