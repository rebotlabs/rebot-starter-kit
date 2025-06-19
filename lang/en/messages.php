<?php

return [
    // General success messages
    'success' => [
        'invitation_accepted' => 'Invitation accepted successfully!',
        'invitation_rejected' => 'Invitation rejected.',
        'invitation_deleted' => 'Invitation deleted successfully.',
        'member_removed' => 'Member removed successfully.',
        'email_verified' => 'Email verified successfully!',
    ],

    // Error messages
    'error' => [
        'auth_required' => 'Please log in with the invited email address to accept this invitation.',
        'login_required' => 'You must be logged in to resend verification code.',
    ],

    // Status messages
    'status' => [
        'verification_code_sent' => 'verification-code-sent',
        'verification_link_sent' => 'verification-link-sent',
    ],

    // Leave organization messages
    'leave_organization' => [
        'create_or_join' => 'You have left the organization. Create or join a new organization to continue.',
        'success' => 'You have successfully left the organization.',
        'select_another' => 'You have successfully left the organization. Please select another organization to continue.',
    ],

    // Password reset
    'password_reset' => [
        'link_sent' => 'A reset link will be sent if the account exists.',
    ],
];
