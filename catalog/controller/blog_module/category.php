<?php
namespace Opencart\Catalog\Controller\Extension\BlogModule\BlogModule;

class Category extends \Opencart\System\Engine\Controller
{
    private $id = 'blog_module';
    private $route = 'extension/blog_module/blog_module/category';
    private $sub_versions = array('lite', 'light', 'free');
    private $mbooth = '';
    private $prefix = '';
    private $config_file = '';
    private $error = array();
    private $debug = false;
    private $setting = array();

    public function __construct($registry)
    {
        parent::__construct($registry);
        if (!isset($this->user)) {
            $this->user = new \Opencart\System\Library\Cart\User($registry);
        }

        $this->load->language('extension/blog_module/blog_module/category');
		if ($this->language->get('text_read_more') == 'text_read_more') {
			$this->load->language('extension/blog_module/blog_module/category', '', 'en-gb');
		}

        $this->load->model('extension/blog_module/module/blog_module');
        $this->load->model('extension/blog_module/blog_module/category');
        $this->load->model('extension/blog_module/blog_module/post');
        $this->load->model('tool/image');

        $this->session->data['blog_module_debug'] = $this->config->get('blog_module_debug');
        $this->config_file = $this->model_extension_blog_module_module_blog_module->getConfigFile($this->id, $this->sub_versions);
        $this->setting = $this->model_extension_blog_module_module_blog_module->getConfigData($this->id, $this->id . '_setting', $this->config->get('config_store_id'), $this->config_file);
    }

