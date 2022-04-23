<?php
/**
 * Created by PhpStorm.
 * User: mosesesan
 * Date: 8/3/16
 * Time: 3:23 PM
 */
?>
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
<h2>Reset Your Password</h2>

<div>
    {{--Dear {{ $details['firstname'] }} {{ $details['surname'] }},--}}
    Please follow the link below to
    <br>
    {{--<b>verify code is:  {{$details['verify_code']}}</b>--}}
    <br>
    {{--<a href="{{ str_replace('-api.', '.', URL::to('auth/restore?verify='.$details['verify_code']))}}" class="button">reset your password </a>--}}

</div>
</body>
</html>
