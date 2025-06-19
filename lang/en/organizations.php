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
    ],
];
