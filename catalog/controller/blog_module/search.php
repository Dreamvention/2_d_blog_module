<?php
namespace Opencart\Catalog\Controller\Extension\BlogModule\BlogModule;

class Search extends \Opencart\System\Engine\Controller
{
    private $id = 'blog_module';
    private $route = 'extension/blog_module/blog_module/search';
    private $sub_versions = array('lite', 'light', 'free');
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

        $this->load->language('extension/blog_module/blog_module/search');
		if ($this->language->get('text_tag') == 'text_tag') {
			$this->load->language('extension/blog_module/blog_module/search', '', 'en-gb');
		}
        $this->load->model('extension/blog_module/module/blog_module');
        $this->load->model('extension/blog_module/blog_module/category');
        $this->load->model('extension/blog_module/blog_module/post');
        $this->load->model('tool/image');

        $this->session->data['blog_module_debug'] = $this->config->get('blog_module_debug');

        $this->config_file = $this->model_extension_blog_module_module_blog_module->getConfigFile($this->id, $this->sub_versions);

        $this->setting = $this->model_extension_blog_module_module_blog_module->getConfigData($this->id, $this->id . '_setting', $this->config->get('config_store_id'), $this->config_file);
    }

    public function index() {

        if(!$this->config->get('blog_module_status')){
            $this->response->redirect($this->url->link('error/not_found'));
        }

		$this->document->addStyle('extension/blog_module/catalog/view/javascript/d_bootstrap_rating/bootstrap-rating.css');
        $this->document->addScript('extension/blog_module/catalog/view/javascript/d_bootstrap_rating/bootstrap-rating.min.js');

        $this->document->addStyle('extension/blog_module/catalog/view/stylesheet/blog_module/blog_module.css');
        $this->document->addStyle('extension/blog_module/catalog/view/stylesheet/blog_module/bootstrap.css');

        if (is_file(DIR_EXTENSION . 'blog_module_pack/catalog/view/stylesheet/blog_module/theme/'.$this->setting['theme'].'.css')) {
        	$this->document->addStyle('extension/blog_module_pack/catalog/view/stylesheet/blog_module/theme/'.$this->setting['theme'].'.css');
		} else {
			$this->document->addStyle('extension/blog_module/catalog/view/stylesheet/blog_module/theme/'.$this->setting['theme'].'.css');
		}

        $title = $this->language->get('text_search');
        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        }
        else {
            $page = 1;
        }

        if (isset($this->request->get['tag'])) {
            $tag = $this->request->get['tag'];
            $title = $this->language->get('text_tag') . ' ' . $this->request->get['tag'];
        }
        else {
            $tag = '';
        }

        if (isset($this->request->get['date_published'])) {
            preg_match('/(\d*)-(\d*)/', $this->request->get['date_published'], $date_published_s);

            if(isset($date_published_s[0])){
                $date_published = $date_published_s[0];
                $title = sprintf($this->language->get('text_date_published'), date("F Y", strtotime('01-'.$date_published)));
            }else{
                $date_published = '';
            }
        }
        else {
            $date_published = '';
        }

        if (isset($this->request->get['limit'])) {
            $limit = $this->request->get['limit'];
        }
        else {
            $limit = $this->setting['category']['post_page_limit'];
        }

        $url = '';
        $data['setting'] = $this->setting;

        //breadcrumbs
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home','','SSL')
            );

        $main_category_info = $this->model_extension_blog_module_blog_module_category->getCategory($this->setting['category']['main_category_id']);

        $data['breadcrumbs'][] = array(
            'text' => $main_category_info['title'],
            'href' => $this->url->link('extension/blog_module/blog_module/category','bm_category_id='. $this->setting['category']['main_category_id'],'SSL')
            );

        $data['breadcrumbs'][] = array(
            'text' => $title,
            'href' => ''
        );

        $data['heading_title'] = $title;
        $data['text_categories'] = $this->language->get('text_categories');
        $data['text_tags'] = $this->language->get('text_tags');
        $data['text_empty'] = $this->language->get('text_empty');
        $data['text_views'] = $this->language->get('text_views');
        $data['text_review'] = $this->language->get('text_review');
        $data['text_read_more'] = $this->language->get('text_read_more');
        $data['button_continue'] = $this->language->get('button_continue');
        $data['continue'] = $this->url->link('common/home', '', 'SSL');

        $data['custom_style'] = $this->setting['design']['custom_style'];

        //posts
        $data['posts'] = array();
        $filter_data = array('filter_tag' => $tag, 'filter_date_published' => $date_published, 'limit' => $limit, 'start' => ($page - 1) * $limit);

        $post_total = $this->model_extension_blog_module_blog_module_post->getTotalPosts($filter_data);
        $posts = $this->model_extension_blog_module_blog_module_post->getPosts($filter_data);
        $layout = $this->setting['category']['layout'];
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
                }
                else {
                    $row = 0;
                    $col_count = $layout[$row];
                }

                $data['posts'][] = array(
                    'post' => $this->load->controller('extension/blog_module/blog_module/post|thumb', $post['post_id']),
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
        $limits = array_unique(array($this->setting['category']['post_page_limit'], 25, 50, 75, 100));
        sort($limits);

        $url = '';

        if($tag){
            $url .= '&tag='.$tag;
        }

        if($date_published){
            $url .= '&date_published='.$date_published;
        }

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $post_total,
			'page'  => $page,
			'limit' => $limit,
			'url'   => $this->url->link('extension/blog_module/blog_module/search',  $url . '&page={page}', 'SSL')
		]);

        $data['results'] = sprintf($this->language->get('text_pagination'), ($post_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($post_total - $limit)) ? $post_total : ((($page - 1) * $limit) + $limit), $post_total, ceil($post_total / $limit));

        if ($page == 1) {
            $this->document->addLink($this->url->link('extension/blog_module/blog_module/search',  $url , 'SSL'), 'canonical');
        } elseif ($page == 2) {
            $this->document->addLink($this->url->link('extension/blog_module/blog_module/search',  $url, 'SSL'), 'prev');
        } else {
            $this->document->addLink($this->url->link('extension/blog_module/blog_module/search',  $url . '&page='. ($page - 1), 'SSL'), 'prev');
        }

        if ($limit && ceil($post_total / $limit) > $page) {
            $this->document->addLink($this->url->link('extension/blog_module/blog_module/search',  $url . '&page='. ($page + 1), 'SSL'), 'next');
        }

        //metas
        $this->document->setTitle($this->language->get('text_title'));
        $this->document->setDescription($this->language->get('meta_description'));
        $this->document->setKeywords($this->language->get('meta_keyword'));

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('extension/blog_module/blog_module/search', $data));
    }
}
