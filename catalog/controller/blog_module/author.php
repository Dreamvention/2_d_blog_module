<?php
namespace Opencart\Catalog\Controller\Extension\BlogModule\BlogModule;

class Author extends \Opencart\System\Engine\Controller {
    private $id = 'blog_module';
    private $route = 'extension/blog_module/blog_module/author';
    private $sub_versions = array('lite', 'light', 'free');
    private $mbooth = '';
    private $prefix = '';
    private $config_file = '';
    private $error = array();
    private $debug = false;
    private $setting = array();

    public function __construct($registry) {
        parent::__construct($registry);
        if(!isset($this->user)){
             $this->user = new \Opencart\System\Library\Cart\User($registry);
        }

        $this->load->language('extension/blog_module/blog_module/author');

		if ($this->language->get('text_edit') == 'text_edit') {
			$this->load->language('extension/blog_module/blog_module/author', '', 'en-gb');
		}
        $this->load->model('extension/blog_module/module/blog_module');
        $this->load->model('extension/blog_module/blog_module/category');
        $this->load->model('extension/blog_module/blog_module/post');
        $this->load->model('extension/blog_module/blog_module/author');
        $this->load->model('tool/image');
        $this->load->model('account/customer');

        $this->session->data['blog_module_debug'] = $this->config->get('blog_module_debug');

        $this->config_file = $this->model_extension_blog_module_module_blog_module->getConfigFile($this->id, $this->sub_versions);

        $this->setting = $this->model_extension_blog_module_module_blog_module->getConfigData($this->id, $this->id.'_setting', $this->config->get('config_store_id'),$this->config_file);
    }

    public function index() {

        if(!$this->config->get('blog_module_status')) {
            $this->response->redirect($this->url->link('error/not_found'));
        }

        $styles = array(
            'blog_module/blog_module.css',
            'blog_module/bootstrap.css',
        );

        $scripts = array(
            'blog_module/author.js'
        );

        if (isset($this->request->get['author_id'])) {
            $author_id = (int) $this->request->get['author_id'];
        } else {
            $author_id = 0;
        }
        $data['author_id'] = $author_id;
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', '', 'SSL')
            );

        $main_category_info = $this->model_extension_blog_module_blog_module_category->getCategory($this->setting['category']['main_category_id']);

        if(empty($main_category_info)){
            $this->load->language('extension/blog_module/blog_module/category');
			if ($this->language->get('text_read_more') == 'text_read_more') {
				$this->load->language('extension/blog_module/blog_module/category', '', 'en-gb');
			}
            $main_category_info['category_id'] = 0;
            $main_category_info['title'] = $this->language->get('heading_title');
            $main_category_info['description'] = $this->language->get('description');
            $main_category_info['meta_title'] = $this->language->get('meta_title');
            $main_category_info['meta_keyword'] = $this->language->get('meta_keyword');
            $main_category_info['meta_description'] = $this->language->get('meta_description');
            $main_category_info['image'] = false;
            $layout = $this->setting['category']['layout'];
        }

        $data['breadcrumbs'][] = array(
            'text' => $main_category_info['title'],
            'href' => $this->url->link('extension/blog_module/blog_module/category','bm_category_id='. $this->setting['category']['main_category_id'],'SSL')
            );

        $data['setting'] = $this->setting;

        //edit
        $data['text_edit'] = $this->language->get('text_edit');
        $data['edit'] = false;
        if(isset($this->session->data['user_id']) && $this->session->data['user_id']){
            $site_link = $this->config->get('config_secure') ? $this->config->get('config_ssl') : $this->config->get('config_url');

            if (VERSION >= '3.0.0.0') {
                $data['edit'] = $site_link . $this->setting['dir_admin'] . '/index.php?route=extension/blog_module/blog_module/author|edit&author_id=' . $author_id . '&user_token=' . $this->session->data['user_token'];
            } else {
                $data['edit'] = $site_link . $this->setting['dir_admin'] . '/index.php?route=extension/blog_module/blog_module/author|edit&author_id=' . $author_id . '&token=' . $this->session->data['token'];
            }
        }

        $data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL'));

        $data['button_continue'] = $this->language->get('button_continue');

        $data['custom_style'] = $this->setting['design']['custom_style'];

        $author = $this->model_extension_blog_module_blog_module_author->getAuthor($author_id);

        $layout_type = $this->setting['author']['layout_type'];
		if (is_file(DIR_EXTENSION . 'blog_module_pack/system/config/blog_module_layout/' . $layout_type . '.php')) {
			$this->config->addPath(DIR_EXTENSION . 'blog_module_pack/system/config/');
		} else {
			$this->config->addPath(DIR_EXTENSION . 'blog_module/system/config/');
		}

        $this->load->config('blog_module_layout/'.$layout_type);
        $layout_type = $this->config->get('blog_module_layout');
        $data['layout_template'] = $layout_type['template'] . '.twig';

        if(isset($layout_type['styles'])){
           $styles = array_merge($layout_type['styles'], $styles);
        }

