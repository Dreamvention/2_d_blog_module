<?php
namespace Opencart\Admin\Controller\Extension\BlogModule\Event;

class BlogModule extends \Opencart\System\Engine\Controller
{


    /**
     * events for visual designer
     *
     */

    private $setting_visual_designer = array();

    public function view_common_column_left_before(&$route, &$data, &$output)
    {

        $this->load->language('extension/blog_module/event/blog_module');

        $blog_module = array();

        $blog_module[] = array(
            'name'     => $this->language->get('text_blog_post'),
            'href'     => $this->url->link('extension/blog_module/blog_module/post', 'user_token=' . $this->session->data['user_token']),
            'children' => array()
        );
        $blog_module[] = array(
            'name'     => $this->language->get('text_blog_category'),
            'href'     => $this->url->link('extension/blog_module/blog_module/category', 'user_token=' . $this->session->data['user_token']),
            'children' => array()
        );
        $blog_module[] = array(
            'name'     => $this->language->get('text_blog_review'),
            'href'     => $this->url->link('extension/blog_module/blog_module/review', 'user_token=' . $this->session->data['user_token']),
            'children' => array()
        );
        $blog_module[] = array(
            'name'     => $this->language->get('text_blog_author'),
            'href'     => $this->url->link('extension/blog_module/blog_module/author', 'user_token=' . $this->session->data['user_token']),
            'children' => array()
        );
        $blog_module[] = array(
            'name'     => $this->language->get('text_blog_author_group'),
            'href'     => $this->url->link('extension/blog_module/blog_module/author_group', 'user_token=' . $this->session->data['user_token']),
            'children' => array()
        );

        $blog_module[] = array(
            'name'     => $this->language->get('text_blog_settings'),
            'href'     => $this->url->link('extension/blog_module/module/blog_module', 'user_token=' . $this->session->data['user_token']),
            'children' => array()
        );

        $insert['menus'][] = array(
            'id'       => 'menu-blog',
            'icon'     => 'fa fa-newspaper fa-fw',
            'name'     => $this->language->get('text_blog'),
            'href'     => '',
            'children' => $blog_module
        );

        array_splice($data['menus'], 2, 0, $insert['menus']);
    }

    //admin/model/localisation/language/addLanguage/after

    public function view_setting_setting_captcha_before(&$route, &$data, &$output)
    {
        $this->load->language('extension/blog_module/event/blog_module');

        $data['captcha_pages'][] = array(
            'text'  => $this->language->get('text_blog'),
            'value' => 'blog_module'
        );
    }

    //admin/model/localisation/language/deleteLanguage/after

    public function model_localisation_language_addLanguage_after($route, $data, $output)
    {
        $this->load->model('extension/blog_module/module/blog_module');

        $data = $data[0];
        $data['language_id'] = $output;


        $this->model_extension_blog_module_module_blog_module->addLanguage($data);
    }

    public function model_localisation_language_deleteLanguage_after($route, $data, $output)
    {
        $this->load->model('extension/blog_module/module/blog_module');

        $language_id = $data[0];
        $data['language_id'] = $language_id;

        $this->model_extension_blog_module_module_blog_module->deleteLanguage($data);
    }

