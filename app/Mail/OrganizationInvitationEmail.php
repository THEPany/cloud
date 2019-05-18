<?php

namespace App\Mail;

use App\{Organization, User};
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrganizationInvitationEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var \App\Organization
     */
    public $organization;

    /**
     * @var \App\User
     */
    public $user;

    /**
     * @var string
     */
    public $url;

    /**
     * Create a new message instance.
     *
     * @param \App\Organization $organization
     * @param \App\User $user
     */
    public function __construct(Organization $organization, User $user)
    {
        $this->organization = $organization;
        $this->user = $user;
        $this->url = URL::temporarySignedRoute(
            'organizations.invitation', now()->addMinutes(30), ['organization' => $organization, 'user' => $user]
        );
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.organization.invitation_contributor');
    }
}
