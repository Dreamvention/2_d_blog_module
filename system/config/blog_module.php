<?php
$_['blog_module_debug'] = 0;
$_['blog_module_setting'] = array(

    'config' => 'blog_module',
    'utc_datetime_format' => 'Y-m-d\TH:i:sP',
    'dir_admin' => 'admin',
    'theme' => 'default',
    'admin_style' => 'light',
    'category' => array(
        'main_category_id' => 1,
        'layout' => array(0 => 2),
        'layout_type' => 'grid',
        'post_page_limit' => 7,
        'image_display' => 1,
        'image_width' => 1200,
        'image_height' => 750,
        'sub_category_display' => 1,
        'sub_category_col' => 6,
        'sub_category_image' => 1,
        'sub_category_post_count' => 1,
        'sub_category_image_width' => 120,
        'sub_category_image_height' => 75,
        'limited_post_display' => 1,
        'main_post_display' => 1,
    ),
    'post_thumb' => array(
        'image_width' => 1200,
        'image_height' => 750,
        'title_length' => 100,
        'short_description_length' => 300,
        'description_length' => 300,
        'category_label_display' => 1,
        'author_display' => 1,
        'date_display' => 1,
        'date_format' => array(1=>'l d F, Y'),
        'date_format_day' => 'd',
        'date_format_month' => 'F',
        'date_format_year' => 'Y',
        'rating_display' => 1,
        'description_display' => 1,
        'tag_display' => 1,
        'views_display' => 1,
        'review_display' => 1,
        'read_more_display' => 1,
        'animate' => 'fadeIn'
    ),
    'post' => array(
        'image_display' => 1,
        'image_width' => 1200,
        'image_height' => 750,
        'popup_display' => 1,
        'popup_width' => 1400,
        'popup_height' => 875,
        'author_display' => 1,
        'date_display' => 1,
        'date_format' => array(1=>'l d F, Y'),
        'review_display' => 1,
        'rating_display' => 1,
        'category_label_display' => 1,
        'short_description_length' => 150,
        'style_short_description_display' => 0,
        'nav_display' => 1,
        'nav_same_category' => 0,

        'limit_access_user' => 0,
        'limit_access_user_group' => 0,
        'access_user' => array(),
        'access_user_group' => array()

    ),
    'review' => array(
        'guest' => 1,
        'social_login' => 1,
        'page_limit' => 5,
        'image_limit' => 5,
        'rating_display' => 1,
        'customer_display' => 1,
        'image_user_display' => 1,
        'moderate' => 0,
        'image_upload_width' => 500,
        'image_upload_height' => 500,
    ),
    'review_thumb' => array(
        'image_width' => 70,
        'image_height' => 70,
        'no_image' => 'catalog/d_blog_module/no_profile_image.png',
        'date_display' => 1,
        'image_display' => 1,
        'rating_display' => 1,
        'image_user_display' => 1,
        // 'layout' => array(0 => 1),
        'image_user_width' => 70,
        'image_user_height' => 70,
    ),
    'author' => array(
        'layout' => array(0 => 2),
        'layout_type' => 'grid',
        'post_page_limit' => 7,
        'image_width' => 400,
        'image_height' => 400,
        'category_display' => 1,
        'category_col' => 6,
        'category_image' => 1,
        'category_post_count' => 1,
        'category_image_width' => 120,
        'category_image_height' => 75,
    ),
    'design' => array(
        'custom_style' => '',
        'ssl_url' => ''
    )
    //'support' => 1,
);

$_['blog_module_animations'] = array(
    '',
    'slideInUp',
    'pulse',
    'tada',
    'jello',
    'fadeIn',
    'fadeInUp',
    'bounce',
    'bounceIn',
    'zoomIn',
    'zoomInDown',
    'zoomInUp'
);
$_['blog_module_events'] = [
    0 => [
        'code' => 'blog_module',
        'description' => '',
        'trigger' => 'admin/view/common/column_left/before',
        'action' => 'extension/blog_module/event/blog_module|view_common_column_left_before',
        'status' => 1,
        'sort_order' => 0
    ],
    1 => [
        'code' => 'blog_module',
        'description' => '',
        'trigger' => 'admin/view/setting/setting/before',
        'action' => 'extension/blog_module/event/blog_module|view_setting_setting_captcha_before',
        'status' => 1,
        'sort_order' => 0
    ],
    2 => [
        'code' => 'blog_module',
        'description' => '',
        'trigger' => 'catalog/view/common/header/before',
        'action' => 'extension/blog_module/event/blog_module|view_common_header_before',
        'status' => 1,
        'sort_order' => 0
    ],
    3 => [
        'code' => 'blog_module',
        'description' => '',
        'trigger' => 'catalog/view/common/menu/before',
        'action' => 'extension/blog_module/event/blog_module|view_common_menu_before',
        'status' => 1,
        'sort_order' => 0
    ],
    4 => [
        'code' => 'blog_module',
        'description' => '',
        'trigger' => 'catalog/model/extension/visual_designer/module/visual_designer/getOptions/after',
        'action' => 'extension/blog_module/event/blog_module|controller_after_visual_designer_menu',
        'status' => 1,
        'sort_order' => 0
    ],
    5 => [
        'code' => 'blog_module',
        'description' => '',
        'trigger' => 'admin/model/localisation/language/addLanguage/after',
        'action' => 'extension/blog_module/event/blog_module|model_localisation_language_addLanguage_after',
        'status' => 1,
        'sort_order' => 0
    ],
    6 => [
        'code' => 'blog_module',
        'description' => '',
        'trigger' => 'admin/model/localisation/language/deleteLanguage/after',
        'action' => 'extension/blog_module/event/blog_module|model_localisation_language_deleteLanguage_after',
        'status' => 1,
        'sort_order' => 0
    ],
    7 => [
        'code' => 'blog_module',
        'description' => '',
        'trigger' => 'catalog/model/design/layout/getLayout/after',
        'action' => 'extension/blog_module/event/blog_module|model_design_layout_getLayout_after',
        'status' => 1,
        'sort_order' => 0
    ],
    8 => [
        'code' => 'blog_module',
        'description' => '',
        'trigger' => 'admin/model/user/user/addUser/after',
        'action' => 'extension/blog_module/event/blog_module|model_user_user_addUser_after',
        'status' => 1,
        'sort_order' => 0
    ],
    9 => [
        'code' => 'blog_module',
        'description' => '',
        'trigger' => 'admin/model/user/user/deleteUser/after',
        'action' => 'extension/blog_module/event/blog_module|model_user_user_deleteUser_after',
        'status' => 1,
        'sort_order' => 0
    ]
];
