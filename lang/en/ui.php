<?php

return [
    'actions' => [
        'warning' => 'Warning',
    ],

    // Common buttons
    'buttons' => [
        'save' => 'Save',
        'cancel' => 'Cancel',
        'delete' => 'Delete',
        'edit' => 'Edit',
        'add' => 'Add',
        'remove' => 'Remove',
        'change' => 'Change',
        'confirm' => 'Confirm',
        'submit' => 'Submit',
        'close' => 'Close',
        'back' => 'Back',
        'continue' => 'Continue',
        'create' => 'Create',
        'update' => 'Update',
        'send' => 'Send',
        'resend' => 'Resend',
        'accept' => 'Accept',
        'reject' => 'Reject',
        'approve' => 'Approve',
        'decline' => 'Decline',
        'log_out' => 'Log out',
        'log_in' => 'Log in',
        'upload' => 'Upload',
        'choose_file' => 'Choose File',
    ],

    // Common labels
    'labels' => [
        'name' => 'Name',
        'email' => 'Email',
        'password' => 'Password',
        'confirm_password' => 'Confirm Password',
        'role' => 'Role',
        'status' => 'Status',
        'created_at' => 'Created',
        'updated_at' => 'Updated',
        'actions' => 'Actions',
    ],

    // Common messages
    'messages' => [
        'loading' => 'Loading...',
        'no_data' => 'No data available',
        'search_placeholder' => 'Search...',
        'required_field' => 'This field is required',
        'optional_field' => 'Optional',
    ],

    // Status indicators
    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'pending' => 'Pending',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'sent' => 'Sent',
        'delivered' => 'Delivered',
        'failed' => 'Failed',
    ],

    // Two-Factor Authentication
    'two_factor' => [
        'title' => 'Two-Factor Authentication',
        'description' => 'Add additional security to your account by enabling two-factor authentication.',
        'enabled' => 'You have enabled two-factor authentication.',
        'not_enabled' => 'You have not enabled two-factor authentication.',
        'enable' => 'Enable',
        'disable' => 'Disable',
        'setup_title' => 'Set up Two-Factor Authentication',
        'setup_description' => 'Scan the QR code with your authenticator app, then enter a verification code to confirm.',
        'qr_code_alt' => 'QR Code for Two-Factor Authentication',
        'enter_code' => 'Enter the 6-digit code from your authenticator app',
        'confirm' => 'Confirm & Enable',
        'recovery_codes_title' => 'Recovery Codes',
        'recovery_codes_description' => 'Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two-factor authentication device is lost.',
        'recovery_codes_saved' => 'I have saved these recovery codes in a safe place',
        'regenerate_recovery_codes' => 'Regenerate Recovery Codes',
        'disable_title' => 'Disable Two-Factor Authentication',
        'disable_description' => 'Enter your password to disable two-factor authentication.',
        'disable_warning' => 'This will reduce the security of your account.',
        'secret_key' => 'Secret Key',
        'copy' => 'Copy',
        'download' => 'Download',
        'print' => 'Print',
    ],

    // Password related
    'password' => [
        'reset' => 'Reset password',
        'confirm' => 'Confirm password',
        'confirm_placeholder' => 'Confirm password',
    ],

    // Verification
    'verification' => [
        'verify_email' => 'Verify Email',
        'code_sent' => 'A new verification code has been sent to your email address.',
        'enter_code' => 'Enter the 6-digit code sent to your email address',
        'didnt_receive' => 'Didn\'t receive the code?',
        'resend_code' => 'Resend verification code',
    ],

    // Organization specific
    'organization' => [
        'name' => 'Organization name',
        'name_placeholder' => 'Organization name',
        'slug' => 'Organization slug',
        'slug_placeholder' => 'Organization slug',
        'general_info' => 'General information',
        'update_info' => 'Update your organization information',
        'leave' => 'Leave organization',
        'leave_description' => 'Leave this organization and remove your access to all its resources',
        'leave_warning' => 'Please proceed with caution, this cannot be undone.',
        'leave_confirm_title' => 'Are you sure you want to leave this organization?',
        'leave_confirm_description' => 'Once you leave <strong>:name</strong>, you will lose access to all its resources and will need to be re-invited to rejoin. Please enter your password to confirm you would like to leave this organization.',
        // Owner management
        'owner_title' => 'Organization Owner',
        'owner_description' => 'Manage owner of the organization',
        'owner_info' => 'The organization owner is the person who has full control over the organization, including billing and settings.',
        'select_owner' => 'Select new owner',
        'search_user' => 'Search for a user...',
        'change_owner_title' => 'Are you sure you want to change organization ownership?',
        'change_owner_description' => 'Once you change the organization owner, the new owner will have full control over the organization, including billing and settings. Please confirm your action.',
        'change_owner_button' => 'Change Owner',
        // Delete organization
        'delete_title' => 'Delete organization',
        'delete_description' => 'Delete your organization and all of its resources',
        'delete_warning' => 'Please proceed with caution, this cannot be undone.',
        'delete_button' => 'Delete organization',
        'delete_confirm_title' => 'Are you sure you want to delete your organization?',
        'delete_confirm_description' => 'Once your organization is deleted, all of its resources and data will also be permanently deleted. Please enter your password to confirm you would like to permanently delete your organization.',
        // Success messages for organization deletion
        'delete_success' => 'Organization deleted successfully.',
        'delete_success_no_orgs' => 'Organization deleted successfully. You can now create a new organization or join an existing one.',
        'delete_success_switched' => 'Organization deleted successfully. You have been switched to your remaining organization.',
        'delete_success_select' => 'Organization deleted successfully. Please select an organization to continue.',
    ],

    // User account management
    'user' => [
        'delete_title' => 'Delete account',
        'delete_description' => 'Delete your account and all of its resources',
        'delete_warning' => 'Please proceed with caution, this cannot be undone.',
        'delete_button' => 'Delete account',
        'delete_confirm_title' => 'Are you sure you want to delete your account?',
        'delete_confirm_description' => 'Once your account is deleted, all of its resources and data will also be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.',
    ],

    // User roles and permissions
    'roles' => [
        'user_role' => 'User\'s role',
        'role' => 'Role',
        'owner' => 'Owner',
        'admin' => 'Admin',
        'member' => 'Member',
    ],

    // Members management
    'members' => [
        'remove_title' => 'Remove Member',
        'remove_description' => 'Are you sure you want to remove <strong>:name</strong> from this organization? This action cannot be undone.',
        'remove_button' => 'Remove Member',
    ],

    // Notifications
    'notifications' => [
        'title' => 'Notifications',
        'empty' => 'No notifications yet',
        'mark_as_read' => 'Mark as read',
        'mark_all_read' => 'Mark all as read',
        'delete' => 'Delete',
        'view_all' => 'View all notifications',
        'invitation_sent' => 'Invitation sent',
        'invitation_sent_message' => 'You have been invited to join {organization}',
        'invitation_sent_general' => 'You have been invited to join an organization',
        'email_verification' => 'Email verification',
        'email_verification_message' => 'Please verify your email address',
        'test' => 'Test Notification',
        'test_message' => 'This is a test notification',
        'general' => 'Notification',
        'general_message' => 'You have a new notification',
    ],

    // Time formatting
    'time' => [
        'just_now' => 'Just now',
        'minutes_ago' => '{count} minute ago|{count} minutes ago',
        'hours_ago' => '{count} hour ago|{count} hours ago',
        'days_ago' => '{count} day ago|{count} days ago',
    ],

    // Search
    'search' => [
        'placeholder' => 'Search in {organization}...',
        'quick_actions' => 'Quick Actions',
        'view_members' => 'View Members',
        'view_members_description' => 'Manage organization members',
        'organization_settings' => 'Organization Settings',
        'organization_settings_description' => 'Configure organization preferences',
        'recent' => 'Recent Searches',
        'no_recent_searches' => 'No recent searches',
        'results' => 'Search Results',
        'no_results' => 'No results found',
        'no_results_description' => 'Try adjusting your search terms for "{query}"',
    ],

    // Avatar
    'avatar' => [
        'title' => 'Avatar',
        'description' => 'Update your profile picture',
        'upload' => 'Upload Avatar',
        'change' => 'Change Avatar',
        'remove' => 'Remove Avatar',
        'upload_success' => 'Avatar uploaded successfully!',
        'delete_success' => 'Avatar removed successfully!',
        'validation' => [
            'required' => 'Please select an image to upload.',
            'must_be_image' => 'The file must be an image.',
            'invalid_type' => 'The avatar must be a file of type: jpeg, png, jpg, gif, webp.',
            'max_size' => 'The avatar must not be larger than 2MB.',
            'max_dimensions' => 'The avatar must not be larger than 1000x1000 pixels.',
            'upload_failed' => 'Failed to upload avatar. Please try again.',
            'delete_failed' => 'Failed to remove avatar. Please try again.',
        ],
    ],

    // Logo
    'logo' => [
        'title' => 'Logo',
        'description' => 'Update your organization logo',
        'upload' => 'Upload Logo',
        'change' => 'Change Logo',
        'remove' => 'Remove Logo',
        'upload_success' => 'Logo uploaded successfully!',
        'delete_success' => 'Logo removed successfully!',
        'validation' => [
            'required' => 'Please select an image to upload.',
            'must_be_image' => 'The file must be an image.',
            'invalid_type' => 'The logo must be a file of type: jpeg, png, jpg, gif, webp, svg.',
            'max_size' => 'The logo must not be larger than 2MB.',
            'upload_failed' => 'Failed to upload logo. Please try again.',
            'delete_failed' => 'Failed to remove logo. Please try again.',
        ],
    ],

    // Billing
    'billing' => [
        'title' => 'Billing & Subscription',
        'description' => 'Manage your subscription and billing information',
        'current_plan' => 'Current Plan',
        'no_subscription' => 'No Active Subscription',
        'no_subscription_description' => 'You don\'t have an active subscription yet.',
        'plan_name' => 'Plan',
        'status' => 'Status',
        'next_billing_date' => 'Next Billing Date',
        'trial_ends' => 'Trial Ends',
        'subscription_ends' => 'Subscription Ends',
        'quantity' => 'Quantity',
        'created_date' => 'Created',
        'manage_subscription' => 'Manage Subscription',
        'manage_subscription_description' => 'Update payment methods, download invoices, and more.',
        'open_billing_portal' => 'Open Billing Portal',
        'payment_method' => 'Payment Method',
        'no_payment_method' => 'No payment method on file',
        'card_ending_in' => ':brand ending in :last4',
        'expires' => 'Expires :month/:year',
        'recent_invoices' => 'Recent Invoices',
        'recent_invoices_description' => 'Your most recent billing invoices',
        'no_invoices' => 'No invoices yet',
        'invoice_date' => 'Date',
        'invoice_amount' => 'Amount',
        'invoice_actions' => 'Actions',
        'download_invoice' => 'Download',
        'view_invoice' => 'View',
        'contact_support_billing' => 'Contact support for next billing date',
        'status_active' => 'Active',
        'status_trialing' => 'Trial',
        'status_canceled' => 'Canceled',
        'status_incomplete' => 'Incomplete',
        'status_incomplete_expired' => 'Expired',
        'status_past_due' => 'Past Due',
        'status_unpaid' => 'Unpaid',
        'on_grace_period' => 'On Grace Period',
    ],
];
