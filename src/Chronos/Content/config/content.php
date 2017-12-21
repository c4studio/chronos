<?php

return [

    'upload_paths' => [
        [
            // E.g.: http://chronos.ro/uploads/media/{year}/{month}
            'asset_path' => env('APP_URL') . '/uploads/media/' . date('Y') . '/' . date('m'),
            // E.g.: /var/www/public/uploads/media/{year}/{month}
            'upload_path' => env('UPLOAD_URL') . '/uploads/media/' . date('Y') . '/' . date('m')
        ]
    ]

];