<?php

return [
    'name'        => 'ProductNews',
    'title'       => 'widget-product-news-title',
    'description' => 'widget-product-news-desc',
    'version'     => '1.0.0',
    'author'      => 'CLSystems',
    'authorEmail' => 'info@clsystems.nl',
    'authorUrl'   => 'https://github.com/CLSystems',
    'updateUrl'   => null,
    'params'      => [
        [
            'name'     => 'categoryIds',
            'type'     => 'CmsModalUcmItem',
            'context'  => 'product-category',
            'multiple' => true,
            'filters'  => ['uint'],
        ],
        [
            'name'     => 'productsNum',
            'type'     => 'Number',
            'label'    => 'limit-products-number',
            'multiple' => true,
            'filters'  => ['uint'],
            'min'      => 1,
            'value'    => 5,
        ],
        [
            'name'    => 'displayLayout',
            'type'    => 'Select',
            'label'   => 'display-layout',
            'value'   => 'FlashNews',
            'options' => [
                'FlashNews'  => 'slider-thumb-nav',
                'SliderNews' => 'sub-slider',
                'BlogList'   => 'blog-list',
                'BlogStack'  => 'blog-stack',
            ],
            'rules'   => ['Options'],
        ],
        [
            'name'    => 'orderBy',
            'type'    => 'Select',
            'label'   => 'order-by',
            'value'   => 'latest',
            'options' => [
                'hits'      => 'order-hits',
                'latest'    => 'order-latest',
                'random'    => 'order-random',
                'titleAsc'  => 'order-title-asc',
                'titleDesc' => 'order-title-desc',
            ],
            'rules'   => ['Options'],
        ],
    ],
];