        if(isset($layout_type['scripts'])){
           $scripts = array_merge($layout_type['scripts'], $scripts);
        }
        if(!empty($author)) {
            $data['heading_title'] = $author['name'];

		    $data['breadcrumbs'][] = array(
                'text' => $author['name']
            );

            $data['short_description'] =  strip_tags(html_entity_decode($author['short_description'], ENT_QUOTES, 'UTF-8'));

            $data['description'] = html_entity_decode($author['description'], ENT_QUOTES, 'UTF-8');

            if ($author['image']) {
                $data['thumb'] = $this->model_tool_image->resize($author['image'], $this->setting['author']['image_width'], $this->setting['author']['image_height']);
            } else {
                $data['thumb'] = $this->model_tool_image->resize('placeholder.png', $this->setting['author']['image_width'], $this->setting['author']['image_height']);
            }

            if(isset($this->request->get['format'])){
                $format = $this->request->get['format'];
                if($this->format($format, $data)){
                    return false;
                }
            }

            if ($this->config->get('config_google_captcha_status')) {
                $this->document->addScript('https://www.google.com/recaptcha/api.js');

                $data['site_key'] = $this->config->get('config_google_captcha_public');
            } else {
                $data['site_key'] = '';
            }
            if (isset($this->request->get['page'])) {
                $page = $this->request->get['page'];
            }
            else {
                $page = 1;
            }
            if (isset($this->request->get['limit'])) {
                $limit = $this->request->get['limit'];
            }
            else {
                $limit = $this->setting['author']['post_page_limit'];
            }

            $filter_data = array('filter_author_id' => $author_id, 'limit' => $limit, 'start' => ($page - 1) * $limit,);

            $post_total = $this->model_extension_blog_module_blog_module_post->getTotalPosts($filter_data);

            $posts = $this->model_extension_blog_module_blog_module_post->getPosts($filter_data);
            $new_row = false;
            $layout = $this->setting['author']['layout'];
            if ($posts) {
                $row_count = count($layout);
                $row = 0;
                $col = 0;

                foreach ($posts as $post) {
                    if (isset($layout[$row])) {
                        $col_count = $layout[$row];
                    }
                    else {
                        $row = 0;
                        $col_count = $layout[$row];
                    }

                    $data['posts'][] = array(
                        'post' => $this->load->controller('extension/blog_module/blog_module/post|thumb', $post['post_id']),
                        'col_count' => $col_count,
                        'animate' => $this->setting['post_thumb']['animate'],
                        'col' => ($col_count) ? round(12 / $col_count) : 12,
                        'row' => $new_row,);

                    $col++;
                    $new_row = false;
                    if ($col >= $col_count) {
                        $col = 0;
                        $row++;
                        $new_row = true;
                    }
                }
            }

            $data['limits'] = array();
            $limits = array_unique(array($this->setting['author']['post_page_limit'], 25, 50, 75, 100));
            sort($limits);

            $url = '';

            $data['pagination'] = $this->load->controller('common/pagination', [
				'total' => $post_total,
				'page'  => $page,
				'limit' => $limit,
				'url'   => $this->url->link('extension/blog_module/blog_module/author', 'author_id=' . $author_id . $url . '&page={page}', 'SSL')
			]);

            $data['results'] = sprintf($this->language->get('text_pagination'), ($post_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($post_total - $limit)) ? $post_total : ((($page - 1) * $limit) + $limit), $post_total, ceil($post_total / $limit));

            if ($page == 1) {
                $this->document->addLink($this->url->link('extension/blog_module/blog_module/author', 'author_id=' . $author_id, 'SSL'), 'canonical');
            } elseif ($page == 2) {
                $this->document->addLink($this->url->link('extension/blog_module/blog_module/author', 'author_id=' . $author_id, 'SSL'), 'prev');
            } else {
                $this->document->addLink($this->url->link('extension/blog_module/blog_module/author', 'author_id=' . $author_id . '&page='. ($page - 1), 'SSL'), 'prev');
            }

            if ($limit && ceil($post_total / $limit) > $page) {
                $this->document->addLink($this->url->link('extension/blog_module/blog_module/author', 'author_id=' . $author_id . '&page='. ($page + 1), 'SSL'), 'next');
            }

            $styles[] = 'blog_module/theme/'.$this->setting['theme'].'.css';

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
            $this->document->setTitle($author['meta_title']);
            $this->document->setDescription($author['meta_description']);
            $this->document->setKeywords($author['meta_keyword']);

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');


            $this->response->setOutput($this->load->view('extension/blog_module/blog_module/author', $data));

        } else {
            $url = '';

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_error'),
                'href' => $this->url->link('extension/blog_module/blog_module/author', $url . '&author_id=' . $author_id, 'SSL')
                );

            $this->document->setTitle($this->language->get('text_error'));

            $data['heading_title'] = $this->language->get('text_error');

            $data['text_error'] = $this->language->get('text_error');

            $data['button_continue'] = $this->language->get('button_continue');

            $data['continue'] = $this->url->link('common/home');

            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');


            $this->response->setOutput($this->load->view('error/not_found', $data));
        }
    }

    public function format($format, $json){
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
    public function saveauthor($setting)
    {
        $result = false;
        $this->load->model('extension/d_opencart_patch/user');

        if (!empty($setting['content']['description']) && !empty($setting['id'])) {
            $this->model_extension_blog_module_blog_module_author->editAuthor($setting['id'], $setting['content']);
            $result = true;
        }

        return $result;
    }

    public function editAuthor() {
        $json = array();

        if(!empty($this->request->post['description'])){
            $description = $this->request->post['description'];
        }

        if(!empty($this->request->get['id'])){
            $author_id = $this->request->get['id'];
        }

        if(isset($description)&&isset($author_id)){

            $this->model_extension_blog_module_blog_module_author->editAuthor($author_id, array('description' => $description));

            $json['success'] = 'success';
        }
        else{
            $json['error'] = 'error';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
