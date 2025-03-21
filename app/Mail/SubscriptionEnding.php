<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionEnding extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function build()
    {
        return $this->markdown('emails.subscription-ending')
                    ->subject('Obunangiz Tugash Arafasida')
                    ->with([
                        'user' => $this->subscription->user->name,
                        'plan' => $this->subscription->plan->name,
                        'ends_at' => $this->subscription->ends_at->format('Y-m-d H:i'),
                    ]);
    }
}