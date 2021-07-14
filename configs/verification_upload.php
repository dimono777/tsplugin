<?php
return [
    'img-validation' => [
        'name'     => [
            'extension' => [
                'jpg',
                'jpeg',
                'png',
                'gif',
                'pdf',
                'doc',
                'docx',
                'odt',
                'bmp',
                'tiff'
            ],
        ],
        'type'     => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/bmp',
            'image/tiff',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.oasis.opendocument.text',
        ],
        'tmp_name' => true,
        'size'     => [
            'min' => 0,
            'max' => 26214400,
        ],
    ],
    'sourceKey'      => 'website',
    'messages'       => [
        'system'               => \TS_Functions::__('Some error. Please, try again later.'),
        'no-name'              => \TS_Functions::__('Please, choose file for upload.'),
        'no-type'              => \TS_Functions::__('You can upload image only. Please, check type of chosen file.'),
        'no-size'              => \TS_Functions::__('You can\'t upload an empty file.'),
        'wrong-name-extension' => \TS_Functions::__(
            'Wrong file extensions. '
            . 'Available extensions: '
            . 'jpg, '
            . 'jpeg, '
            . 'png, '
            . 'gif, '
            . 'pdf, '
            . 'doc, '
            . 'docx, '
            . 'odt.'
        ),
        'wrong-type'           => \TS_Functions::__(
            'You can upload image only. '
            . 'Please, check type of chosen file. '
            . 'Available types: '
            . 'image/jpeg, image/png, '
            . 'image/gif, '
            . 'application/pdf, '
            . 'application/msword, '
            . 'application/vnd.openxmlformats-officedocument.wordprocessingml.document, '
            . 'application/vnd.oasis.opendocument.text.'
        ),
        'wrong-size-min'       => \TS_Functions::__('You can\'t upload an empty file.'),
        'wrong-size-max'       => \TS_Functions::__('Maximum file size is 1Mb.'),
    ],
];