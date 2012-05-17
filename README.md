## Stripe Donation Form

### Description

This is a simple example showing how to accept donations directly from your website using [Stripe](http://stripe.com). Note: This is meant to help you get started, not to be the final product. You should add server-side validation.

[View Demo](https://stripedonationtest.phpfogapp.com/)  |  [Download Script](https://github.com/tommymarshall/Stripe-Donation-Form/zipball/master)

### Feature Overview

- Process donation using Stripe Payments
- Validate user input
- Forward user to Thank You page
- Email receipt to Administrator and donor

### Requirements

- [SSL Certificate](http://webdesign.about.com/od/ecommerce/a/aa070407.htm) installed on server (Not required for testing purposes)
- PHP 4.3+

### Installation

1. Open config.php in a text or code editor
2. Update configuration (Your public and private keys can be accessed at http://manage.stripe.com/ and click on Your Account, then API Keys.)
3. Copy entire contents of folder to your web server
4. Start collecting donations online in Test Mode(Check out Stripe's [docs online for testing](https://stripe.com/docs/testing))