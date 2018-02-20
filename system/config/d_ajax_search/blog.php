<?php
$_['d_ajax_search_blog'] = array(
    'table' => array(
        'name' => 'bd',
        'full_name' => 'bm_post_description',
        'key' => 'post_id'
    ),
    'tables' => array(
        array(
            'name' => 'b',
            'join_to' => 'bd',
            'full_name' => 'bm_post',
            'key' => 'post_id',
            'join' => 'LEFT JOIN',
            'multi_language' => 1
            )
    ),
    'query' => array(
        'Title' => array(
            'key' => 'bd.title',
            'rule' => 'LIKE'),

        'Short Description' => array(
            'key' => 'bd.short_description',
            'rule' => 'LIKE'),

        'Description' => array(
            'key' => 'bd.description',
            'rule' => 'LIKE'),

        'Tag' => array(
            'key' => 'bd.tag',
            'rule' => 'LIKE'),

        'Meta Description' => array(
            'key' => 'bd.meta_description',
            'rule' => 'LIKE'),

        'Meta Title' => array(
            'key' => 'bd.meta_title',
            'rule' => 'LIKE'),

        'Meta Keyword' => array(
            'key' => 'bd.meta_keyword',
            'rule' => 'LIKE'),

        ),

    'select' => array(
        'image' => 'b.image',
        'name' => 'bd.title',
        // 'description' => 'pd.description',
        // 'price' => 'p.price'
    )
);