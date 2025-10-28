<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Styles
    |--------------------------------------------------------------------------
    |
    | List of styles.
    |
    */

    'styles' => [

      'nobr' => [
        'type' => 'span',
        'name' => 'No Break',
        'ident' => 'B',
        'icon' => null,
        'class' => 'nobr',
        'cp_css' => 'white-space: nowrap',
        'cp_badge' => false,
      ],

      'pill' => [
        'type' => 'span',
        'name' => 'Pill',
        'ident' => 'P',
        'icon' => null,
        'class' => 'pill pill-sm pill-static',
        'cp_css' => 'border-radius: 9999px; border: 1px solid #1C2E36; padding: 2px 6px',
        'cp_badge' => false,
      ],
      
    ],

    /*
    |--------------------------------------------------------------------------
    | Attributes (pro only)
    |--------------------------------------------------------------------------
    |
    | The attributes that can be edited through the attributes panel.
    |
    */

    'attributes' => [

        // 'heading_1' => [
        //     'id' => [
        //         'type' => 'text',
        //         'display' => 'ID',
        //         'default' => null,
        //         'rendered' => true,
        //     ],
        //     'hero' => [
        //         'type' => 'toggle',
        //         'display' => 'Hero',
        //         'default' => null,
        //         'rendered' => 'class',
        //         'values' => [
        //             'true' => 'hero',
        //         ],
        //     ],
        // ],

        // 'ordered_list' => [
        //     'start' => [
        //         'type' => 'text',
        //         'display' => 'Start',
        //         'default' => null,
        //         'rendered' => true,
        //     ],
        //     'reversed' => [
        //         'type' => 'toggle',
        //         'display' => 'Reversed',
        //         'default' => null,
        //         'rendered' => true,
        //     ],
        // ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    |
    | Default styles that will be applied to elements with no style set. It's
    | also possible configure multiple sets of defaults to use with different
    | Bard fields, refer to the docs for more info.
    |
    */

    'defaults' => [

        // 'heading_1' => [
        //     'class' => 'heading-1',
        //     'cp_css' => null,
        //     'cp_badge' => false,
        // ],
        // 'heading_2' => [
        //     'class' => 'heading-2',
        //     'cp_css' => null,
        //     'cp_badge' => false,
        // ],
        // 'paragraph' => [
        //     'class' => 'paragraph',
        //     'cp_css' => null,
        //     'cp_badge' => false,
        // ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    |
    | By default the class names are saved to your content. If you would prefer
    | to save the style keys instead you can change this option to "key".
    |
    */

    'store' => 'class',

];
