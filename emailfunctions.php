<?php

// Only do this on the live server
if($_SERVER['SERVER_NAME'] == 'mythicsilence.com') {

    // Need to set this for the PEAR module to work
    ini_set("include_path", '/home3/mythicsi/php:' . ini_get("include_path") );
    
    require_once 'Mail.php';
    
}

function sendEmail($to, $subject, $body) {
    if($_SERVER['SERVER_NAME'] != 'mythicsilence.com') {
        return;
    }

    $mailer = Mail::factory('smtp', [
        'host' => 'mail.mythicsilence.com',
        'port' => '25',
        'auth' => true,
        'username' => '',
        'password' => '',
    ]);

    $headers = [
        'From' => 'admin@mythicsilence.com',
        //'Reply-To' => 'noreply',
        'To' => $to,
        'Subject' => $subject,
    ];

    $mailResult = $mailer->send($to, $headers, $body);

    if(PEAR::isError($mailResult)) {
        // Uh, do something...
    }
}
?>