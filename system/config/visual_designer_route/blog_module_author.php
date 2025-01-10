<?php
$_['name'] = 'Blog author';
//Статус Frontend редатора
$_['frontend_status'] = '1';
//GET параметр route в админке
$_['backend_route'] = 'extension/blog_module/blog_module/author|edit';
//REGEX для GET параметров route в админке
$_['backend_route_regex'] = 'extension/blog_module/blog_module/author/*';
//GET параметр route на Frontend
$_['frontend_route'] = 'extension/blog_module/blog_module/author';
//GET параметр содержащий id страницы в админке
$_['backend_param'] = 'author_id';
//GET параметр содержащий id страницы на Frontend
$_['frontend_param'] = 'author_id';

//Путь для сохранения описания на Frontend
$_['edit_route'] = 'extension/blog_module/blog_module/author|saveauthor';
//edited
//События необходимые для работы данного route
$_['events'] = [
    'admin/view/extension/blog_module/blog_module/author_form/after'       => 'extension/blog_module/event/blog_module|view_author_after',
    'admin/model/extension/blog_module/blog_module/author/addAuthor/after'   => 'extension/blog_module/event/blog_module|model_catalog_product_addauthor_after',
    'admin/model/extension/blog_module/blog_module/author/addAuthor/before'  => 'extension/blog_module/event/blog_module|model_catalog_product_addauthor_before',
    'admin/model/extension/blog_module/blog_module/author/editAuthor/after'  => 'extension/blog_module/event/blog_module|model_catalog_product_editauthor_after',
    'admin/model/extension/blog_module/blog_module/author/editAuthor/before' => 'extension/blog_module/event/blog_module|model_catalog_product_editauthor_before',

    'catalog/view/extension/blog_module/blog_module/author/before'         => 'extension/blog_module/event/blog_module|view_author_before'
];
