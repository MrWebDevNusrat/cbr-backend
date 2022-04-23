<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <style type="text/css">
        .button {
            background-color: #4CAF50; /* Green */
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
        }
    </style>
</head>
<body>
<div>
    Hi {{ $details['firstname'] }} {{ $details['surname'] }},
    <br>
    Thank you for creating an account with us. Don't forget to complete your registration!
    <br>
    Please click on the link below or copy it into the address bar of your browser to confirm your email address:
    <br>
    <b>verify code is:  {{$details['verify_code']}}</b>
    <br>
    <a href="{{ URL::to('api/auth/verify/'.$details['verify_code'])}}" class="button">Confirm my email address </a>

    <br/>
</div>
</body>
</html>
