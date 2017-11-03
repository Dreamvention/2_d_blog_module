<?php
$_['name']            = 'Blog Post';
//Статус Frontend редатора
$_['frontend_status'] = '1';
//GET параметр route в админке 
$_['backend_route']   = 'd_blog_module/post/edit';

$_['backend_route_regex'] = 'd_blog_module/post/*';
//GET параметр route на Frontend
$_['frontend_route']  = 'd_blog_module/post';
//GET параметр содержащий id страницы в админке
$_['backend_param']   = 'post_id';
//GET параметр содержащий id страницы на Frontend
$_['frontend_param']  = 'post_id';
//Путь для сохранения описания на Frontend
$_['edit_url']        = 'index.php?route=d_blog_module/post/editPost';

$_['events']          = array(
    'admin/view/extension/d_blog_module/post_form/after' => 'extension/event/d_blog_module/view_post_after',
    'catalog/view/extension/d_blog_module/post/before' => 'extension/event/d_blog_module/view_post_before',
    'catalog/model/extension/d_blog_module/post/getPost/after' => 'extension/event/d_blog_module/model_post_getPost_after'
);