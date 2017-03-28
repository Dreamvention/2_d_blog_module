<?php
class ControllerEventDBlogModule extends Controller {
    public function view_common_column_left_before(&$route, &$data, &$output) {

        $this->load->language('event/d_blog_module');

        $d_blog_module = array();

        $d_blog_module[] = array(
            'name'     => $this->language->get('text_blog_post'),
            'href'     => $this->url->link('d_blog_module/post', 'token=' . $this->session->data['token'], 'SSL'),
            'children' => array()
        );
        $d_blog_module[] = array(
            'name'     => $this->language->get('text_blog_category'),
            'href'     => $this->url->link('d_blog_module/category', 'token=' . $this->session->data['token'], 'SSL'),
            'children' => array()
        );
        $d_blog_module[] = array(
            'name'     => $this->language->get('text_blog_review'),
            'href'     => $this->url->link('d_blog_module/review', 'token=' . $this->session->data['token'], 'SSL'),
            'children' => array()
        );
        $d_blog_module[] = array(
            'name'     => $this->language->get('text_blog_author'),
            'href'     => $this->url->link('d_blog_module/author', 'token=' . $this->session->data['token'], 'SSL'),
            'children' => array()
        );
        $d_blog_module[] = array(
            'name'     => $this->language->get('text_blog_author_group'),
            'href'     => $this->url->link('d_blog_module/author_group', 'token=' . $this->session->data['token'], 'SSL'),
            'children' => array()
        );
        
        $d_blog_module[] = array(
            'name'     => $this->language->get('text_blog_settings'),
            'href'     => $this->url->link('module/d_blog_module', 'token=' . $this->session->data['token'], true),
            'children' => array()
        );

        $insert['menus'][] = array(
            'id'       => 'menu-blog',
            'icon'     => 'fa fa-newspaper-o fa-fw',
            'name'     => $this->language->get('text_blog'),
            'href'     => '',
            'children' => $d_blog_module
        );

        $html = $this->load->view('event/d_blog_module.tpl', $insert);

        $html_dom = new d_simple_html_dom();
        $html_dom->load($data['menu'], $lowercase = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT);
        $html_dom->find('#catalog', 0)->innertext .= $html;

        $data['menu'] = $html_dom;


    }

    public function view_setting_setting_captcha_before(&$route, &$data, &$output){
        $this->load->language('event/d_blog_module');

        $data['captcha_pages'][] = array(
                'text'  => $this->language->get('text_blog'),
                'value' => 'blog_module'
        );
    }

    //admin/model/localisation/language/addLanguage/after
    public function model_localisation_language_addLanguage_after($route, $data, $output){
        $this->load->model('extension/module/d_blog_module');

        $data = $data[0];
        $data['language_id'] = $output; 


        $this->model_extension_module_d_blog_module->addLanguage($data);
    }

    //admin/model/localisation/language/deleteLanguage/after
    public function model_localisation_language_deleteLanguage_after($route, $data, $output){
        $this->load->model('extension/module/d_blog_module');

        $language_id = $data[0];
        $data['language_id'] = $language_id;

        $this->model_extension_module_d_blog_module->deleteLanguage($data);
    }
}