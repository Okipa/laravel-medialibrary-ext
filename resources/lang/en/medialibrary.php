<?php

return [

    'constraints' => [
        'dimensions' => [
            'width' => [
                'min' => 'Min. width: :width px.',
            ],
            'height' => [
                'min' => 'Min. height: :height px.',
            ],
        ],
        'size' => [
            'max' => 'Max. file size: :size Mb.',
        ],
        'types' => '{1}Accepted type: :types.|[2,*]Accepted types: :types.',
    ],

];
