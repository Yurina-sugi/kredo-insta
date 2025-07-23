<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>

<body>
    {{-- <p>Hello {{ $name }}!</p>
    <p>Thank you for registering.</p>
    <p>To start, please access the website <a href="{{ $app_url }}">here</a>.</p>
    <p>Thank you!</p> --}}
    <div class="email-header">
        <h1>Welcome to Insta App!</h1>
    </div>

    <div class="email-body">
        <p class="name">Hi {{ $name }}</p>
        <p>Thank you for signing up to Insta App!</p>
        <p>To get started, please confirm your email address by clicking the button below:</p>

        <p><a href="{{ $app_url }}" class="email-button">Confirm Email Address</a></p>

        <p>Best regards, <br>Kredo Team </p>
        <p class="not-me">If you did not sign up for this account, you can ignore this email.</p>
    </div>

    <div class="email-footer">
        <p>&copy; 2024 Kredo Insta App. All rights reserved.</p>
    </div>
</body>

</html>