    public function index()
    {

        if (!$this->config->get('blog_module_status')) {
            $this->response->redirect($this->url->link('error/not_found'));
        }

        $styles = array(
            'blog_module/blog_module.css',
            'blog_module/bootstrap.css',
        );

        $scripts = array(
            'blog_module/category.js'
        );

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        if (!empty($this->request->get['bm_category_id'])) {
            $category_id = (int)$this->request->get['bm_category_id'];
        } else {
            $category_id = (int)$this->setting['category']['main_category_id'];
        }

        $url = '';

        $data['category_id'] = $category_id;

        //category_info
        $category_info = $this->model_extension_blog_module_blog_module_category->getCategory($category_id);


        if (!empty($category_info['custom'])) {
            $this->setting['category'] = array_merge($this->setting['category'], (array)$category_info['setting']);
        }

        $data['setting'] = $this->setting;

		if (isset($this->request->get['limit'])) {
            $limit = $this->request->get['limit'];
        } else {
            $limit = $this->setting['category']['post_page_limit'];
        }

        //category
        $parents = array();
        if ($category_info) {
            $parents = $this->model_extension_blog_module_blog_module_category->getCategoryParents($category_id);
            $category_info['image_width'] = (!empty($category_info['image_width'])) ? $category_info['image_width'] : $this->setting['category']['image_width'];
            $category_info['image_height'] = (!empty($category_info['image_height'])) ? $category_info['image_height'] : $this->setting['category']['image_height'];
            $layout = (!empty($category_info['layout'])) ? $category_info['layout'] : $this->setting['category']['layout'];
        } else {
            $category_info['category_id'] = 0;
            $category_info['title'] = $this->language->get('heading_title');
            $category_info['image'] = false;
            $layout = $this->setting['category']['layout'];
        }


        //edit
        $data['text_edit'] = $this->language->get('text_edit');
        $data['edit'] = false;
        if ($this->user->isLogged()) {
            $site_link = $this->config->get('config_secure') ? $this->config->get('config_ssl') : $this->config->get('config_url');

            $data['edit'] = $site_link . $this->setting['dir_admin'] . '/index.php?route=extension/blog_module/blog_module/category|edit&bm_category_id=' . $category_id . '&user_token=' . $this->session->data['user_token'];
        }

        //breadcrumbs
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array('text' => $this->language->get('text_home'), 'href' => $this->url->link('common/home'), '', 'SSL');
        if ($this->setting['category']['main_category_id'] == 0 && $this->setting['category']['main_category_id'] !== $category_id) {
            $data['breadcrumbs'][] = array('text' => $this->language->get('heading_title'), 'href' => $this->url->link('extension/blog_module/blog_module/category', 'bm_category_id=' . $this->setting['category']['main_category_id'], 'SSL'));
        }
        foreach ($parents as $parent) {
            $data['breadcrumbs'][] = array(
                'text' => $parent['title'],
                'href' => $this->url->link('extension/blog_module/blog_module/category', 'bm_category_id=' . $parent['category_id'], 'SSL')
            );
        }
        $data['breadcrumbs'][] = array('text' => $category_info['title']);

        $data['heading_title'] = $category_info['title'];

        $data['description'] = html_entity_decode(($category_info['description'] ?? ''), ENT_QUOTES, 'UTF-8');
        $data['continue'] = $this->url->link('common/home', '', 'SSL');

        $layout_type = $this->setting['category']['layout_type'];
        if (is_file(DIR_EXTENSION . 'blog_module_pack/system/config/blog_module_layout/' . $layout_type . '.php')) {
			$this->config->addPath(DIR_EXTENSION . 'blog_module_pack/system/config/');
		} else {
			$this->config->addPath(DIR_EXTENSION . 'blog_module/system/config/');
		}
        $this->load->config('blog_module_layout/' . $layout_type);
        $layout_type = $this->config->get('blog_module_layout');

        $data['layout_template'] = (string)$layout_type['template'] . '.twig';

        if (isset($layout_type['styles'])) {
            $styles = array_merge($layout_type['styles'], $styles);
        }

        if (isset($layout_type['scripts'])) {
            $scripts = array_merge($layout_type['scripts'], $scripts);
        }

        $data['custom_style'] = $this->setting['design']['custom_style'];

        //cateogry image
        if ($category_info['image']) {
            $data['thumb'] = $this->model_tool_image->resize($category_info['image'], $category_info['image_width'], $category_info['image_height']);
        } else {
            $data['thumb'] = '';
        }

        if (isset($this->request->get['format'])) {
            $format = $this->request->get['format'];
            if ($this->format($format, $data)) {
                return false;
            }
        }

        //categories
        $data['categories'] = array();
        $categories = $this->model_extension_blog_module_blog_module_category->getCategories($category_id);
        if ($this->setting['category']['sub_category_display']) {

            // subcategory
            foreach ($categories as $category) {
                $filter_data = array('filter_category_id' => $category['category_id'], 'filter_sub_category' => true);

                if ($category['image']) {
                    $thumb = $this->model_tool_image->resize($category['image'], $this->setting['category']['sub_category_image_width'], $this->setting['category']['sub_category_image_height']);
                } else {
                    $thumb = $this->model_tool_image->resize('placeholder.png', $this->setting['category']['sub_category_image_width'], $this->setting['category']['sub_category_image_height']);
                }

                $data['categories'][] = array(
                    'thumb' => $thumb,
                    'title' => $category['title'] . ($this->setting['category']['sub_category_post_count'] ? ' (' . $this->model_extension_blog_module_blog_module_post->getTotalPostsByCategoryId($category['category_id']) . ')' : ''),
                    'href'  => $this->url->link('extension/blog_module/blog_module/category', 'bm_category_id=' . $category['category_id'] . $url, 'SSL'),
                    'col'   => ($this->setting['category']['sub_category_col']) ? round(12 / $this->setting['category']['sub_category_col']) : 12
                );

            }
        }

        //posts
        $data['posts'] = array();
        if ($category_id == $this->setting['category']['main_category_id']) {
            $filter_data = array('limit' => $limit, 'start' => ($page - 1) * $limit,);
        } else {
            $filter_data = array('filter_category_id' => $category_id, 'limit' => $limit, 'start' => ($page - 1) * $limit,);
        }
        $post_total = $this->model_extension_blog_module_blog_module_post->getTotalPosts($filter_data);
        $posts = $this->model_extension_blog_module_blog_module_post->getPosts($filter_data);

        $new_row = false;
        if ($posts) {
            $post_thumb = $this->setting['post_thumb'];
            $data['post_thumb'] = $post_thumb;

            $row_count = count($layout);
            $row = 0;
            $col = 0;

            foreach ($posts as $post) {
                if (isset($layout[$row])) {
                    $col_count = $layout[$row];
                } else {
                    $row = 0;
                    $col_count = $layout[$row];
                }

                $data['posts'][] = array(
                    'post'      => $this->load->controller('extension/blog_module/blog_module/post|thumb', $post['post_id']),
                    'col_count' => $col_count,
                    'animate'   => $this->setting['post_thumb']['animate'],
                    'col'       => ($col_count) ? round(12 / $col_count) : 12,
                    'row'       => $new_row
                );

                $col++;
                $new_row = false;
                if ($col >= $col_count) {
                    $col = 0;
                    $row++;
                    $new_row = true;
                }
            }
        }

        ;
        $data['limits'] = array();
        $limits = array_unique(array($this->setting['category']['post_page_limit'], 25, 50, 75, 100));
        sort($limits);

        $url = '';

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $post_total,
			'page'  => $page,
			'limit' => $limit,
			'url'   => $this->url->link('extension/blog_module/blog_module/category', 'bm_category_id=' . $category_info['category_id'] . $url . '&page={page}', 'SSL')
		]);

        $data['results'] = sprintf($this->language->get('text_pagination'), ($post_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($post_total - $limit)) ? $post_total : ((($page - 1) * $limit) + $limit), $post_total, ceil($post_total / $limit));

        if ($page == 1) {
            $this->document->addLink($this->url->link('extension/blog_module/blog_module/category', 'bm_category_id=' . $category_info['category_id'], 'SSL'), 'canonical');
        } elseif ($page == 2) {
            $this->document->addLink($this->url->link('extension/blog_module/blog_module/category', 'bm_category_id=' . $category_info['category_id'], 'SSL'), 'prev');
        } else {
            $this->document->addLink($this->url->link('extension/blog_module/blog_module/category', 'bm_category_id=' . $category_info['category_id'] . '&page=' . ($page - 1), 'SSL'), 'prev');
        }

        if ($limit && ceil($post_total / $limit) > $page) {
            $this->document->addLink($this->url->link('extension/blog_module/blog_module/category', 'bm_category_id=' . $category_info['category_id'] . '&page=' . ($page + 1), 'SSL'), 'next');
        }


        $styles[] = 'blog_module/theme/' . $this->setting['theme'] . '.css';
        $this->document->addStyle('extension/blog_module/catalog/view/javascript/d_bootstrap_rating/bootstrap-rating.css');
        $this->document->addScript('extension/blog_module/catalog/view/javascript/d_bootstrap_rating/bootstrap-rating.min.js');

        foreach($styles as $style){
			if (is_file(DIR_EXTENSION . 'blog_module_pack/catalog/view/stylesheet/'.$style)) {
				$this->document->addStyle('extension/blog_module_pack/catalog/view/stylesheet/'.$style);
			} else {
				$this->document->addStyle('extension/blog_module/catalog/view/stylesheet/'.$style);
			}
		}

		foreach($scripts as $script){
			if (is_file(DIR_EXTENSION . 'blog_module_pack/catalog/view/javascript/'.$script)) {
				$this->document->addScript('extension/blog_module_pack/catalog/view/javascript/'.$script);
			} else {
				$this->document->addScript('extension/blog_module/catalog/view/javascript/'.$script);
			}
		}

        //metas
        $this->document->setTitle($category_info['meta_title'] ?? '');
        $this->document->setDescription($category_info['meta_description'] ?? '');
        $this->document->setKeywords($category_info['meta_keyword'] ?? '');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('extension/blog_module/blog_module/category', $data));

    }

    public function format($format, $json)
    {
        if ($format == 'json') {
            $this->response->addHeader('Content-Type: application/json');

            if (isset($this->request->get['callback'])) {
                $this->response->setOutput($this->request->get['callback'] . '(' . json_encode($json) . ');');
            } else {
                $this->response->setOutput(json_encode($json));
            }

            return true;
        }

        return false;
    }

    public function saveCategory($setting)
    {
        $result = false;

        if (!empty($setting['content']['description']) && !empty($setting['id'])) {
            $this->model_extension_blog_module_blog_module_category->editCategory($setting['id'], $setting['content']);
            $result = true;
        }

        return $result;
    }


    public function editCategory()
    {
        $json = array();

        if (!empty($this->request->post['description'])) {
            $description = $this->request->post['description'];
        }

        if (!empty($this->request->get['id'])) {
            $category_id = $this->request->get['id'];
        }

        if (isset($description) && isset($category_id)) {

            $this->model_extension_blog_module_blog_module_category->editCategory($category_id, array('description' => $description));

            $json['success'] = 'success';
        } else {
            $json['error'] = 'error';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
