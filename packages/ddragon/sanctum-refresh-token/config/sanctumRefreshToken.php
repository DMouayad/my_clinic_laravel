<?php

namespace DDragon\SanctumRefreshToken;

return [
    /*
   |--------------------------------------------------------------------------
   | Expiration Minutes
   |--------------------------------------------------------------------------
   |
   | This value controls the number of minutes until an issued refresh token will be
   | considered expired. If this value is null, refresh tokens do
   | not expire. This won't tweak the lifetime of first-party sessions.
   |
   */

    'expiration' => 10080,

];
