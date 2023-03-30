<?php
class ControllerExtensionDBlogModuleCategory extends Controller {

    private $codename = 'd_blog_module';
    private $error = array();
    private $setting = '';
    private $sub_versions = array('lite', 'light', 'free');
    private $config_file = '';

    public function __construct($registry) {
        parent::__construct($registry);
        $this->load->model('extension/module/d_blog_module');
        $this->load->model('extension/d_opencart_patch/url');
        $this->load->model('extension/d_opencart_patch/user');
        $this->load->model('extension/d_opencart_patch/load');
        $this->config_file = $this->model_extension_module_d_blog_module->getConfigFile($this->codename, $this->sub_versions);
        $this->setting = $this->model_extension_module_d_blog_module->getConfigData($this->codename, $this->codename.'_setting', $this->config->get('config_store_id'),$this->config_file);
        $this->d_admin_style = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_admin_style.json'));
        if ($this->d_admin_style){
            $this->load->model('extension/d_admin_style/style');
            $this->model_extension_d_admin_style_style->getStyles('light');
        }
    }

    public function index() {

        $this->load->model('extension/d_blog_module/category');
        $this->load->language('extension/d_blog_module/category');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->model_extension_module_d_blog_module->updateTables();
        $this->getList();
    }

    public function add() {
        $this->load->language('extension/d_blog_module/category');
        $this->document->addStyle('view/javascript/summernote/summernote.css');
        $this->document->addScript('view/javascript/summernote/summernote.js');
        
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/d_blog_module/category');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm('add_categories')) {
            if(empty($this->request->post['custom'])){
                $this->request->post['setting'] = '';
            }
            $this->model_extension_d_blog_module_category->addCategory($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = $this->getUrl();

            $this->response->redirect($this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/category', $url));
        }

        $this->getForm();
    }

    public function edit() {

        $this->load->language('extension/d_blog_module/category');
        $this->document->addStyle('view/javascript/summernote/summernote.css');
        $this->document->addScript('view/javascript/summernote/summernote.js');
        
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/d_blog_module/category');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm('edit_categories')) {

            if(empty($this->request->post['custom'])){
                $this->request->post['setting'] = '';
            }

            $this->model_extension_d_blog_module_category->editCategory($this->request->get['bm_category_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = $this->getUrl();

            $this->response->redirect($this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/category', $url));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('module/category');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/d_blog_module/category');

        if (isset($this->request->post['selected']) && $this->validateDelete('delete_categories')) {
            foreach ($this->request->post['selected'] as $category_id) {
                $this->model_extension_d_blog_module_category->deleteCategory($category_id);
            }

            $url = $this->getUrl();

            $this->response->redirect($this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/category', $url));
        }

        $this->getList();
    }

    public function copy() {
        $this->load->language('extension/d_blog_module/category');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/d_blog_module/category');

        if (isset($this->request->post['selected']) && $this->validateCopy('add_categories')) {

            foreach ($this->request->post['selected'] as $category_id) {
                $this->model_extension_d_blog_module_category->copyCategory($category_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = $this->getUrl();

            $this->response->redirect($this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/category', $url));
        }

        $this->getList();
    }

    protected function getList() {

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'title';
        }
        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }


        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->model_extension_d_opencart_patch_url->link('common/dashboard')
            );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_blog_module'),
            'href' => $this->model_extension_d_opencart_patch_url->link('extension/module/d_blog_module')
            );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/category', $url)
            );

        $data['add'] = $this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/category/add', $url);
        $data['delete'] = $this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/category/delete', $url);
        $data['copy'] = $this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/category/copy', $url);

        $data['categories'] = array();
        $filter_data = array(
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
            );
        $category_total = $this->model_extension_d_blog_module_category->getTotalCategories();

        $results = $this->model_extension_d_blog_module_category->getCategories($filter_data);

        foreach ($results as $result) {

            $data['categories'][] = array(
                'category_id' => $result['category_id'],
                'title' => $result['title'],
                'sort_order' => $result['sort_order'],
                'status' => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'edit' => $this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/category/edit', '&bm_category_id=' . $result['category_id'] . $url)
                );
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['column_title'] = $this->language->get('column_title');
        $data['column_sort_order'] = $this->language->get('column_sort_order');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_action'] = $this->language->get('column_action');

        $data['entry_title'] = $this->language->get('entry_title');
        $data['entry_short_description'] = $this->language->get('entry_short_description');
        $data['entry_quantity'] = $this->language->get('entry_quantity');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_category'] = $this->language->get('entry_category');
        $data['entry_date_added'] = $this->language->get('entry_date_added');
        $data['entry_date_modified'] = $this->language->get('entry_date_modified');
        $data['entry_tag'] = $this->language->get('entry_tag');

        $data['button_copy'] = $this->language->get('button_copy');
        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_filter'] = $this->language->get('button_filter');
        $data['button_copy'] = $this->language->get('button_copy');

        $data['token'] = $this->model_extension_d_opencart_patch_user->getToken();

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

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array) $this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }
        $url = $this->getUrl();
        $data['sort_title'] = $this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/category', '&sort=title' . $url);
        $data['sort_order'] = $this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/category', '&sort=sort_order' . $url);

        $pagination = new Pagination();
        $pagination->total = $category_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/category', $url . '&page={page}');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($category_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($category_total - $this->config->get('config_limit_admin'))) ? $category_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $category_total, ceil($category_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->model_extension_d_opencart_patch_load->view('extension/d_blog_module/category_list', $data));
    }

    protected function getForm() {
        $this->document->addStyle('view/stylesheet/shopunity/bootstrap.css');
        $this->document->addScript('view/javascript/d_bootstrap_switch/js/bootstrap-switch.min.js');
        $this->document->addStyle('view/javascript/d_bootstrap_switch/css/bootstrap-switch.css');

        if(VERSION >= '2.2.0.0'){
            if(file_exists(DIR_APPLICATION.'view/javascript/summernote/opencart.js')){
                $this->document->addScript('view/javascript/summernote/opencart.js');
            }
            $data['store_2302'] = true;
        }

        $data['codename'] = $this->codename;
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_form'] = !isset($this->request->get['bm_category_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_plus'] = $this->language->get('text_plus');
        $data['text_minus'] = $this->language->get('text_minus');
        $data['text_default'] = $this->language->get('text_default');
        $data['text_option'] = $this->language->get('text_option');
        $data['text_option_value'] = $this->language->get('text_option_value');
        $data['text_select'] = $this->language->get('text_select');

        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_width'] = $this->language->get('text_width');
        $data['text_height'] = $this->language->get('text_height');

        $data['entry_title'] = $this->language->get('entry_title');
		$data['entry_short_description'] = $this->language->get('entry_short_description');
        $data['entry_description'] = $this->language->get('entry_description');
        $data['entry_meta_title'] = $this->language->get('entry_meta_title');
        $data['entry_meta_description'] = $this->language->get('entry_meta_description');
        $data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
        $data['entry_image'] = $this->language->get('entry_image');
        $data['entry_store'] = $this->language->get('entry_store');
        $data['entry_category'] = $this->language->get('entry_category');
        $data['entry_parent'] = $this->language->get('entry_parent');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_layout'] = $this->language->get('entry_layout');

        $data['entry_category_custom'] = $this->language->get('entry_category_custom');
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
        $data['entry_limit_access_user'] = $this->language->get('entry_limit_access_user');
        $data['entry_limit_access_user_group'] = $this->language->get('entry_limit_access_user_group');
        $data['entry_user'] = $this->language->get('entry_user');
        $data['entry_user_group'] = $this->language->get('entry_user_group');

        $data['help_layout'] = $this->language->get( 'help_layout' );
        $data['help_category'] = $this->language->get('help_category');
        $data['help_filter'] = $this->language->get('help_filter');
        $data['help_download'] = $this->language->get('help_download');
        $data['help_tag'] = $this->language->get('help_tag');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_attribute_add'] = $this->language->get('button_attribute_add');
        $data['button_option_add'] = $this->language->get('button_option_add');
        $data['button_option_value_add'] = $this->language->get('button_option_value_add');
        $data['button_discount_add'] = $this->language->get('button_discount_add');
        $data['button_special_add'] = $this->language->get('button_special_add');
        $data['button_image_add'] = $this->language->get('button_image_add');
        $data['button_add'] = $this->language->get('button_add');
        $data['button_remove'] = $this->language->get('button_remove');
        $data['button_recurring_add'] = $this->language->get('button_recurring_add');

        $data['tab_general'] = $this->language->get('tab_general');
        $data['tab_data'] = $this->language->get('tab_data');
        $data['tab_setting'] = $this->language->get('tab_setting');
        $data['tab_design'] = $this->language->get('tab_design');


        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['title'])) {
            $data['error_title'] = $this->error['title'];
        } else {
            $data['error_title'] = array();
        }

