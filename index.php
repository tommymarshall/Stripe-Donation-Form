<?php

// Load Stripe library
require 'lib/Stripe.php';

// Load configuration settings
require 'config.php';

// Force https
if( $_SERVER["HTTPS"] != "on" && !$config['test-mode'] ) {
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
	exit();
}

if ($_POST) {
	Stripe::setApiKey($config['secret-key']);

	// POSTed Variables
	$token = $_POST['stripeToken'];
	$first_name = $_POST['first-name'];
	$last_name 	= $_POST['last-name'];
	$name 			= $first_name . ' ' . $last_name;
	$address = $_POST['address']."\n" . $_POST['city'] . ', ' . $_POST['state'] . ' ' . $_POST['zip'];
	$email   = $_POST['email'];
	$phone   = $_POST['phone'];
	$amount  = (float) $_POST['amount'];

	try {
		if ( ! isset($_POST['stripeToken']) ) {
			throw new Exception("The Stripe Token was not generated correctly");
		}

		// Charge the card
		$donation = Stripe_Charge::create(array(
			'card' => $token,
			'description' => 'Donation by ' . $name . ' (' . $email . ')',
			'amount' => $amount * 100,
			'currency' => 'usd')
		);

		// Build and send the email
		$headers = "From: " . $config['emaily-from'];
		$headers .= "\r\nBcc: " . $config['emaily-bcc'] . "\r\n\r\n";

		// Find and replace values
		$find = array('%name%', '%amount%');
		$replace = array($name, '$' . $amount);

		$message = str_replace($find, $replace , $config['email-message']) . "\n\n";
		$message .= "Amount: $" . $amount . "\n";
		$message .= "Address: " . $address . "\n";
		$message .= "Phone: " . $phone . "\n";
		$message .= "Email: " . $email . "\n";
		$message .= "Date: " . date('M j, Y, g:ia', $donation['created']) . "\n";
		$message .= "Transaction ID: " . $donation['id'] . "\n\n\n";

		$subject = $config['email-subject'];

		// Send it
		if ( !$config['test-mode'] ) {
			mail($email,$subject,$message,$headers);
		}

		// Forward to "Thank You" page
		header('Location: ' . $config['thank-you']);
		exit;

	}
	catch (Exception $e) {
		$error = $e->getMessage();
	}
}

?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html lang="en-us" class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>    <html lang="en-us" class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>    <html lang="en-us" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en-us" class="no-js"> <!--<![endif]-->
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8">
		<title>Stripe Donation Form</title>
		<link rel="stylesheet" type="text/css" href="style.css" media="all">
		<script type="text/javascript" src="https://js.stripe.com/v1/"></script>
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
						<select name="state" class="state text">
							<option value="AL">AL</option>
							<option value="AK">AK</option>
							<option value="AZ">AZ</option>
							<option value="AR">AR</option>
							<option value="CA">CA</option>
							<option value="CO">CO</option>
							<option value="CT">CT</option>
							<option value="DE">DE</option>
							<option value="DC">DC</option>
							<option value="FL">FL</option>
							<option value="GA">GA</option>
							<option value="HI">HI</option>
							<option value="ID">ID</option>
							<option value="IL">IL</option>
							<option value="IN">IN</option>
							<option value="IA">IA</option>
							<option value="KS">KS</option>
							<option value="KY">KY</option>
							<option value="LA">LA</option>
							<option value="ME">ME</option>
							<option value="MD">MD</option>
							<option value="MA">MA</option>
							<option value="MI">MI</option>
							<option value="MN">MN</option>
							<option value="MS">MS</option>
							<option value="MO">MO</option>
							<option value="MT">MT</option>
							<option value="NE">NE</option>
							<option value="NV">NV</option>
							<option value="NH">NH</option>
							<option value="NJ">NJ</option>
							<option value="NM">NM</option>
							<option value="NY">NY</option>
							<option value="NC">NC</option>
							<option value="ND">ND</option>
							<option value="OH">OH</option>
							<option value="OK">OK</option>
							<option value="OR">OR</option>
							<option value="PA">PA</option>
							<option value="RI">RI</option>
							<option value="SC">SC</option>
							<option value="SD">SD</option>
							<option value="TN">TN</option>
							<option value="TX">TX</option>
							<option value="UT">UT</option>
							<option value="VT">VT</option>
							<option value="VA">VA</option>
							<option value="WA">WA</option>
							<option value="WV">WV</option>
							<option value="WI">WI</option>
							<option value="WY">WY</option>
						</select>
					</div>
					<div class="form-row form-zip">
						<label>Zip</label>
						<input type="text" name="zip" class="zip text">
					</div>
				</fieldset>

				<fieldset>
					<legend>
						Your Generous Donation
					</legend>
					<div class="form-row form-amount">
						<label><input type="radio" name="amount" class="set-amount" value="25"> $25</label>
						<label><input type="radio" name="amount" class="set-amount" value="500"> $500</label>
						<label><input type="radio" name="amount" class="set-amount" value="2500"> $2,500</label>
						<label><input type="radio" name="amount" class="set-amount" value="100"> $100</label>
						<label><input type="radio" name="amount" class="set-amount" value="1000"> $1,000</label>
						<label><input type="radio" name="amount" class="set-amount" value="5000"> $5,000</label>
						<label><input type="radio" name="amount" class="other-amount" value="0"> Other:</label> <input type="text" class="amount text" disabled>
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
							<option value="01">January</option>
							<option value="02">February</option>
							<option value="03">March</option>
							<option value="04">April</option>
							<option value="05">May</option>
							<option value="06">June</option>
							<option value="07">July</option>
							<option value="08">August</option>
							<option value="09">September</option>
							<option value="10">October</option>
							<option value="11">November</option>
							<option value="12">December</option>
						</select>
						<select class="card-expiry-year text">
							<option value="2012">2012</option>
							<option value="2013">2013</option>
							<option value="2014" selected>2014</option>
							<option value="2015">2015</option>
							<option value="2016">2016</option>
							<option value="2017">2017</option>
							<option value="2018">2018</option>
							<option value="2019">2019</option>
							<option value="2020">2020</option>
						</select>
					</div>
					<div class="form-row form-submit">
						<input type="submit" class="submit-button" value="Submit Donation">
					</div>
				</fieldset>
			</form>

      <script>if (window.Stripe) $(".donation-form").show()</script>
      <noscript><p>JavaScript is required for the donation form.</p></noscript>
		</div>

	</body>
</html>
