<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    // PayFast URL (sandbox for testing, live URL in production)
    protected $payfastUrl;

    public function __construct()
    {
        // Set PayFast URL from env, defaulting to sandbox if not set
        $this->payfastUrl = env('PAYFAST_URL', 'https://sandbox.payfast.co.za/eng/process');
        Log::info('CheckoutController initialized. PayFast URL: ' . $this->payfastUrl);
    }

    /**
     * Display the checkout form
     */
    public function showCheckoutForm()
    {
        Log::info('Displaying checkout form.');
        return view('checkout'); // Return the Blade view for the checkout
    }

    /**
     * Process the checkout
     */
    public function processCheckout(Request $request)
    {
        Log::info('Processing checkout.', ['request_data' => $request->all()]);

        // Format the amount to 2 decimal places as required by PayFast
        $amount = number_format($request->amount, 2, '.', '');
        Log::info('Formatted payment amount: ' . $amount);

        // Prepare PayFast data array in the exact required order
        $data = [
            'merchant_id'   => env('PAYFAST_MERCHANT_ID'), // Merchant ID from .env
            'merchant_key'  => env('PAYFAST_MERCHANT_KEY'), // Merchant Key from .env
            'return_url'    => route('checkout.success'), // URL after successful payment
            'cancel_url'    => route('checkout.cancel'), // URL if user cancels payment
            'notify_url'    => route('checkout.notify'), // URL for PayFast IPN notification
            'name_first'    => 'John', // Customer first name
            'name_last'     => 'Doe',  // Customer last name
            'email_address' => 'customer@example.com', // Customer email
            'm_payment_id'  => time(), // Unique payment ID (using timestamp here)
            'amount'        => $amount, // Payment amount
            'item_name'     => 'Test Product', // Description of product/service
        ];

        Log::info('Prepared PayFast data array.', $data);

        // Generate signature for security
        $signature = $this->generateSignature($data);
        $data['signature'] = $signature;
        Log::info('Signature generated and added to data array.', ['signature' => $signature]);

        // Build an auto-submitting form for PayFast POST
        $html = '<form action="' . $this->payfastUrl . '" method="POST" id="payfast_form">';
        foreach ($data as $key => $value) {
            $html .= '<input type="hidden" name="' . $key . '" value="' . $value . '">';
            Log::info('Adding hidden input to form', ['key' => $key, 'value' => $value]);
        }
        $html .= '</form>';
        $html .= '<script>document.getElementById("payfast_form").submit();</script>';

        Log::info('Checkout form generated and ready to submit.');

        // Return the form so user is redirected to PayFast
        return $html;
    }

    /**
     * Generate PayFast signature for data array
     */
    private function generateSignature(array $data)
    {
        $passPhrase = env('PAYFAST_PASSPHRASE'); // Get passphrase from .env
        Log::info('Generating signature with passphrase from env.');

        // Concatenate all non-empty fields in the array in order, with '&' separator
        $pfOutput = '';
        foreach ($data as $key => $val) {
            if ($val !== '') {
                $pfOutput .= $key . '=' . urlencode(trim($val)) . '&';
            }
        }

        // Remove the trailing '&'
        $getString = rtrim($pfOutput, '&');

        // Append passphrase if exists
        if (!empty($passPhrase)) {
            $getString .= '&passphrase=' . urlencode(trim($passPhrase));
        }

        Log::info('String to be hashed for signature: ' . $getString);

        // MD5 hash of the concatenated string
        $signature = md5($getString);

        Log::info('Generated MD5 signature: ' . $signature);

        return $signature;
    }

    /**
     * Handle successful payment return
     */
    public function success()
    {
        Log::info('PayFast payment successful.');
        return "✅ Payment successful!";
    }

    /**
     * Handle canceled payment return
     */
    public function cancel()
    {
        Log::warning('PayFast payment canceled by user.');
        return "⚠️ Payment canceled.";
    }

    /**
     * Handle PayFast Instant Payment Notification (IPN)
     */
    public function notify(Request $request)
    {
        $data = $request->all();
        Log::info('Received PayFast notify request.', $data);

        // Here you can add verification & update database
        // Example: Verify signature, check payment status, etc.

        return response('OK', 200); // Respond to PayFast
    }
}
