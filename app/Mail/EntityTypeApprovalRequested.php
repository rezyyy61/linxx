<?php

namespace App\Mail;

use App\Models\PoliticalProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EntityTypeApprovalRequested extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public PoliticalProfile $profile, public string $requestedType)
    {
    }

    public function build()
    {
        $labels = [
            'party'      => ['en' => 'Party',      'fa' => 'حزب'],
            'collective' => ['en' => 'Collective', 'fa' => 'گروه'],
            'media'      => ['en' => 'Media',      'fa' => 'رسانه'],
            'individual' => ['en' => 'Individual', 'fa' => 'حقیقی'],
        ];
        $reqEn = $labels[$this->requestedType]['en'] ?? ucfirst($this->requestedType);
        $reqFa = $labels[$this->requestedType]['fa'] ?? $this->requestedType;
        $curEn = $labels[$this->profile->entity_type]['en'] ?? ucfirst($this->profile->entity_type);
        $curFa = $labels[$this->profile->entity_type]['fa'] ?? $this->profile->entity_type;

        $subject = 'Linxx – Entity type request received | دریافت درخواست تغییر نوع پروفایل';
        return $this->subject($subject)
            ->view('emails.political_profile.entity_type_pending')
            ->with([
                'userName'   => $this->profile->user?->name ?: 'User',
                'requestedEn'=> $reqEn,
                'requestedFa'=> $reqFa,
                'currentEn'  => $curEn,
                'currentFa'  => $curFa,
                'profileUrl' => url('/settings/political-profile'),
                'supportUrl' => url('/support'),
                'homeUrl'    => url('/'),
                'appName'    => config('app.name', 'Linxx'),
                'logoUrl'    => asset('images/linxx-email-logo.png'),
                'brand'      => [
                    'primary' => '#ef4444',
                    'accent'  => '#111827'
                ],
                'preheader'  => 'Your profile type change is pending review | درخواست تغییر نوع پروفایل شما در انتظار تأیید است',
            ]);
    }
}
