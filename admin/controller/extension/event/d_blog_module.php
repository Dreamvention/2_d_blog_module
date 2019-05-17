<?php

class ControllerExtensionEventDBlogModule extends Controller
{


    /**
     * events for visual designer
     *
     */

    private $setting_visual_designer = array();

    public function view_common_column_left_before(&$route, &$data, &$output)
    {

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

        if (VERSION > '2.2.0.0') {
            array_splice($data['menus'], 2, 0, $insert['menus']);
        } else {
            $html = $this->load->view('extension/event/d_blog_module', $insert);

            $html_dom = new d_simple_html_dom();
            $html_dom->load($data['menu'], $lowercase = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT);
            $html_dom->find('#catalog', 0)->innertext .= $html;

            $data['menu'] = $html_dom;
        }


    }

    //admin/model/localisation/language/addLanguage/after

    public function view_setting_setting_captcha_before(&$route, &$data, &$output)
    {
        $this->load->language('extension/event/d_blog_module');

        $data['captcha_pages'][] = array(
            'text'  => $this->language->get('text_blog'),
            'value' => 'blog_module'
        );
    }

    //admin/model/localisation/language/deleteLanguage/after

    public function model_localisation_language_addLanguage_after($route, $data, $output)
    {
        $this->load->model('extension/module/d_blog_module');

        $data = $data[0];
        $data['language_id'] = $output;


        $this->model_extension_module_d_blog_module->addLanguage($data);
    }

    public function model_localisation_language_deleteLanguage_after($route, $data, $output)
    {
        $this->load->model('extension/module/d_blog_module');

        $language_id = $data[0];
        $data['language_id'] = $language_id;

        $this->model_extension_module_d_blog_module->deleteLanguage($data);
    }

    public function model_catalog_product_addPost_after(&$route, &$data, &$output)
    {
        $this->load->model('extension/' . 'd_visual_designer' . '/designer');
        foreach ($data[0]['vd_content'] as $field_name => $setting_json) {
            $setting = json_decode(html_entity_decode($setting_json, ENT_QUOTES, 'UTF-8'), true);
            $content = $this->{'model_extension_' . 'd_visual_designer' . '_designer'}->parseSetting($setting);
            $this->{'model_extension_' . 'd_visual_designer' . '_designer'}->saveContent($content, 'product', $output, rawurldecode($field_name));
        }
    }

    public function model_catalog_product_addPost_before(&$route, &$data, &$output)
    {
        $this->load_settings_vd();
        if ($this->setting_visual_designer['save_text']) {// todo
            $this->load->model('localisation/language');
            $languages = $this->model_localisation_language->getLanguages();
            foreach ($languages as $language) {
                $field_name = rawurlencode("product_description[" . $language['language_id'] . "][description]");
                if (!empty($data[0]['vd_content'][$field_name])) {
                    $setting = json_decode(html_entity_decode($data[0]['vd_content'][$field_name], ENT_QUOTES, 'UTF-8'), true);
                    $data[0]['product_description'][$language['language_id']]['description'] = $this->{'model_extension_' . $this->codename . '_designer'}->getText($setting);
                }
            }
        }
    }

    private function load_settings_vd()
    {
        $this->load->model('setting/setting');
        $this->load->model('extension/d_visual_designer/designer');
        $this->setting_visual_designer = $this->model_setting_setting->getSetting('d_visual_designer');

        if (!empty($this->setting_visual_designer['d_visual_designer' . '_setting'])) {

            $this->setting_visual_designer = $this->setting_visual_designer['d_visual_designer' . '_setting'];
        } else {
            $this->setting_visual_designer = $this->config->get('d_visual_designer' . '_setting');
        }

    }

    public function model_catalog_product_editPost_after(&$route, &$data, &$output)
    {

        $this->load->model('extension/d_visual_designer/designer');
        foreach ($data[1]['vd_content'] as $field_name => $setting_json) {
            $setting = json_decode(html_entity_decode($setting_json, ENT_QUOTES, 'UTF-8'), true);
            $content = $this->{'model_extension_' . 'd_visual_designer' . '_designer'}->parseSetting($setting);
            $this->{'model_extension_' . 'd_visual_designer' . '_designer'}->saveContent($content, 'd_blog_module_post', $data[0], rawurldecode($field_name));
        }
    }

