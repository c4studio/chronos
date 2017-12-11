<?php

return [

    'system_content_types' => ['Gallery'],

    'upload_paths' => [
        [
            // E.g.: http://chronos.ro/uploads/media/{year}/{month}
            'asset_path' => asset('uploads/media/' . date('Y') . '/' . date('m')),
            // E.g.: /home/public/uploads/media/{year}/{month}
            'upload_path' => public_path('uploads/media/' . date('Y') . '/' . date('m'))
        ]
    ]

];