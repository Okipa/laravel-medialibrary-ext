<?php

return [

    'constraints' => [
        'dimensions' => [
            'width' => [
                'min' => 'Largeur min. : :width pixels.',
            ],
            'height' => [
                'min' => 'Hauteur min. : :height pixels.',
            ],
        ],
        'size' => [
            'max' => 'Taille fichier max. : :size Mo.',
        ],
        'types' => '{1}Type accepté : :types.|[2,*]Types acceptés : :types.',
    ],

];