    public function model_catalog_product_editPost_before(&$route, &$data) // before wont work with $output  only 3.0.2 maybe
    {
        $this->load_settings_vd();

        if ($this->setting_visual_designer['save_text']) {
            $this->load->model('localisation/language');

            $languages = $this->model_localisation_language->getLanguages();

            foreach ($languages as $language) {

                $field_name = rawurlencode("post_description[" . $language['language_id'] . "][description]");
                if (!empty($data[1]['vd_content'][$field_name])) {
                    $setting = json_decode(html_entity_decode($data[1]['vd_content'][$field_name], ENT_QUOTES, 'UTF-8'), true);
                    $data[1]['post_description'][$language['language_id']]['description'] = $this->{'model_extension_' . 'd_visual_designer' . '_designer'}->getText($setting);
                }
            }
        }

    }

    public function model_catalog_product_addauthor_after(&$route, &$data, &$output)
    {
        $this->load->model('extension/' . 'd_visual_designer' . '/designer');
        foreach ($data[0]['vd_content'] as $field_name => $setting_json) {
            $setting = json_decode(html_entity_decode($setting_json, ENT_QUOTES, 'UTF-8'), true);
            $content = $this->{'model_extension_' . 'd_visual_designer' . '_designer'}->parseSetting($setting);
            $this->{'model_extension_' . 'd_visual_designer' . '_designer'}->saveContent($content, 'product', $output, rawurldecode($field_name));
        }
    }

    public function model_catalog_product_addauthor_before(&$route, &$data)
    {
        $this->load_settings_vd();
        if ($this->setting_visual_designer['save_text']) {// todo
            $this->load->model('localisation/language');
            $languages = $this->model_localisation_language->getLanguages();
            foreach ($languages as $language) {
                $field_name = rawurlencode("product_description[" . $language['language_id'] . "][description]");
                if (!empty($data[0]['vd_content'][$field_name])) {
                    $setting = json_decode(html_entity_decode($data[0]['vd_content'][$field_name], ENT_QUOTES, 'UTF-8'), true);
                    $data[0]['product_description'][$language['language_id']]['description'] = $this->{'model_extension_' . $this->codename . '_designer'}->getText($setting);
                }
            }
        }
    }

    public function model_catalog_product_editauthor_after(&$route, &$data, &$output)
    {

        $this->load->model('extension/d_visual_designer/designer');
        foreach ($data[1]['vd_content'] as $field_name => $setting_json) {
            $setting = json_decode(html_entity_decode($setting_json, ENT_QUOTES, 'UTF-8'), true);
            $content = $this->{'model_extension_' . 'd_visual_designer' . '_designer'}->parseSetting($setting);
            $this->{'model_extension_' . 'd_visual_designer' . '_designer'}->saveContent($content, 'd_blog_module_author', $data[0], rawurldecode($field_name));
        }
    }

    public function model_catalog_product_editauthor_before(&$route, &$data) // before wont work with $output  only 3.0.2 maybe
    {
        $this->load_settings_vd();

        if ($this->setting_visual_designer['save_text']) {
            $this->load->model('localisation/language');

            $languages = $this->model_localisation_language->getLanguages();

            foreach ($languages as $language) {

                $field_name = rawurlencode("author_description[" . $language['language_id'] . "][description]");
                if (!empty($data[1]['vd_content'][$field_name])) {
                    $setting = json_decode(html_entity_decode($data[1]['vd_content'][$field_name], ENT_QUOTES, 'UTF-8'), true);
                    $data[1]['author_description'][$language['language_id']]['description'] = $this->{'model_extension_' . 'd_visual_designer' . '_designer'}->getText($setting);
                }
            }
        }

    }

    public function model_catalog_product_addCategory_after(&$route, &$data, &$output)
    {
        $this->load->model('extension/' . 'd_visual_designer' . '/designer');
        foreach ($data[0]['vd_content'] as $field_name => $setting_json) {
            $setting = json_decode(html_entity_decode($setting_json, ENT_QUOTES, 'UTF-8'), true);
            $content = $this->{'model_extension_' . 'd_visual_designer' . '_designer'}->parseSetting($setting);
            $this->{'model_extension_' . 'd_visual_designer' . '_designer'}->saveContent($content, 'product', $output, rawurldecode($field_name));
        }
    }

