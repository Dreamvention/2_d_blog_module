<?php
$_['name'] = 'Blog category';
//Статус Frontend редатора
$_['frontend_status'] = '1';
//GET параметр route в админке
$_['backend_route'] = 'extension/d_blog_module/category/edit';
//REGEX для GET параметров route в админке
$_['backend_route_regex'] = 'extension/d_blog_module/category/*';
//GET параметр route на Frontend
$_['frontend_route'] = 'extension/d_blog_module/category';
//GET параметр содержащий id страницы в админке
$_['backend_param'] = 'category_id';
//GET параметр содержащий id страницы на Frontend
$_['frontend_param'] = 'category_id';

//Путь для сохранения описания на Frontend
$_['edit_route'] = 'extension/d_blog_module/category/saveCategory';
//edited
//События необходимые для работы данного route
$_['events'] = array(
    'admin/view/extension/d_blog_module/category_form/after'           => 'extension/event/d_blog_module/view_category_after',
    'admin/model/extension/d_blog_module/category/addCategory/after'   => 'extension/event/d_blog_module/model_catalog_product_addCategory_after',
    'admin/model/extension/d_blog_module/category/addCategory/before'  => 'extension/event/d_blog_module/model_catalog_product_addCategory_before',
    'admin/model/extension/d_blog_module/category/editCategory/after'  => 'extension/event/d_blog_module/model_catalog_product_editCategory_after',
    'admin/model/extension/d_blog_module/category/editCategory/before' => 'extension/event/d_blog_module/model_catalog_product_editCategory_before',

    'catalog/view/extension/d_blog_module/category/before' => 'extension/event/d_blog_module/view_category_before'
);