<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PayFast Checkout</title>
    <style>
        /* Reset */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .checkout-card {
            background: #fff;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .checkout-card h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-size: 1.8rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1rem;
            transition: border 0.3s;
        }

        input[type="text"]:focus {
            border-color: #007bff;
            outline: none;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #007bff;
            color: white;
            font-size: 1.1rem;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #0056b3;
        }

        .note {
            text-align: center;
            margin-top: 15px;
            color: #888;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>

    <div class="checkout-card">
        <h1>Checkout</h1>

        <form method="POST" action="{{ route('checkout.process') }}">
            @csrf

            <div class="form-group">
                <label for="item_name">Item Name</label>
                <input type="text" id="item_name" name="item_name" value="{{ old('item_name', 'Test Product') }}" required>
            </div>

            <div class="form-group">
                <label for="amount">Amount (ZAR)</label>
                <input type="text" id="amount" name="amount" value="{{ old('amount', '100.00') }}" required>
            </div>

            <button type="submit">Pay Now</button>
        </form>

        <p class="note">Secure payment via PayFast</p>
    </div>

</body>

</html>