    public function model_catalog_product_addCategory_before(&$route, &$data)
    {

        $this->load_settings_vd();
        if ($this->setting_visual_designer['save_text']) {// todo
            $this->load->model('localisation/language');
            $languages = $this->model_localisation_language->getLanguages();
            foreach ($languages as $language) {
                $field_name = rawurlencode("product_description[" . $language['language_id'] . "][description]");
                if (!empty($data[0]['vd_content'][$field_name])) {
                    $setting = json_decode(html_entity_decode($data[0]['vd_content'][$field_name], ENT_QUOTES, 'UTF-8'), true);
                    $data[0]['product_description'][$language['language_id']]['description'] = $this->{'model_extension_' . $this->codename . '_designer'}->getText($setting);
                }
            }
        }
    }

    public function model_catalog_product_editCategory_after(&$route, &$data, &$output)
    {
        $this->load->model('extension/d_visual_designer/designer');
        foreach ($data[1]['vd_content'] as $field_name => $setting_json) {
            $setting = json_decode(html_entity_decode($setting_json, ENT_QUOTES, 'UTF-8'), true);
            $content = $this->{'model_extension_' . 'd_visual_designer' . '_designer'}->parseSetting($setting);
            $this->{'model_extension_' . 'd_visual_designer' . '_designer'}->saveContent($content, 'd_blog_module_category', $data[0], rawurldecode($field_name));
        }
    }

    public function model_catalog_product_editCategory_before(&$route, &$data) // before wont work with $output  only 3.0.2 maybe
    {
        $this->load_settings_vd();

        if ($this->setting_visual_designer['save_text']) {
            $this->load->model('localisation/language');

            $languages = $this->model_localisation_language->getLanguages();

            foreach ($languages as $language) {

                $field_name = rawurlencode("category_description[" . $language['language_id'] . "][description]");
                if (!empty($data[1]['vd_content'][$field_name])) {
                    $setting = json_decode(html_entity_decode($data[1]['vd_content'][$field_name], ENT_QUOTES, 'UTF-8'), true);
                    $data[1]['category_description'][$language['language_id']]['description'] = $this->{'model_extension_' . 'd_visual_designer' . '_designer'}->getText($setting);
                }
            }
        }

    }

    public function view_post_after(&$route, &$data, &$output)
    {
        $designer_data = array(
            'config' => 'd_blog_module_post',
            'output' => &$output,
            'id' => !empty($this->request->get['post_id'])?$this->request->get['post_id']:false
        );

        $vd_content = $this->load->controller('extension/'.'d_visual_designer'.'/designer', $designer_data);

        $html_dom = new d_simple_html_dom();
        $html_dom->load((string)$output, $lowercase = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT);

        $this->load->model('localisation/language');

        $languages = $this->model_localisation_language->getLanguages();

        foreach ($languages as $language) {
            $html_dom->find('textarea[name^="post_description[' . $language['language_id'] . '][description"]', 0)->class .= ' d_visual_designer';
        }
        $html_dom->find('body', 0)->innertext .= $vd_content;


        $output = (string)$html_dom;
    }

    public function view_author_after(&$route, &$data, &$output)
    {
        $designer_data = array(
            'config' => 'd_blog_module_author',
            'output' => &$output,
            'id' => !empty($this->request->get['author_id'])?$this->request->get['author_id']:false
        );

        $vd_content = $this->load->controller('extension/'.'d_visual_designer'.'/designer', $designer_data);


        $html_dom = new d_simple_html_dom();
        $html_dom->load((string)$output, $lowercase = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT);

        $this->load->model('localisation/language');

        $languages = $this->model_localisation_language->getLanguages();

        foreach ($languages as $language) {
            $html_dom->find('textarea[name^="author_description[' . $language['language_id'] . '][description"]', 0)->class .= ' d_visual_designer';
        }

        $html_dom->find('body', 0)->innertext .= $vd_content;

        $output = (string)$html_dom;
    }

    public function view_category_after(&$route, &$data, &$output)
    {
        $designer_data = array(
            'config' => 'd_blog_module_category',
            'output' => &$output,
            'id' => !empty($this->request->get['category_id'])?$this->request->get['category_id']:false
        );

        $vd_content = $this->load->controller('extension/'.'d_visual_designer'.'/designer', $designer_data);

        $html_dom = new d_simple_html_dom();
        $html_dom->load((string)$output, $lowercase = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT);

        $this->load->model('localisation/language');

        $languages = $this->model_localisation_language->getLanguages();

        foreach ($languages as $language) {
            $html_dom->find('textarea[name^="category_description[' . $language['language_id'] . '][description"]', 0)->class .= ' d_visual_designer';
        }

        $html_dom->find('body', 0)->innertext .= $vd_content;

        $output = (string)$html_dom;
    }
}