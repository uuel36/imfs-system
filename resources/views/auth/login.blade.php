<title>IFMS / Login</title>
@extends('layouts.app')

@section('content')
<head>
    <link rel="shortcut icon" type="x-icon" href="/img/logo.png">

    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a81368914c.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<img class="image-up" src="/img/up.png">
    <img class="wave" src="img/wave2.png">
    <div class="container">
         <!-- "up" image -->
        <div class="img">
            <!-- ... (your existing image content) ... -->
        </div>
        <div class="login-container">
            <div class="login-content">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div>
                        
<h6 class="logo">MS GREENGOLD FARM BANANA VENTURES, INC.</h6>
                        <h1>LOG IN</h1>
                        <div class="input-div one">
                            <div class="i">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="div">
                                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror; input" name="username" required placeholder="Username" value="{{ old('username') }}" autocomplete="username">
                            </div>
                        </div>
                        <div class="input-div pass">
                            <div class="i">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div class="div">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror; input" name="password" required placeholder="Password" autocomplete="current-password">
                            </div>
                        </div>
                        @if ($errors->has('username'))
                            <div class="alert alert-danger" role="alert">
                                These credentials do not match our records.
                            </div>
                        @endif

                        @if ($errors->has('password') && !$errors->has('username'))
                            <div class="alert alert-danger" role="alert">
                                The provided password is incorrect.
                            </div>
                        @endif
                        <p class="login-box-msg">Sign in to start your session</p>
                        <button type="submit" class="btn btn-primary">
                            {{ __('Login') }}
                        </button>
                    </div>
                </form>
            </div>

          


    </div>
    <img class="image-down" src="/img/down.png">
    <script type="text/javascript" src="js/main.js"></script>
    <style>
    /* Add styles for the "up" and "down" images */
    /* Add styles for the "up" and "down" images */
    .image-up {
            position: fixed;
            top: 2%; /* Adjust the top position as a percentage of the viewport height */
            right: 2%; /* Adjust the right position as a percentage of the viewport width */
            opacity: 1; /* Show the "up" image on all screens */
            width: 100px; /* Adjust the width to make the image smaller */
            height: auto; /* Maintain the aspect ratio */
            transition: opacity 0.5s ease-in-out; /* Add a transition for smooth appearance */
        }

        .image-down {
            position: fixed;
            bottom: 2%; /* Adjust the bottom position as a percentage of the viewport height */
            left: 2%; /* Adjust the left position as a percentage of the viewport width */
            opacity: 1; /* Show the "down" image on all screens */
            width: 100px; /* Adjust the width to make the image smaller */
            height: auto; /* Maintain the aspect ratio */
            transition: opacity 0.5s ease-in-out; /* Add a transition for smooth appearance */
        }
        /* Hide the "up" and "down" images on desktop screens */
        @media (min-width: 769px) {
            .image-up,
            .image-down {
                display: none;
            }
        }
        .login-container {
            display: flex;
            justify-content: flex-end; /* This aligns the login-content to the right */
            align-items: center; /* This centers the login-content vertically */
            height: 100vh; /* This makes sure the container takes up the full height of the viewport */
            width: 80%; /* Adjust the width to control how far to the right it should be */
            margin-left: auto; /* This will push the container to the right edge of the screen */
        }
        .logo {
            position: absolute;
            top: 20px; /* Adjust the top position to your liking */
            left: 20px; /* Adjust the left position to your liking */
            opacity: 0; /* Initially, hide the logo */
            transition: opacity 0.5s ease-in-out; /* Add a transition for smooth appearance */
        }

        /* Media query for mobile devices */
        @media (max-width: 768px) {
            .logo {
                opacity: 1; /* Show the logo with a morphing animation on mobile */
            }
        }
        /* Additional CSS styles can be added here for further customization */
    </style>
</body>
@endsection