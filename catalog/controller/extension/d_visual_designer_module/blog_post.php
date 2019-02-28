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
        $this->load->config('d_blog_module');
        $this->setting = $this->config->get('d_blog_module_setting');

        $this->load->model('extension/module/d_blog_module');
        $this->config_file = $this->model_extension_module_d_blog_module->getConfigFile('d_blog_module', $this->sub_versions);
        $this->setting = $this->model_extension_module_d_blog_module->getConfigData('d_blog_module', 'd_blog_module'. '_setting', $this->config->get('config_store_id'), $this->config_file);


    }

    public function index($setting)
    {
        if (empty($this->setting)){
            return;
        }
        $posts = array();
        $data = array();
        $filter_data = array(
            'start'      => 0,
            'limit'      => $setting['limit'],
            'categories' => $setting['categories'],
            'order'      => 'DESC'
        );
        switch ($setting['mode']) {
            case 'latest':
                $filter_data['sort'] = 'p.date_published';
                break;
            case 'popular':
                $filter_data['sort'] = 'p.viewed';
                break;
            case 'featured':
                $filter_data['posts'] = $setting['posts'];
                break;
            default:
                break;
        }



        if ($posts) {
            foreach ($posts as $post) {
                $data['posts'][] = array(
                    'animate' => $this->setting['post_thumb']['animate'],
                    'partial' => $this->model_extension_d_opencart_patch_load->view('extension/d_blog_module/post_thumb',
                        array(
                            'post'    => $this->load->controller('extension/d_blog_module/post/thumb', $post['post_id']),
                            'setting' => $this->setting,
                        )),
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


    public function scripts($permission)
    {
        $scripts = array();
        foreach ($scripts as $script) {
            if (file_exists(DIR_TEMPLATE . $this->theme . '/javascript/' . $script)) {
                $this->document->addScript('catalog/view/theme/' . $this->theme . '/javascript/' . $script);
            } else {
                $this->document->addScript('catalog/view/theme/default/javascript/' . $script);
            }
        }

    }

    public function styles($permission)
    {
        $data = array();

        $styles = array(
            'd_blog_module/d_blog_module.css',
            //'d_blog_module/bootstrap.css',
        );
        $styles[] = 'd_blog_module/theme/' . $this->setting['theme'] . '.css';

		if (VERSION >= '3.0.0.0') {
			$this->theme = $this->config->get('theme_' . $this->config->get('config_theme') . '_directory');
		} elseif (VERSION >= '2.2.0.0') {
			$this->theme = $this->config->get($this->config->get('config_theme') . '_directory');
		} else {
			$this->theme = $this->config->get('config_template');
		}
        
        foreach ($styles as $style) {
            if (file_exists(DIR_TEMPLATE . $this->theme . '/stylesheet/' . $style)) {
                $data[] = 'catalog/view/theme/' . $this->theme . '/stylesheet/' . $style;
            } else {
                $data[] = 'catalog/view/theme/default/stylesheet/' . $style;
            }
        }
        return $data;
    }
}
