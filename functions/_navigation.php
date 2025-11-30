<?php

function navigation_array($selected = false)
{

    $navigation = [
        [
            'title' => 'Stores',
            'sections' => [
                [
                    'title' => 'Stores',
                    'id' => 'admin-content',
                    'pages' => [
                        [
                            'icon' => 'stores',
                            'url' => '/admin/dashboard',
                            'title' => 'Stores',
                            'sub-pages' => [
                                [
                                    'title' => 'Dashboard',
                                    'url' => '/admin/dashboard',
                                    'colour' => 'red',
                                ],[
                                    'title' => 'Import Stores',
                                    'url' => '/admin/import/stores',
                                    'colour' => 'red',
                                ],[
                                    'title' => 'Import Countries',
                                    'url' => '/admin/import/countries',
                                    'colour' => 'red',
                                ],[
                                    'br' => '---',
                                ],[
                                    'title' => 'Visit Stores App',
                                    'url' => 'https://stores.brickmmo.com',
                                    'colour' => 'orange',
                                    'icon' => 'fa-solid fa-arrow-up-right-from-square',
                                ],[
                                    'br' => '---',
                                ],[
                                    'title' => 'Uptime Report',
                                    'url' => 'https://uptime.brickmmo.com/details/22',
                                    'colour' => 'orange',
                                    'icons' => 'bm-uptime',
                                ],[
                                    'title' => 'Stats Report',
                                    'url' => '/stas/colours',
                                    'colour' => 'orange',
                                    'icons' => 'bm-stats',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    if($selected)
    {
        
        $selected = '/'.$selected;
        $selected = str_replace('//', '/', $selected);
        $selected = str_replace('.php', '', $selected);
        $selected = str_replace('.', '/', $selected);
        $selected = substr($selected, 0, strpos($selected, '/'));

        foreach($navigation as $levels)
        {

            foreach($levels['sections'] as $section)
            {

                foreach($section['pages'] as $page)
                {

                    if(strpos($page['url'], $selected) === 0)
                    {
                        return $page;
                    }

                }

            }

        }

    }

    return $navigation;

}