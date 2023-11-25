
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Upload Limit
    |--------------------------------------------------------------------------
    |
    | The maximum upload storage space amount that non-admin users
    | can use per account.
    |
    */

    'limit' => intval(env('UPLOAD_LIMIT', 50 * 1000 * 1000)), // 50 MB

    /*
    |--------------------------------------------------------------------------
    | Upload Size Validation Message
    |--------------------------------------------------------------------------
    |
    | The validation message presented when a user tries to upload a file
    | and they have exceeded the storage limit.
    |
    */

    'limit_exceeded_message' => 'You have exceeded your available upload storage space. To make more '.
        'room, please delete some images from your library, or remove '.
        'thumbnails from your presentations.',
];
