<?php
$_['name'] = 'Blog Post';
//Статус Frontend редатора
$_['frontend_status'] = '1';
//GET параметр route в админке
$_['backend_route'] = 'extension/blog_module/blog_module/post/edit';
//REGEX для GET параметров route в админке
$_['backend_route_regex'] = 'extension/blog_module/blog_module/post/*';
//GET параметр route на Frontend
$_['frontend_route'] = 'extension/blog_module/blog_module/post';
//GET параметр содержащий id страницы в админке
$_['backend_param'] = 'post_id';
//GET параметр содержащий id страницы на Frontend
$_['frontend_param'] = 'post_id';

//Путь для сохранения описания на Frontend
$_['edit_route'] = 'extension/blog_module/blog_module/post/savePost';
//edited
//События необходимые для работы данного route
$_['events'] = [
    'admin/view/extension/blog_module/blog_module/post_form/after'       => 'extension/blog_module/event/blog_module|view_post_after',
    'admin/model/extension/blog_module/blog_module/post/addPost/after'   => 'extension/blog_module/event/blog_module|model_catalog_product_addPost_after',
    'admin/model/extension/blog_module/blog_module/post/addPost/before'  => 'extension/blog_module/event/blog_module|model_catalog_product_addPost_before',
    'admin/model/extension/blog_module/blog_module/post/editPost/after'  => 'extension/blog_module/event/blog_module|model_catalog_product_editPost_after',
    'admin/model/extension/blog_module/blog_module/post/editPost/before' => 'extension/blog_module/event/blog_module|model_catalog_product_editPost_before',

    'catalog/view/extension/blog_module/blog_module/post/before'         => 'extension/blog_module/event/blog_module|view_post_before'
];
