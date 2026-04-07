<?php

return [
    /*
     * URL base de Apache Guacamole (sin trailing slash).
     * Ej: http://40.0.0.126:8080/guacamole
     */
    'url'      => env('GUACAMOLE_URL', 'http://localhost:8080/guacamole'),
    'username' => env('GUACAMOLE_USERNAME', 'guacadmin'),
    'password' => env('GUACAMOLE_PASSWORD', 'guacadmin'),
];