        if (isset($this->error['meta_title'])) {
            $data['error_meta_title'] = $this->error['meta_title'];
        } else {
            $data['error_meta_title'] = array();
        }

        if (isset($this->error['date_available'])) {
            $data['error_date_available'] = $this->error['date_available'];
        } else {
            $data['error_date_available'] = '';
        }

        $url = $this->getUrl();

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->model_extension_d_opencart_patch_url->link('common/dashboard')
            );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/category', $url)
            );

        if (!isset($this->request->get['bm_category_id']) && !isset($this->request->get['category_id'])) {
            $data['action'] = $this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/category/add', $url);
        } else if (!isset($this->request->get['bm_category_id']) && isset($this->request->get['category_id'])) {
            $this->response->redirect($this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/category/edit', '&bm_category_id=' . (int)$this->request->get['category_id'] . $url));
        } else {
            $data['action'] = $this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/category/edit', '&bm_category_id=' . (int)$this->request->get['bm_category_id'] . $url);
        }

        $data['cancel'] = $this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/category', $url);

        if (isset($this->request->get['bm_category_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {

            $category_info = $this->model_extension_d_blog_module_category->getCategory($this->request->get['bm_category_id']);
        }

        $data['token'] = $this->model_extension_d_opencart_patch_user->getToken();
        $data['url_token'] = $this->model_extension_d_opencart_patch_user->getUrlToken();
        $data['category_autocomplete'] = $this->model_extension_d_opencart_patch_url->ajax('extension/d_blog_module/category/autocomplete');

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();
        foreach ($data['languages'] as $key =>  $language){
            if(VERSION >= '2.2.0.0'){
                $data['languages'][$key]['flag'] = 'language/'.$language['code'].'/'.$language['code'].'.png';
            }else{
                $data['languages'][$key]['flag'] = 'view/image/flags/'.$language['image'];
            }
        }

        if (isset($this->request->post['category_description'])) {
            $data['category_description'] = $this->request->post['category_description'];
        } elseif (isset($this->request->get['bm_category_id'])) {
            $data['category_description'] = $this->model_extension_d_blog_module_category->getCategoryDescriptions($this->request->get['bm_category_id']);
        } else {
            $data['category_description'] = array();
        }
        if (isset($this->request->post['image'])) {
            $data['image'] = $this->request->post['image'];
        } elseif (!empty($category_info)) {
            $data['image'] = $category_info['image'];
        } else {
            $data['image'] = '';
        }

        $this->load->model('tool/image');

        if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
            $data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
        } elseif (!empty($category_info) && is_file(DIR_IMAGE . $category_info['image'])) {
            $data['thumb'] = $this->model_tool_image->resize($category_info['image'], 100, 100);
        } else {
            $data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        $this->load->model('setting/store');

        if (isset($this->request->post['path'])) {
            $data['path'] = $this->request->post['path'];
        } elseif (!empty($category_info)) {
            $data['path'] = $category_info['path'];
        } else {
            $data['path'] = '';
        }


        if (isset($this->request->post['parent_id'])) {
            $data['parent_id'] = $this->request->post['parent_id'];
        } elseif (!empty($category_info)) {
            $data['parent_id'] = $category_info['parent_id'];
        } else {
            $data['parent_id'] = 0;
        }

        $data['stores'] = $this->model_setting_store->getStores();

        if (isset($this->request->post['category_store'])) {
            $data['category_store'] = $this->request->post['category_store'];
        } elseif (isset($this->request->get['bm_category_id'])) {
            $data['category_store'] = $this->model_extension_d_blog_module_category->getCategoryStores($this->request->get['bm_category_id']);
        } elseif (isset($category_info['category_id'])) {
            $data['category_store'] = $this->model_extension_d_blog_module_category->getCategoryStores($category_info['category_id']);
        } else {
            $data['category_store'] = array(0);
        }

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($category_info)) {
            $data['sort_order'] = $category_info['sort_order'];
        } else {
            $data['sort_order'] = 1;
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($category_info)) {
            $data['status'] = $category_info['status'];
        } else {
            $data['status'] = true;
        }

        // access allow
        if (isset($this->request->post['limit_access_user'])) {
            $data['limit_access_user'] = $this->request->post['limit_access_user'];
        } elseif (!empty($category_info)) {
            $data['limit_access_user'] = $category_info['limit_access_user'];
        } else {
            $data['limit_access_user'] = 0;
        }
        if (isset($this->request->post['limit_access_user_group'])) {
            $data['limit_access_user_group'] = $this->request->post['limit_access_user_group'];
        } elseif (!empty($category_info)) {
            $data['limit_access_user_group'] = $category_info['limit_access_user_group'];
        } else {
            $data['limit_access_user_group'] = 0;
        }
        if(VERSION >= '2.1.0.1'){
            $this->load->model('customer/customer');
            $this->load->model('customer/customer_group');
            $customer_customer = 'model_customer_customer';
            $customer_groups = 'model_customer_customer_group';
        }else{
            $this->load->model('sale/customer');
            $this->load->model('sale/customer_group');
            $customer_customer = 'model_sale_customer';
            $customer_groups = 'model_sale_customer_group';
        }
        $data['users'] = array();
        if (!empty($category_info['limit_users'])) {
            foreach (explode(',', $category_info['limit_users']) as $user_id) {
                $user_info = $this->{$customer_customer}->getCustomer($user_id);
                $data['users'][$user_info['customer_id']] = $user_info['firstname'];
            }
        }
        $data['user_groups'] = array();
        if (!empty($category_info['limit_user_groups'])) {
            foreach (explode(',', $category_info['limit_user_groups']) as $user_group_id) {
                $user_group_info = $this->{$customer_groups}->getCustomerGroup($user_group_id);
                $data['user_groups'][$user_group_id] = $user_group_info['name'];
            }
        }

        //setting
        $data['setting'] = $this->setting['category'];
        if(!empty($category_info['custom'])){
            $data['custom'] = $category_info['custom'];
        }

        if(!empty($data['custom'])){
            $data['setting'] = array_merge($data['setting'], (array)$category_info['setting']);
        }
        
        $data['cols']  = array(1,2,3,4,6);
        $data['themes'] = $this->model_extension_module_d_blog_module->getThemes();
        $data['layout_types'] = $this->model_extension_module_d_blog_module->getLayouts();

        if (isset($this->request->post['category_layout'])) {
            $data['category_layout'] = $this->request->post['category_layout'];
        } elseif (isset($this->request->get['bm_category_id'])) {
            $data['category_layout'] = $this->model_extension_d_blog_module_category->getCategoryLayouts($this->request->get['bm_category_id']);
        } else {
            $data['category_layout'] = array();
        }

        $this->load->model('design/layout');
        $data['layouts'] = $this->model_design_layout->getLayouts();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->model_extension_d_opencart_patch_load->view('extension/d_blog_module/category_form', $data));
    }

    protected function validateForm($permission='edit_categories') {
        if (!$this->user->hasPermission('modify', 'extension/d_blog_module/category')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->model('extension/d_blog_module/author');

        if (!$this->model_extension_d_blog_module_author->hasPermission('add_categories')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        foreach ($this->request->post['category_description'] as $language_id => $value) {
            if ((utf8_strlen($value['title']) < 2) || (utf8_strlen($value['title']) > 255)) {
                $this->error['title'][$language_id] = $this->language->get('error_title');
            }

            if ((utf8_strlen($value['meta_title']) < 3) || (utf8_strlen($value['meta_title']) > 255)) {
                $this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
            }
        }
        
        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    protected function validateDelete($permission='delete_categories') {
        if (!$this->user->hasPermission('modify', 'extension/d_blog_module/category')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->model('extension/d_blog_module/author');

        if (!$this->model_extension_d_blog_module_author->hasPermission('delete_categories')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validateCopy() {
        if (!$this->user->hasPermission('modify', 'extension/d_blog_module/category')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->model('extension/d_blog_module/author');

        if (!$this->model_extension_d_blog_module_author->hasPermission('add_categories')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_title'])) {
            $this->load->model('extension/d_blog_module/category');

            $filter_data = array(
                'filter_title' => $this->request->get['filter_title'],
                'sort' => 'title',
                'order' => 'ASC',
                'start' => 0,
                'limit' => 20
                );

            $results = $this->model_extension_d_blog_module_category->getCategories($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'category_id' => $result['category_id'],
                    'title' => strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8'))
                    );
            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['title'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    protected function getUrl() {

        $url = '';

        if (isset($this->request->get['filter_title'])) {
            $url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_tag'])) {
            $url .= '&filter_tag=' . urlencode(html_entity_decode($this->request->get['filter_tag'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['filter_date_modified'])) {
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
        }

        if (isset($this->request->get['order']) && $this->request->get['order'] == 'DESC') {
            if($this->request->get['route'] == 'extension/d_blog_module/category'){
                $url .= '&order=ASC';
            }else{
                $url .= '&order=DESC';
            }
        } else {
            if($this->request->get['route'] == 'extension/d_blog_module/category'){
                $url .= '&order=DESC';
            }else{
                $url .= '&order=ASC';
            }
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        return $url;
    }
}