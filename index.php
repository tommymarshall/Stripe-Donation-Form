<?php

// Load Stripe
require('lib/Stripe.php');

// Load configuration settings
$config = require('config.php');

// Force https
if ($config['test-mode'] && $_SERVER['HTTPS'] != 'on') {
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: https://' . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
    exit;
}

if ($_POST) {
    Stripe::setApiKey($config['secret-key']);

    // POSTed Variables
    $token      = $_POST['stripeToken'];
    $first_name = $_POST['first-name'];
    $last_name  = $_POST['last-name'];
    $name       = $first_name . ' ' . $last_name;
    $address    = $_POST['address'] . "\n" . $_POST['city'] . ', ' . $_POST['state'] . ' ' . $_POST['zip']. ' ' .$_POST['country'];
    $email      = $_POST['email'];
    $phone      = $_POST['phone'];
    $amount     = (float) $_POST['amount'];

    try {
        if ( ! isset($_POST['stripeToken']) ) {
            throw new Exception("The Stripe Token was not generated correctly");
        }

        // Charge the card
        $donation = Stripe_Charge::create(array(
            'card'        => $token,
            'description' => 'Donation by ' . $name . ' (' . $email . ')',
            'amount'      => $amount * 100,
            'currency'    => $_POST['currency']
        ));

        // Build and send the email
        $headers = 'From: ' . $config['emaily-from'];
        $headers .= "\r\nBcc: " . $config['emaily-bcc'] . "\r\n\r\n";

        // Find and replace values
        $find    = array('%name%', '%amount%');
        $replace = array($name, '$' . $amount);

        $message = str_replace($find, $replace , $config['email-message']) . "\n\n";
        $message .= 'Amount: $' . $amount . "\n";
        $message .= 'Address: ' . $address . "\n";
        $message .= 'Phone: ' . $phone . "\n";
        $message .= 'Email: ' . $email . "\n";
        $message .= 'Date: ' . date('M j, Y, g:ia', $donation['created']) . "\n";
        $message .= 'Transaction ID: ' . $donation['id'] . "\n\n\n";

        $subject = $config['email-subject'];

        // Send it
        if ( !$config['test-mode'] ) {
            mail($email,$subject,$message,$headers);
        }

        // Forward to "Thank You" page
        header('Location: ' . $config['thank-you']);
        exit;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stripe Donation Form</title>
    <link rel="stylesheet" type="text/css" href="style.css" media="all">
    <script type="text/javascript" src="https://js.stripe.com/v2"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript">
        Stripe.setPublishableKey('<?php echo $config['publishable-key'] ?>');
    </script>
    <script type="text/javascript" src="script.js"></script>
</head>
<body>
    <div class="wrapper">
        <h1>
            Stripe Donation Form
        </h1>
        <p>
            <strong>This form has been pre-populated with test Credit Card data. No
            live transactions are taking place.</strong>
            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
            consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
            cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
            proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
        </p>

        <div class="messages">
            <!-- Error messages go here go here -->
        </div>

        <form action="#" method="POST" class="donation-form">
            <fieldset>
                <legend>
                    Contact Information
                </legend>
                <div class="form-row form-first-name">
                    <label>First Name</label>
                    <input type="text" name="first-name" class="first-name text">
                </div>
                <div class="form-row form-last-name">
                    <label>Last Name</label>
                    <input type="text" name="last-name" class="last-name text">
                </div>
                <div class="form-row form-email">
                    <label>Email</label>
                    <input type="text" name="email" class="email text">
                </div>
                <div class="form-row form-phone">
                    <label>Phone</label>
                    <input type="text" name="phone" class="phone text">
                </div>
                <div class="form-row form-address">
                    <label>Address</label>
                    <textarea name="address" cols="30" rows="2" class="address text"></textarea>
                </div>
                <div class="form-row form-city">
                    <label>City</label>
                    <input type="text" name="city" class="city text">
                </div>
                <div class="form-row form-state">
                    <label>State</label>
                    <input type=”text” name=“state" class="state text">
                </div>
                <div class="form-row form-zip">
                    <label>Zip</label>
                    <input type="text" name="zip" class="zip text">
                </div>
                <div class="form-row form-country">
                    <label>Country</label>
                    <input type="text" name"country" class="country text">
                </div>
            </fieldset>

            <fieldset>
                <legend>
                    Your Generous Donation
                </legend>
                <div class="form-row form-currency">
                    <label>Currency and Amount</label>
                    <select name="currency" class="state text">
                        <option value="USD">US Dollars</option>
                        <option value="GBP">Great British Pounds</option>
                        <option value="CAD">Canadian Dollars</option>
                        <option value="EUR">Euros</option>
                        <option value="INR">Indian Rupee</option>
                        <option value="RUB">Russian Ruble</option>
                    </select>
                </div>
                <div class="form-row form-amount">
                    <input type="text" name="amount" class="amount text">
                </div>
                <div class="form-row form-number">
                    <label>Card Number</label>
                    <input type="text" autocomplete="off" class="card-number text" value="4242424242424242">
                </div>
                <div class="form-row form-cvc">
                    <label>CVC</label>
                    <input type="text" autocomplete="off" class="card-cvc text" value="123">
                </div>
                <div class="form-row form-expiry">
                    <label>Expiration Date</label>
                    <select class="card-expiry-month text">
                        <option value="01" selected>Jan (01)</option>
                        <option value="02">Feb (02)</option>
                        <option value="03">Mar (03)</option>
                        <option value="04">Apr (04)</option>
                        <option value="05">May (05)</option>
                        <option value="06">June (06)</option>
                        <option value="07">July (07)</option>
                        <option value="08">Aug (08)</option>
                        <option value="09">Sep (09)</option>
                        <option value="10">Oct (10)</option>
                        <option value="11">Nov (11)</option>
                        <option value="12">Dec (12)</option>
                    </select>
                    <select class="card-expiry-year text">
                        <option value="2015">2015</option>
                        <option value="2016">2016</option>
                        <option value="2017">2017</option>
                        <option value="2018">2018</option>
                        <option value="2019">2019</option>
                        <option value="2020">2020</option>
                        <option value="2021">2021</option>
                        <option value="2022">2022</option>
                        <option value="2023">2023</option>
                    </select>
                </div>
                <div class="form-row form-submit">
                    <input type="submit" class="submit-button" value="Submit Donation">
                </div>
            </fieldset>
        </form>
    </div>

    <script>if (window.Stripe) $('.donation-form').show()</script>
    <noscript><p>JavaScript is required for the donation form.</p></noscript>

</body>
</html>
