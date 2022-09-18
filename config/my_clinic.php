<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Delete old access and refresh tokens for a specified device when a new
    | login is performed
    |--------------------------------------------------------------------------
    |
    */

    "delete_device_previous_auth_tokens_on_login" => true,

    /*
     | Number of users added to the database after a normal database seeding
     */
    "seeded_users_count" => 3,
    "seeded_staff_users_count" => 3,
    "seeded_staff_emails_count" => 3,
];
