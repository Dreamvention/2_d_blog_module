<?php

class ControllerExtensionEventDBlogModule extends Controller
{
    public function view_common_header_before(&$route, &$data, &$output)
    {
        if (VERSION < '3.0.0.0') {
            $this->add_blog_into_menu($data);
        }
    }
    public function view_common_menu_before(&$route, &$data, &$output)
    {
        if (VERSION >= '3.0.0.0') {
            $this->add_blog_into_menu($data);
        }
    }
    public function controller_after_d_visual_designer_menu(&$route, &$data, &$output)
    {
        if ($data[0] == 'menu') {
            $this->add_blog_into_menu($output);
        }
    }

    private function add_blog_into_menu(&$data)
    {
        $bm_status = $this->config->get('d_blog_module_status');
        if ($bm_status) {
            $this->load->language('extension/event/d_blog_module');
            $this->load->model('extension/d_blog_module/category');
            $this->load->model('setting/setting');
            $config = $this->model_setting_setting->getSetting('d_blog_module', $this->config->get('config_store_id'));
            $bm_category_id = (isset($config['d_blog_module_setting']['category']['main_category_id'])) ? $config['d_blog_module_setting']['category']['main_category_id'] : 0;
            $bm_children_data = array();
            $children = $this->model_extension_d_blog_module_category->getCategories($bm_category_id);
            foreach ($children as $child) {
                $bm_children_data[] = array(
                    'name' => $child['title'],
                    'href' => $this->url->link('extension/d_blog_module/category', 'bm_category_id=' . $child['category_id'])
                );
            }


            $data['text_blog'] = $this->language->get('text_blog');
            $data['blog'] = $this->url->link('extension/d_blog_module/category', 'bm_category_id=' . $bm_category_id, 'SSL');
            $data['categories'][] = array(
                'name'     => $this->language->get('text_blog'),
                'children' => $bm_children_data,
                'column'   => 1,
                'href'     => $this->url->link('extension/d_blog_module/category', 'bm_category_id=' . $bm_category_id, 'SSL')
            );
        }
    }

    public function view_category_before(&$view, &$data, &$output)
    {
        if (isset($data['description'])) {

            $designer_data = array(
                'config'     => 'd_blog_module_category',
                'content'    => $data['description'],
                'header'     => &$data['header'],
                'field_name' => 'category_description[' . (int)$this->config->get('config_language_id') . '][description]',
                'id'         => $data['category_id']
            );
            $this->load->model('extension/module/d_visual_designer');
            $data['description'] = $this->load->controller('extension/d_visual_designer/designer', $designer_data);
            $data['description'] = html_entity_decode($data['description'], ENT_QUOTES, 'UTF-8');
        }
    }

    public function view_post_before(&$view, &$data)
    {

        // edited
        if (isset($data['description'])) {

            $designer_data = array(
                'config'     => 'd_blog_module_post',
                'content'    => $data['description'],
                'header'     => &$data['header'],
                'field_name' => 'post_description[' . (int)$this->config->get('config_language_id') . '][description]',
                'id'         => $data['post_id']
            );
            $this->load->model('extension/module/d_visual_designer');
            $data['description'] = $this->load->controller('extension/d_visual_designer/designer', $designer_data);
            $data['description'] = html_entity_decode($data['description'], ENT_QUOTES, 'UTF-8');
        }
    }

    public function view_author_before(&$view, &$data, &$output)
    {
        if (isset($data['description'])) {

            $designer_data = array(
                'config'     => 'd_blog_module_author',
                'content'    => $data['description'],
                'header'     => &$data['header'],
                'field_name' => 'description[' . (int)$this->config->get('config_language_id') . '][description]',
                'id'         => $data['author_id']
            );
            $this->load->model('extension/module/d_visual_designer');
            $data['description'] = $this->load->controller('extension/d_visual_designer/designer', $designer_data);
            $data['description'] = html_entity_decode($data['description'], ENT_QUOTES, 'UTF-8');
        }

    }

    public function model_design_layout_getLayout_after(&$view, &$data, &$output)
    {
        if (isset($this->request->get['category_id'])) {
            $category_id = $this->request->get['category_id'];

            $this->load->model('extension/d_blog_module/category');
            $layout_id = $this->model_extension_d_blog_module_category->getCategoryLayoutId($category_id);
            if ($layout_id) {
                $output = $layout_id;
            }
        }

        if (isset($this->request->get['post_id'])) {

            $post_id = $this->request->get['post_id'];

            $this->load->model('extension/d_blog_module/post');
            $layout_id = $this->model_extension_d_blog_module_post->getPostLayoutId($post_id);
            if ($layout_id) {
                $output = $layout_id;
            }
        }
    }

    public function model_post_getPost_after(&$view, &$data, &$output)
    {
        if (isset($data['description'])) {
            $designer_data = array(
                'config'     => 'd_visual_designer_module',
                'content'    => $data['description'],
                'field_name' => 'description[' . (int)$this->config->get('config_language_id') . '][description]',
                'id'         => $data['module_id']
            );
            $data['description'] = $this->load->controller('extension/d_visual_designer/designer', $designer_data);

            $data['description'] = html_entity_decode($data['description'], ENT_QUOTES, 'UTF-8');
        }
    }
}
