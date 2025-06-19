<?php

return [
    // Organization selection page
    'select' => [
        'title' => 'Select Organization',
        'subtitle' => 'Choose an organization to continue, or create a new one.',
        'no_organizations' => 'No Organizations',
        'no_access' => "You don't have access to any organizations yet.",
        'or' => 'or',
        'create_new' => 'Create New Organization',
    ],

    // Organization creation
    'create' => [
        'title' => 'Create organization',
        'description' => 'Create your own organization.',
        'page_title' => 'Create an organization',
        'name_label' => 'Name',
        'name_placeholder' => 'Acme Corporation',
        'slug_label' => 'Slug',
        'slug_placeholder' => 'acme-corporation',
        'create_button' => 'Create Organization',
    ],

    // Leave organization
    'leave' => [
        'title' => 'Leave Organization',
        'description' => 'Are you sure you want to leave this organization?',
        'warning' => 'This action cannot be undone. You will lose access to all resources and data associated with this organization.',
        'confirm_button' => 'Leave Organization',
        'cancel_button' => 'Cancel',
        // Additional leave-related keys
        'card_title' => 'Leave organization',
        'card_description' => 'Leave this organization and remove your access to all its resources',
        'card_warning' => 'Please proceed with caution, this cannot be undone.',
        'button' => 'Leave organization',
        'confirm_dialog_title' => 'Are you sure you want to leave this organization?',
        'confirm_dialog_description' => 'Once you leave <strong>:name</strong>, you will lose access to all its resources and will need to be re-invited to rejoin. Please enter your password to confirm you would like to leave this organization.',
        'password_label' => 'Password',
        'password_placeholder' => 'Password',
    ],

    // Organization settings
    'settings' => [
        'general_info' => 'General information',
        'update_info' => 'Update your organization information',
        'name_label' => 'Organization name',
        'name_placeholder' => 'Organization name',
        'slug_label' => 'Organization slug',
        'slug_placeholder' => 'Organization slug',
    ],

    // Members and roles
    'members' => [
        'role_label' => 'User\'s role',
        'leave_action' => 'Leave organization',
    ],
];