    public function model_catalog_product_addPost_after(&$route, &$data, &$output)
    {
        $this->load->model('extension/visual_designer/visual_designer/designer');
        foreach ($data[0]['vd_content'] as $field_name => $setting_json) {
            $setting = json_decode(html_entity_decode($setting_json, ENT_QUOTES, 'UTF-8'), true);
            $content = $this->{'model_extension_visual_designer_visual_designer_designer'}->parseSetting($setting);
            $this->{'model_extension_visual_designer_visual_designer_designer'}->saveContent($content, 'product', $output, rawurldecode($field_name));
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
        $this->load->model('extension/visual_designer/visual_designer/designer');
        $this->setting_visual_designer = $this->model_setting_setting->getSetting('visual_designer');

        if (!empty($this->setting_visual_designer['visual_designer_setting'])) {

            $this->setting_visual_designer = $this->setting_visual_designer['visual_designer_setting'];
        } else {
            $this->setting_visual_designer = $this->config->get('visual_designer_setting');
        }

    }

    public function model_catalog_product_editPost_after(&$route, &$data, &$output)
    {

        $this->load->model('extension/visual_designer/visual_designer/designer');
        foreach ($data[1]['vd_content'] as $field_name => $setting_json) {
            $setting = json_decode(html_entity_decode($setting_json, ENT_QUOTES, 'UTF-8'), true);
            $content = $this->{'model_extension_visual_designer_visual_designer_designer'}->parseSetting($setting);
            $this->{'model_extension_visual_designer_visual_designer_designer'}->saveContent($content, 'blog_module_post', $data[0], rawurldecode($field_name));
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
                    $data[1]['post_description'][$language['language_id']]['description'] = $this->{'model_extension_visual_designer_visual_designer_designer'}->getText($setting);
                }
            }
        }

    }

    public function model_catalog_product_addauthor_after(&$route, &$data, &$output)
    {
        $this->load->model('extension/visual_designer/visual_designer/designer');
        foreach ($data[0]['vd_content'] as $field_name => $setting_json) {
            $setting = json_decode(html_entity_decode($setting_json, ENT_QUOTES, 'UTF-8'), true);
            $content = $this->{'model_extension_visual_designer_visual_designer_designer'}->parseSetting($setting);
            $this->{'model_extension_visual_designer_visual_designer_designer'}->saveContent($content, 'product', $output, rawurldecode($field_name));
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
                    $data[0]['product_description'][$language['language_id']]['description'] = $this->{'model_extension_blog_module_' . $this->codename . '_designer'}->getText($setting);
                }
            }
        }
    }

    public function model_catalog_product_editauthor_after(&$route, &$data, &$output)
    {

        $this->load->model('extension/visual_designer/visual_designer/designer');
        foreach ($data[1]['vd_content'] as $field_name => $setting_json) {
            $setting = json_decode(html_entity_decode($setting_json, ENT_QUOTES, 'UTF-8'), true);
            $content = $this->{'model_extension_visual_designer_visual_designer_designer'}->parseSetting($setting);
            $this->{'model_extension_visual_designer_visual_designer_designer'}->saveContent($content, 'blog_module_author', $data[0], rawurldecode($field_name));
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
                    $data[1]['author_description'][$language['language_id']]['description'] = $this->{'model_extension_visual_designer_visual_designer_designer'}->getText($setting);
                }
            }
        }

    }

    public function model_catalog_product_addCategory_after(&$route, &$data, &$output)
    {
        $this->load->model('extension/visual_designer/visual_designer/designer');
        foreach ($data[0]['vd_content'] as $field_name => $setting_json) {
            $setting = json_decode(html_entity_decode($setting_json, ENT_QUOTES, 'UTF-8'), true);
            $content = $this->{'model_extension_visual_designer_visual_designer_designer'}->parseSetting($setting);
            $this->{'model_extension_visual_designer_visual_designer_designer'}->saveContent($content, 'product', $output, rawurldecode($field_name));
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
        $this->load->model('extension/visual_designer/visual_designer/designer');
        foreach ($data[1]['vd_content'] as $field_name => $setting_json) {
            $setting = json_decode(html_entity_decode($setting_json, ENT_QUOTES, 'UTF-8'), true);
            $content = $this->{'model_extension_visual_designer_visual_designer_designer'}->parseSetting($setting);
            $this->{'model_extension_visual_designer_visual_designer_designer'}->saveContent($content, 'blog_module_category', $data[0], rawurldecode($field_name));
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
                    $data[1]['category_description'][$language['language_id']]['description'] = $this->{'model_extension_visual_designer_visual_designer_designer'}->getText($setting);
                }
            }
        }

    }

    public function view_post_after(&$route, &$data, &$output)
    {
        $designer_data = array(
            'config' => 'blog_module_post',
            'output' => &$output,
            'id' => !empty($this->request->get['post_id'])?$this->request->get['post_id']:false
        );

        $vd_content = $this->load->controller('extension/visual_designer/visual_designer/designer', $designer_data);


        $html_dom = $this->load->library('extension/dv_simple_html_dom/dv_simple_html_dom');
        $html_dom->load((string)$output, $lowercase = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT);

        $this->load->model('localisation/language');

        $languages = $this->model_localisation_language->getLanguages();

        foreach ($languages as $language) {
            $html_dom->find('textarea[name^="post_description[' . $language['language_id'] . '][description"]', 0)->class .= ' visual_designer';
        }
        $html_dom->find('body', 0)->innertext .= $vd_content;


        $output = (string)$html_dom;
    }

    public function view_author_after(&$route, &$data, &$output)
    {
        $designer_data = array(
            'config' => 'blog_module_author',
            'output' => &$output,
            'id' => !empty($this->request->get['author_id'])?$this->request->get['author_id']:false
        );

        $vd_content = $this->load->controller('extension/visual_designer/visual_designer/designer', $designer_data);


        $html_dom = $this->load->library('extension/dv_simple_html_dom/dv_simple_html_dom');
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
            'config' => 'blog_module_category',
            'output' => &$output,
            'id' => !empty($this->request->get['category_id'])?$this->request->get['category_id']:false
        );

        $vd_content = $this->load->controller('extension/visual_designer/visual_designer/designer', $designer_data);


        $html_dom = $this->load->library('extension/dv_simple_html_dom/dv_simple_html_dom');
        $html_dom->load((string)$output, $lowercase = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT);

        $this->load->model('localisation/language');

        $languages = $this->model_localisation_language->getLanguages();

        foreach ($languages as $language) {
            $html_dom->find('textarea[name^="category_description[' . $language['language_id'] . '][description"]', 0)->class .= ' d_visual_designer';
        }

        $html_dom->find('body', 0)->innertext .= $vd_content;

        $output = (string)$html_dom;
    }

	public function model_user_user_addUser_after(&$route, &$data, &$output) {
        $this->load->model('extension/blog_module/blog_module/author');
        $this->load->model('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();

        $user = $data[0];

        $author = $this->model_extension_blog_module_blog_module_author->getAuthorByUserId($this->user->getId());

        $this->db->query("INSERT INTO `" . DB_PREFIX . "bm_author` SET user_id = '" .(int)$output  . "', author_group_id = '" . ((int)$author['author_group_id']) . "'");

        $author_id = $this->db->getLastId();

        foreach ($languages as $key => $language) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "bm_author_description
                SET author_id = '" . (int) $author_id
                . "', language_id = '" . (int) $language['language_id']
                . "', name = '" . $this->db->escape($user['firstname'] . ' ' . $user['lastname'])
                . "', description = '"
                . "', short_description = '"
                . "', meta_title = '" . $this->db->escape($user['firstname'] . ' ' . $user['lastname'])
                . "', meta_description = '"
                . "', meta_keyword = ''");
        }
    }

    public function model_user_user_deleteUser_after(&$route, &$data, &$output) {
        $this->load->model('extension/blog_module/blog_module/author');

        $user_id = $data[0];

        $author = $this->model_extension_blog_module_blog_module_author->getAuthorByUserId($user_id);

        $this->db->query("DELETE FROM `" . DB_PREFIX . "bm_author` WHERE user_id = '" .(int)$user_id  . "'");

        $author_id = $author['author_id'];

        $this->db->query("DELETE FROM `" . DB_PREFIX . "bm_author_description` WHERE author_id = '" . ((int) $author_id) . "'");

    }
}
