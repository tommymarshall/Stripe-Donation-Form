<?php

return array(

    // Enable test mode (not require HTTPS)
    'test-mode'       => true,

    // Secret Key from Stripe.com Dashboard
    'secret-key'      => 'YOUR_SECRET_KEY',

    // Publishable Key from Stripe.com Dashboard
    'publishable-key' => 'YOUR_PUBLISHABLE_KEY',

    // Where to send upon successful donation (must include http://)
    'thank-you'       => 'http://domain.com/thank-you.html',

    // Who the email will be from.
    'email-from'      => 'no-reply@domain.com',

    // Who should be BCC'd on this email. Probably an administrative email.
    'email-bcc'       => 'admin@domain.com',

    // Subject of email receipt
    'email-subject'   => 'Thank you for your donation!',

    // Email message. %name% is the donor's name. %amount% is the donation amount
    'email-message'   => "Dear %name%,\n\nThank you for your donation of %amount%. We rely on the financial support from people like you to keep our cause alive. Below is your donation receipt to keep for your records."

);
