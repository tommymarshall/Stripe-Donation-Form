
/* =============================================================================
	Script for Simple Donation Form
	Handles validation and form processing
  ========================================================================== */

$(function() {
	var $form = $('.donation-form');
	var $otheramount = $form.find('.other-amount');
	var $amount = $form.find('.amount');
	var outputError = function(error) {
			$('.messages')
				.html('<p>' + error + '</p>')
				.addClass('active');
			$('.submit-button')
				.removeProp('disabled')
				.val('Submit Donation');
	};
	var stripeResponseHandler = function(status, response) {
		if (response.error) {
			outputError(response.error.message);
		} else {
			var token = response['id'];
			$form.append('<input type="hidden" name="stripeToken" value="' + token + '">');
			$form.get(0).submit();
		}
	};
	var disableinput = function(amount) {
		$amount
			.val(amount)
			.blur()
			.prop('disabled');
	};
	var enableinput = function() {
		$amount
			.removeProp('disabled')
			.focus();
	};

	$('.donation-form').on('submit', function(event) {
		// Disable processing button to prevent multiple submits
		$('.submit-button')
			.prop('disabled', true)
			.val('Processing...');

		// Very simple validation
		if ( $('.first-name').val() === '' ) {
			outputError('First name is required');
			$('.first-name').focus();
			return false;
		}
		if ( $('.last-name').val() === '' ) {
			outputError('Last name is required');
			$('.last-name').focus();
			return false;
		}
		if ( $('.email').val() === '' ) {
			outputError('Email is required');
			$('.email').focus();
			return false;
		}
		if ( $('.phone').val() === '' ) {
			outputError('Phone is required');
			$('.phone').focus();
			return false;
		}
		if ( $('.address').val() === '' ) {
			outputError('Address is required');
			$('.address').focus();
			return false;
		}
		if ( $('.city').val() === '' ) {
			outputError('City is required');
			$('.city').focus();
			return false;
		}
		if ( $('.zip').val() === '' ) {
			outputError('Zip code is required');
			$('.zip').focus();
			return false;
		}
		if ( $('.amount').val() === '' ) {
			outputError('Please make a donation amount');
			$('.other-amount').trigger('click');
			return false;
		}

		// Create Stripe token, check if CC information correct
		Stripe.createToken({
			name: $('.first-name').val() + ' ' + $('.last-name').val(),
			number: $('.card-number').val(),
			cvc: $('.card-cvc').val(),
			exp_month: $('.card-expiry-month').val(),
			exp_year: $('.card-expiry-year').val()
		}, stripeResponseHandler);

		return false;
	});

	$('.form-amount label').on('click', function() {
		var $label = $(this);

		$label.parent().children('label').removeClass('active');
		$label.addClass('active');

		if ( $label.index() === 6 ) {
			enableinput();
		} else {
			disableinput($label.find('.set-amount').val());
		}

	});

	$amount.on('change', function() {
		$otheramount.val($(this).val());
	});

});