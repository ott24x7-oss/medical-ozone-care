<?php
/**
 * Medical Ozone Care — configuration.
 * Edit the values below with your Hostinger MySQL details, then open install.php once.
 *
 * In hPanel: Databases → MySQL Databases → create a database + user, then copy the
 * database name, username and password here. Host is almost always "localhost".
 */
return [
    'db' => [
        // 'mysql' for Hostinger (recommended). 'sqlite' is only for local testing.
        'driver'  => 'mysql',
        'host'    => 'localhost',
        'name'    => 'YOUR_DATABASE_NAME',
        'user'    => 'YOUR_DATABASE_USER',
        'pass'    => 'YOUR_DATABASE_PASSWORD',
        'charset' => 'utf8mb4',
        // used only when driver = sqlite
        'sqlite_path' => __DIR__ . '/data/ozonecare.sqlite',
    ],

    'site' => [
        // Leave empty to auto-detect. Or set e.g. 'https://www.medicalozonecare.co.in'
        'base_url' => '',
    ],

    // install.php uses this to create the first admin. CHANGE THE PASSWORD, then you can
    // log in at /admin/ and (optionally) remove install.php.
    'admin' => [
        'email'    => 'medicalozonecare@gmail.com',
        'password' => 'admin12345',
    ],

    'mail' => [
        // true = email new enquiries using PHP mail() (works on Hostinger shared hosting).
        // false = enquiries are still saved; you just won't get the email.
        'enabled'   => true,
        'notify_to' => 'shekharaiims@gmail.com,medicalozonecare@gmail.com',
        'from'      => 'Medical Ozone Care <medicalozonecare@gmail.com>',
    ],

    'security' => [
        // Used to sign/secure sessions. Put any long random string here.
        'secret' => 'change-this-to-a-long-random-string',
    ],
];
