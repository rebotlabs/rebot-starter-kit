<?php

return [
    // Common buttons
    'buttons' => [
        'save' => 'Save',
        'cancel' => 'Cancel',
        'delete' => 'Delete',
        'edit' => 'Edit',
        'add' => 'Add',
        'remove' => 'Remove',
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
];
