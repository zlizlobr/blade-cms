<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines (English)
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    // Login page
    'login' => [
        'title' => 'Log in',
        'email' => 'Email',
        'password' => 'Password',
        'remember' => 'Remember me',
        'forgot_password' => 'Forgot your password?',
        'submit' => 'Log in',
    ],

    // Register page
    'register' => [
        'title' => 'Register',
        'name' => 'Name',
        'email' => 'Email',
        'password' => 'Password',
        'confirm_password' => 'Confirm Password',
        'already_registered' => 'Already registered?',
        'submit' => 'Register',
    ],

    // Forgot password
    'forgot_password' => [
        'title' => 'Forgot Password',
        'description' => 'Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.',
        'email' => 'Email',
        'submit' => 'Email Password Reset Link',
    ],

    // Reset password
    'reset_password' => [
        'title' => 'Reset Password',
        'email' => 'Email',
        'password' => 'Password',
        'confirm_password' => 'Confirm Password',
        'submit' => 'Reset Password',
    ],

    // Confirm password
    'confirm_password' => [
        'title' => 'Confirm Password',
        'description' => 'This is a secure area of the application. Please confirm your password before continuing.',
        'password' => 'Password',
        'submit' => 'Confirm',
    ],

    // Verify email
    'verify_email' => [
        'title' => 'Verify Email Address',
        'description' => 'Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.',
        'resend' => 'Resend Verification Email',
        'logout' => 'Log Out',
        'sent' => 'A new verification link has been sent to your email address.',
    ],

];
