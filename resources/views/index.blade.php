<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet" />
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" />
    <title>Finanss</title>
</head>
<body>
<div class="container">
    <div class="header"></div>

    <span class="brand">Finanss</span>
    <span class="login"><a href="{{ url('/login') }}">Log in to your account</a></span>

    <span class="description">Take control of your finances with ease, achieve your financial goals.</span>

    <div class="cta-section">
        <a href="{{ url('/registration') }}" class='cta-link'><span class='cta'>Ready to get started?</span></a>
    </div>
</div>
</body>
</html>

