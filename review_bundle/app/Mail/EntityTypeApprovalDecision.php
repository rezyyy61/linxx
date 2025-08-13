<?php

namespace App\Mail;

use App\Models\PoliticalProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EntityTypeApprovalDecision extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PoliticalProfile $profile,
        public string $decision,
        public ?string $reason = null
    ) {}

    public function build()
    {
        $user = $this->profile->user;
        $appName = config('app.name');
        $profileUrl = rtrim(config('app.url'), '/').'/dashboard/political-profile';
        $supportUrl = rtrim(config('app.url'), '/').'/support';
        $requestedType = $this->profile->entity_type ?: 'individual';

        $subject = $this->decision === 'approved'
            ? "{$appName} – Entity type approved"
            : "{$appName} – Entity type rejected";

        return $this->subject($subject)
            ->view('emails.political_profile.entity-type-decision', [
                'appName' => $appName,
                'userName' => $user?->name ?? 'User',
                'requestedType' => $requestedType,
                'decision' => $this->decision,
                'reason' => $this->reason,
                'profileUrl' => $profileUrl,
                'supportUrl' => $supportUrl,
            ]);
    }
}
