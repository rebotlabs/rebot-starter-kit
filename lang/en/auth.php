<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    'two_factor_required' => 'Two-factor authentication is required.',

    // Page titles and descriptions
    'login' => [
        'title' => 'Log in',
        'description' => 'Enter your credentials to access your account',
        'welcome_back' => 'Welcome back',
    ],

    'register' => [
        'title' => 'Create account',
        'description' => 'Create a new account to get started',
        'welcome' => 'Welcome',
    ],

    'forgot_password' => [
        'title' => 'Forgot password',
        'description' => 'Enter your email address and we\'ll send you a link to reset your password',
        'reset_link_sent' => 'We have emailed your password reset link.',
    ],

    'reset_password' => [
        'title' => 'Reset password',
        'description' => 'Enter your new password below',
    ],

    'confirm_password' => [
        'title' => 'Confirm password',
        'description' => 'Please confirm your password before continuing',
        'warning' => 'This is a secure area of the application. Please confirm your password before continuing.',
    ],

    'verify_email' => [
        'title' => 'Verify email',
        'description' => 'Please verify your email address by clicking on the link we just emailed to you.',
        'verification_sent' => 'A new verification link has been sent to the email address you provided during registration.',
    ],

    'verify_email_otp' => [
        'title' => 'Verify your email',
        'description' => 'We\'ve sent a 6-digit verification code to your email address. Please enter it below to verify your account.',
        'code_sent' => 'A new verification code has been sent to your email address.',
        'enter_code' => 'Enter the 6-digit code sent to your email address',
    ],

    'two_factor' => [
        'title' => 'Two-Factor Authentication',
        'description' => 'Please enter the 6-digit code from your authenticator app.',
        'authentication_code' => 'Authentication Code',
        'verify_code' => 'Verify Code',
        'invalid_code' => 'The provided two-factor authentication code was invalid.',
        'session_expired' => 'Your session has expired. Please log in again.',
        'invalid_session' => 'Invalid authentication session.',
    ],

    // Form labels
    'labels' => [
        'email' => 'Email address',
        'password' => 'Password',
        'confirm_password' => 'Confirm Password',
        'new_password' => 'New Password',
        'current_password' => 'Current Password',
        'name' => 'Full Name',
        'remember_me' => 'Remember me',
        'code' => 'Verification Code',
    ],

    // Form placeholders
    'placeholders' => [
        'email' => 'Enter your email address',
        'password' => 'Enter your password',
        'confirm_password' => 'Confirm your password',
        'new_password' => 'Enter your new password',
        'current_password' => 'Enter your current password',
        'name' => 'Enter your full name',
    ],

    // Buttons
    'buttons' => [
        'log_in' => 'Log in',
        'register' => 'Create account',
        'send_reset_link' => 'Email Password Reset Link',
        'reset_password' => 'Reset Password',
        'confirm' => 'Confirm',
        'verify_email' => 'Resend Verification Email',
        'verify_code' => 'Verify Code',
        'back_to_login' => '← Back to login',
        'resend_code' => 'Resend Code',
    ],

    // Links
    'links' => [
        'forgot_password' => 'Forgot your password?',
        'remember_password' => 'Remembered your password?',
        'already_registered' => 'Already registered?',
        'no_account' => 'Don\'t have an account?',
    ],

    // Messages
    'messages' => [
        'unverified_email' => 'Your email address is unverified.',
        'verification_link_sent' => 'A new verification link has been sent to your email address.',
    ],
];
