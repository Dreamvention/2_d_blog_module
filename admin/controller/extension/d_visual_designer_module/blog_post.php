<?php
/*
*  location: admin/controller
*/

class ControllerExtensionDVisualDesignerModuleBlogPost extends Controller
{
    private $codename = 'blog_post';
    private $route = 'extension/d_visual_designer_module/blog_post';
    private $sub_versions = array('lite', 'light', 'free');

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->load->language($this->route);
        $this->load->model($this->route);
        $this->load->model('extension/d_opencart_patch/load');
        $this->load->model('extension/module/d_blog_module');
        $this->load->model('extension/d_opencart_patch/url');
        $this->load->config('d_blog_module');
        $this->setting = $this->config->get('d_blog_module_setting');

    }


    public function setting($setting)
    {
        $data['posts'] = array();
        if (!empty($setting['posts'])) {
            foreach ($this->model_extension_d_visual_designer_module_blog_post->getPosts(array('posts' => $setting['posts'])) as $post) {
                $name = html_entity_decode($post['title'], ENT_QUOTES, 'UTF-8');

                $data['posts'][] = array(
                    'id'    => $post['post_id'],
                    'name' => $name
                );
            }
        }

        return $data;
    }

    public function local($permission)
    {
        $data = array();
        $this->load->language($this->route);
        $data['entry_title'] = $this->language->get('entry_title');
        $data['entry_layout'] = $this->language->get('entry_layout');
        $data['entry_limit'] = $this->language->get('entry_limit');
        $data['entry_mode'] = $this->language->get('entry_mode');
        $data['text_custom_products'] = $this->language->get('text_custom_products');
        $data['text_featured'] = $this->language->get('text_featured');
        return $data;
    }

    public function options($permission)
    {
        $data = array();
        $data['modes'] = array(
            'latest'   => $this->language->get('text_latest'),
            'popular'  => $this->language->get('text_popular'),
            'featured' => $this->language->get('text_featured'),
        );
        $data['autocomplete_posts'] = $this->model_extension_d_opencart_patch_url->ajax($this->route.'/autocomplete_posts');
        return $data;
    }



    public function autocomplete_posts()
    {
        $json = array();

        if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_id'])) {
            if (isset($this->request->get['filter_name'])) {
                $filter_name = $this->request->get['filter_name'];
            } else {
                $filter_name = '';
            }

            if (isset($this->request->get['filter_id'])) {
                $filter_post_id = $this->request->get['filter_id'];
            } else {
                $filter_post_id = '';
            }

            if (isset($this->request->get['limit'])) {
                $limit = $this->request->get['limit'];
            } else {
                $limit = 5;
            }

            if (!$filter_post_id) {
                $filter_data = array(
                    'filter_name' => $filter_name,
                    'start'       => 0,
                    'limit'       => $limit
                );

                $results = $this->model_extension_d_visual_designer_module_blog_post->getPosts($filter_data);
            } else {
                $results = array($this->model_extension_d_visual_designer_module_blog_post->getPost($filter_post_id));
            }

            foreach ($results as $result) {
                $option_data = array();
                $json[] = array(
                    'id'   => $result['post_id'],
                    'name' => strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8'))
                );
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function autocompleteCategories()
    {
        $json = array();

        if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_id'])) {
            $this->load->model('catalog/category');

            if (isset($this->request->get['filter_name'])) {
                $filter_name = $this->request->get['filter_name'];
            } else {
                $filter_name = '';
            }

            if (isset($this->request->get['filter_id'])) {
                $filter_category_id = $this->request->get['filter_id'];
            } else {
                $filter_category_id = '';
            }

            if (isset($this->request->get['limit'])) {
                $limit = $this->request->get['limit'];
            } else {
                $limit = 5;
            }

            if (!$filter_category_id) {
                $filter_data = array(
                    'filter_name' => $filter_name,
                    'start'       => 0,
                    'limit'       => $limit
                );
                $results = $this->{'model_extension_d_visual_designer_module_' . $this->codename}->getCategories($filter_data);
            } else {
                $results = array($this->model_catalog_category->getCategory($filter_category_id));
            }
            foreach ($results as $result) {

                $json[] = array(
                    'id'   => $result['category_id'],
                    'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                );
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
