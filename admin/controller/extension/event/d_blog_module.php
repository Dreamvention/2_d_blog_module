<?php
class ControllerExtensionEventDBlogModule extends Controller {
    public function view_common_column_left_before(&$route, &$data, &$output) {

        $this->load->language('extension/event/d_blog_module');

        $d_blog_module = array();
        $this->load->model('extension/d_opencart_patch/url');
        $d_blog_module[] = array(
            'name'     => $this->language->get('text_blog_post'),
            'href'     => $this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/post'),
            'children' => array()
        );
        $d_blog_module[] = array(
            'name'     => $this->language->get('text_blog_category'),
            'href'     => $this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/category'),
            'children' => array()
        );
        $d_blog_module[] = array(
            'name'     => $this->language->get('text_blog_review'),
            'href'     => $this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/review'),
            'children' => array()
        );
        $d_blog_module[] = array(
            'name'     => $this->language->get('text_blog_author'),
            'href'     => $this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/author'),
            'children' => array()
        );
        $d_blog_module[] = array(
            'name'     => $this->language->get('text_blog_author_group'),
            'href'     => $this->model_extension_d_opencart_patch_url->link('extension/d_blog_module/author_group'),
            'children' => array()
        );

        $d_blog_module[] = array(
            'name'     => $this->language->get('text_blog_settings'),
            'href'     => $this->model_extension_d_opencart_patch_url->link('extension/module/d_blog_module'),
            'children' => array()
        );

        $insert['menus'][] = array(
            'id'       => 'menu-blog',
            'icon'     => 'fa fa-newspaper-o fa-fw',
            'name'     => $this->language->get('text_blog'),
            'href'     => '',
            'children' => $d_blog_module
        );

        if(VERSION > '2.2.0.0'){
            array_splice( $data['menus'], 2, 0, $insert['menus'] );
        } else {
            $html = $this->load->view('extension/event/d_blog_module', $insert);

            $html_dom = new d_simple_html_dom();
            $html_dom->load($data['menu'], $lowercase = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT);
            $html_dom->find('#catalog', 0)->innertext .= $html;

            $data['menu'] = $html_dom;
        }


    }

    public function view_setting_setting_captcha_before(&$route, &$data, &$output){
        $this->load->language('extension/event/d_blog_module');

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

    public function view_category_after(&$route, &$data, &$output){

        $html_dom = new d_simple_html_dom();
        $html_dom->load((string)$output, $lowercase = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT);

        $this->load->model('localisation/language');

        $languages = $this->model_localisation_language->getLanguages();

        foreach ($languages as $language) {
            $html_dom->find('textarea[name^="category_description['.$language['language_id'].'][description]"]', 0)->class .=' d_visual_designer';
        }

        $this->load->model('extension/d_visual_designer/designer');
        if($this->model_extension_d_visual_designer_designer->checkPermission()){
            $html_dom->find('head', 0)->innertext  .= '<script src="view/javascript/d_visual_designer/d_visual_designer.js?'.$this->extension['version'].'" type="text/javascript"></script>';
        }

        $output = (string)$html_dom;
    }


    public function view_post_after(&$route, &$data, &$output){

        $html_dom = new d_simple_html_dom();
        $html_dom->load((string)$output, $lowercase = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT);

        $this->load->model('localisation/language');

        $languages = $this->model_localisation_language->getLanguages();

        foreach ($languages as $language) {
            $html_dom->find('textarea[name^="post_description['.$language['language_id'].'][description]"]', 0)->class .=' d_visual_designer';
        }

        $this->load->model('extension/d_visual_designer/designer');
        if($this->model_extension_d_visual_designer_designer->checkPermission()){
            $html_dom->find('head', 0)->innertext  .= '<script src="view/javascript/d_visual_designer/d_visual_designer.js?'.$this->extension['version'].'" type="text/javascript"></script>';
        }

        $output = (string)$html_dom;
    }



    public function view_author_after(&$route, &$data, &$output){

        $html_dom = new d_simple_html_dom();
        $html_dom->load((string)$output, $lowercase = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT);

        $this->load->model('localisation/language');

        $languages = $this->model_localisation_language->getLanguages();

        foreach ($languages as $language) {
            $html_dom->find('textarea[name^="author_description['.$language['language_id'].'][description]"]', 0)->class .=' d_visual_designer';
        }

        $this->load->model('extension/d_visual_designer/designer');
        if($this->model_extension_d_visual_designer_designer->checkPermission()){
            $html_dom->find('head', 0)->innertext  .= '<script src="view/javascript/d_visual_designer/d_visual_designer.js?'.$this->extension['version'].'" type="text/javascript"></script>';
        }

        $output = (string)$html_dom;
    }

}