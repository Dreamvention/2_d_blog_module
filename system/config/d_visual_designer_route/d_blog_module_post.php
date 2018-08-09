<?php
$_['name'] = 'Blog Post';
//Статус Frontend редатора
$_['frontend_status'] = '1';
//GET параметр route в админке
$_['backend_route'] = 'extension/d_blog_module/post/edit';
//REGEX для GET параметров route в админке
$_['backend_route_regex'] = 'extension/d_blog_module/post/*';
//GET параметр route на Frontend
$_['frontend_route'] = 'extension/d_blog_module/post';
//GET параметр содержащий id страницы в админке
$_['backend_param'] = 'post_id';
//GET параметр содержащий id страницы на Frontend
$_['frontend_param'] = 'post_id';

//Путь для сохранения описания на Frontend
$_['edit_route'] = 'extension/d_blog_module/post/savePost';
//edited
//События необходимые для работы данного route
$_['events'] = array(
    'admin/view/extension/d_blog_module/post_form/after'       => 'extension/event/d_blog_module/view_post_after',
    'admin/model/extension/d_blog_module/post/addPost/after'   => 'extension/event/d_blog_module/model_catalog_product_addPost_after',
    'admin/model/extension/d_blog_module/post/addPost/before'  => 'extension/event/d_blog_module/model_catalog_product_addPost_before',
    'admin/model/extension/d_blog_module/post/editPost/after'  => 'extension/event/d_blog_module/model_catalog_product_editPost_after',
    'admin/model/extension/d_blog_module/post/editPost/before' => 'extension/event/d_blog_module/model_catalog_product_editPost_before',

    'catalog/view/extension/d_blog_module/post/before'         => 'extension/event/d_blog_module/view_post_before'
);