<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Frontend URL
    |--------------------------------------------------------------------------
    |
    | The base URL of the frontend app, used to build links sent in emails
    | (e.g. the password reset link) that point back to the SPA.
    |
    */

    'url' => rtrim(env('FRONTEND_URL', 'http://localhost:5173'), '/'),

];
