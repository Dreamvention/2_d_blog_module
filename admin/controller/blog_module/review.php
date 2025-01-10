<?php
namespace Opencart\Admin\Controller\Extension\BlogModule\BlogModule;

class Review extends \Opencart\System\Engine\Controller {

    private $error = array();

    public function __construct($registry) {
        parent::__construct($registry);
    }

    public function index() {

        $this->load->language('extension/blog_module/blog_module/review');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/blog_module/blog_module/review');

        $this->getList();
    }

    public function add() {
        $this->load->language('extension/blog_module/blog_module/review');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/blog_module/blog_module/review');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_extension_blog_module_blog_module_review->addReview($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_post'])) {
                $url .= '&filter_post=' . urlencode(html_entity_decode($this->request->get['filter_post'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_author'])) {
                $url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['filter_date_added'])) {
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/blog_module/blog_module/review&user_token=' . $this->session->data['user_token'], $url));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('extension/blog_module/blog_module/review');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/blog_module/blog_module/review');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_extension_blog_module_blog_module_review->editReview($this->request->get['review_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_post'])) {
                $url .= '&filter_post=' . urlencode(html_entity_decode($this->request->get['filter_post'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_author'])) {
                $url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['filter_date_added'])) {
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/blog_module/blog_module/review&user_token=' . $this->session->data['user_token'], $url));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('extension/blog_module/blog_module/review');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/blog_module/blog_module/review');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $review_id) {
                $this->model_extension_blog_module_blog_module_review->deleteReview($review_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_post'])) {
                $url .= '&filter_post=' . urlencode(html_entity_decode($this->request->get['filter_post'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_author'])) {
                $url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['filter_date_added'])) {
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/blog_module/blog_module/review&user_token=' . $this->session->data['user_token'], $url));
        }

        $this->getList();
    }

    protected function getList() {
        $this->document->addScript(HTTP_CATALOG . 'extension/d_autocomplete/admin/view/javascript/d_autocomplete.js');

        if (isset($this->request->get['filter_post'])) {
            $filter_post = $this->request->get['filter_post'];
        } else {
            $filter_post = null;
        }

        if (isset($this->request->get['filter_author'])) {
            $filter_author = $this->request->get['filter_author'];
        } else {
            $filter_author = null;
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = null;
        }

        if (isset($this->request->get['filter_date_added'])) {
            $filter_date_added = $this->request->get['filter_date_added'];
        } else {
            $filter_date_added = null;
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'r.date_added';
            $order = 'DESC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_post'])) {
            $url .= '&filter_post=' . urlencode(html_entity_decode($this->request->get['filter_post'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_author'])) {
            $url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', '&user_token=' . $this->session->data['user_token'])
            );

        $data['breadcrumbs'][]  =   array(
            'text'  =>  $this->language->get('text_blog_module'),
            'href'  =>  $this->url->link('extension/blog_module/module/blog_module', '&user_token=' . $this->session->data['user_token'])
            );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/blog_module/blog_module/review&user_token=' . $this->session->data['user_token'], $url)
            );

        $data['add'] = $this->url->link('extension/blog_module/blog_module/review|add&user_token=' . $this->session->data['user_token'], $url);
        $data['delete'] = $this->url->link('extension/blog_module/blog_module/review|delete&user_token=' . $this->session->data['user_token'], $url);

        $data['reviews'] = array();

        $filter_data = array(
            'filter_post' => $filter_post,
            'filter_author' => $filter_author,
            'filter_status' => $filter_status,
            'filter_date_added' => $filter_date_added,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_pagination_admin'),
            'limit' => $this->config->get('config_pagination_admin')
            );

        $review_total = $this->model_extension_blog_module_blog_module_review->getTotalReviews($filter_data);

        $results = $this->model_extension_blog_module_blog_module_review->getReviews($filter_data);

        foreach ($results as $result) {
            $data['reviews'][] = array(
                'review_id' => $result['review_id'],
                'title' => $result['title'],
                'author' => $result['author'],
                'rating' => $result['rating'],
                'status' => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'edit' => $this->url->link('extension/blog_module/blog_module/review|edit&user_token=' . $this->session->data['user_token'], '&review_id=' . $result['review_id'] . $url)
                );
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');

        $data['column_post'] = $this->language->get('column_post');
        $data['column_author'] = $this->language->get('column_author');
        $data['column_rating'] = $this->language->get('column_rating');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_date_added'] = $this->language->get('column_date_added');
        $data['column_action'] = $this->language->get('column_action');

        $data['entry_post'] = $this->language->get('entry_post');
        $data['entry_author'] = $this->language->get('entry_author');
        $data['entry_rating'] = $this->language->get('entry_rating');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_date_added'] = $this->language->get('entry_date_added');

        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_filter'] = $this->language->get('button_filter');
        $data['button_remove'] = $this->language->get('button_remove');

        $data['token'] = $this->session->data['user_token'];

        $data['review_filter'] = $this->url->link('extension/blog_module/blog_module/review', '&user_token=' . $this->session->data['user_token']);

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array) $this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_post'] = $this->url->link('extension/blog_module/blog_module/review', '&sort=pd.name' . $url);
        $data['sort_author'] = $this->url->link('extension/blog_module/blog_module/review', '&sort=r.author' . $url);
        $data['sort_rating'] = $this->url->link('extension/blog_module/blog_module/review&user_token=' . $this->session->data['user_token'], '&sort=r.rating' . $url);
        $data['sort_status'] = $this->url->link('extension/blog_module/blog_module/review&user_token=' . $this->session->data['user_token'], '&sort=r.status' . $url);
        $data['sort_date_added'] = $this->url->link('extension/blog_module/blog_module/review&user_token=' . $this->session->data['user_token'], '&sort=r.date_added' . $url);

        $url = '';

        if (isset($this->request->get['filter_post'])) {
            $url .= '&filter_post=' . urlencode(html_entity_decode($this->request->get['filter_post'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_author'])) {
            $url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $review_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('extension/blog_module/blog_module/review', 'user_token=' . $this->session->data['user_token'] . '&' . $url . '&page={page}')
		]);

        $data['results'] = sprintf($this->language->get('text_pagination'), ($review_total)
            ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1
            : 0,
            ((($page - 1) * $this->config->get('config_pagination_admin')) > ($review_total - $this->config->get('config_pagination_admin')))
            ? $review_total
            : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')),
            $review_total, ceil($review_total / $this->config->get('config_pagination_admin')));

        $data['filter_post'] = $filter_post;
        $data['filter_author'] = $filter_author;
        $data['filter_status'] = $filter_status;
        $data['filter_date_added'] = $filter_date_added;

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/blog_module/blog_module/review_list', $data));
    }

    public function deleteImage(){
        if(isset($this->request->get['image']))
        {
            $image = $this->request->get['image'];
        }
        if(isset($this->request->get['review_id']))
        {
            $review_id = $this->request->get['review_id'];
        }
        if(isset($image) && isset($review_id))
        {
            $this->load->model('extension/blog_module/blog_module/review');
            $this->model_extension_blog_module_blog_module_review->deleteImageReview($review_id,$image);
            unlink(DIR_IMAGE.$image);
            $json['success'] = $this->language->get('text_upload');
        }
        else
        {
            $json['error'] = 'error';
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    protected function getForm() {
        $this->document->addScript(HTTP_CATALOG . 'extension/d_autocomplete/admin/view/javascript/d_autocomplete.js');
        
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_form'] = !isset($this->request->get['review_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');

        $data['entry_post'] = $this->language->get('entry_post');
        $data['entry_author'] = $this->language->get('entry_author');
        $data['entry_rating'] = $this->language->get('entry_rating');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_text'] = $this->language->get('entry_text');
        $data['entry_images'] = $this->language->get('entry_images');

        $data['column_path'] = $this->language->get('column_path');
        $data['column_image'] = $this->language->get('column_image');
        $data['column_action'] = $this->language->get('column_action');

        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['help_post'] = $this->language->get('help_post');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_remove'] = $this->language->get('button_remove');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['post'])) {
            $data['error_post'] = $this->error['post'];
        } else {
            $data['error_post'] = '';
        }

        if (isset($this->error['author'])) {
            $data['error_author'] = $this->error['author'];
        } else {
            $data['error_author'] = '';
        }

        if (isset($this->error['text'])) {
            $data['error_text'] = $this->error['text'];
        } else {
            $data['error_text'] = '';
        }

        if (isset($this->error['rating'])) {
            $data['error_rating'] = $this->error['rating'];
        } else {
            $data['error_rating'] = '';
        }

        $url = '';

        if (isset($this->request->get['filter_post'])) {
            $url .= '&filter_post=' . urlencode(html_entity_decode($this->request->get['filter_post'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_author'])) {
            $url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', '&user_token=' . $this->session->data['user_token'])
            );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/blog_module/blog_module/review&user_token=' . $this->session->data['user_token'], $url)
            );

        if (!isset($this->request->get['review_id'])) {
            $data['action'] = $this->url->link('extension/blog_module/blog_module/review|add&user_token=' . $this->session->data['user_token'], $url);
        } else {
            $data['action'] = $this->url->link('extension/blog_module/blog_module/review|edit&user_token=' . $this->session->data['user_token'], '&review_id=' . $this->request->get['review_id'] . $url);
        }

        $data['cancel'] = $this->url->link('extension/blog_module/blog_module/review&user_token=' . $this->session->data['user_token'], $url);

        if (isset($this->request->get['review_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $review_info = $this->model_extension_blog_module_blog_module_review->getReview($this->request->get['review_id']);
        }

        $data['token'] = $this->session->data['user_token'];

        $data['image_remove'] = $this->url->link('extension/blog_module/blog_module/review|deleteImage', '&user_token=' . $this->session->data['user_token']);
        $data['post_autocomplete'] = $this->url->link('extension/blog_module/blog_module/post|autocomplete');

        $this->load->model('extension/blog_module/blog_module/post');
        if (isset($this->request->post['post_id'])) {
            $data['post_id'] = $this->request->post['post_id'];
        } elseif (!empty($review_info)) {
            $data['post_id'] = $review_info['post_id'];
        } else {
            $data['post_id'] = '';
        }

        if (isset($this->request->get['review_id'])) {
            $data['review_id'] = $this->request->get['review_id'];
        }
        else
        {
            $data['review_id'] = '';
        }

        if (isset($this->request->post['post'])) {
            $data['post'] = $this->request->post['post'];
        } elseif (!empty($review_info)) {
            $data['post'] = $review_info['post'];
        } else {
            $data['post'] = '';
        }

        if (isset($this->request->post['author'])) {
            $data['author'] = $this->request->post['author'];
        } elseif (!empty($review_info)) {
            $data['author'] = $review_info['author'];
        } else {
            $data['author'] = '';
        }

        if (isset($this->request->post['description'])) {
            $data['description'] = $this->request->post['description'];
        } elseif (!empty($review_info)) {
            $data['description'] = $review_info['description'];
        } else {
            $data['description'] = '';
        }

        if (isset($this->request->post['rating'])) {
            $data['rating'] = $this->request->post['rating'];
        } elseif (!empty($review_info)) {
            $data['rating'] = $review_info['rating'];
        } else {
            $data['rating'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($review_info)) {
            $data['status'] = $review_info['status'];
        } else {
            $data['status'] = '';
        }
        if(isset($this->request->get['review_id']))
        {
          $images = $this->model_extension_blog_module_blog_module_review->getReviewImages($this->request->get['review_id']);
        }
        else {
          $images = array();
        }


        $this->load->model('tool/image');

        $data['images'] = array();
        foreach ($images as $image) {

            $thumb = $this->model_tool_image->resize($image['image'], 100, 100);
            $data['images'][]=array(
                'thumb' => $thumb,
                'href' => $image['image'],
                'delete' => $this->url->link('extension/blog_module/blog_module/review|deleteImage&user_token=' . $this->session->data['user_token'], 'image='.$image['image'] . $url)
            );
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/blog_module/blog_module/review_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'extension/blog_module/blog_module/review')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->model('extension/blog_module/blog_module/author');
        $this->load->model('extension/blog_module/blog_module/post');

        $current_author = $this->model_extension_blog_module_blog_module_author->getAuthorByUserId($this->user->getId());
        $post_author =  $this->model_extension_blog_module_blog_module_post->getAuthorByPost($this->request->post['post_id']);
        if($post_author['author_id'] != $current_author['author_id'])
        {
          if(isset($this->request->get['review_id'])){
            if	(!$this->model_extension_blog_module_blog_module_author->hasPermission('edit_others_reviews'))	{
              $this->error['warning']	=	$this->language->get('error_permission');
            }
          }
          else {
            if	(!$this->model_extension_blog_module_blog_module_author->hasPermission('add_others_reviews'))	{
              $this->error['warning']	=	$this->language->get('error_permission');
            }
          }
        }
        else {
          if(isset($this->request->get['preview_id'])){
            if	(!$this->model_extension_blog_module_blog_module_author->hasPermission('add_reviews'))	{
              $this->error['warning']	=	$this->language->get('error_permission');
            }
          }
          else {
            if	(!$this->model_extension_blog_module_blog_module_author->hasPermission('edit_reviews'))	{
              $this->error['warning']	=	$this->language->get('error_permission');
            }
          }
        }

        if (!$this->request->post['post_id']) {
            $this->error['post'] = $this->language->get('error_post');
        }
        $this->load->model('extension/dv_opencart_patch/helper/general');
        if (($this->model_extension_dv_opencart_patch_helper_general->strlen($this->request->post['author']) < 3) || ($this->model_extension_dv_opencart_patch_helper_general->strlen($this->request->post['author']) > 64)) {
            $this->error['author'] = $this->language->get('error_author');
        }

        if ($this->model_extension_dv_opencart_patch_helper_general->strlen($this->request->post['description']) < 1) {
            $this->error['text'] = $this->language->get('error_text');
        }

        if (!isset($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
            $this->error['rating'] = $this->language->get('error_rating');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'extension/blog_module/blog_module/review')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->model('extension/blog_module/blog_module/author');
        $this->load->model('extension/blog_module/blog_module/post');

        $this->load->model('extension/blog_module/blog_module/author');

        $current_author = $this->model_extension_blog_module_blog_module_author->getAuthorByUserId($this->user->getId());
        foreach ($this->request->post['selected'] as  $review_id) {
          $review_info = $this->model_extension_blog_module_blog_module_review->getReview($review_id);
          $post_author =  $this->model_extension_blog_module_blog_module_post->getAuthorByPost($review_info['post_id']);
          if($post_author['author_id'] != $current_author['author_id'])
          {
            if	(!$this->model_extension_blog_module_blog_module_author->hasPermission('delete_others_reviews'))	{
              $this->error['warning']	=	$this->language->get('error_permission');
            }
          }
          else {
            if	(!$this->model_extension_blog_module_blog_module_author->hasPermission('delete_reviews'))	{
              $this->error['warning']	=	$this->language->get('error_permission');
            }
          }
        }

        return !$this->error;
    }

}
