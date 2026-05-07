<?php

namespace App\Notifications;

use App\Models\Merchant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AiQuotaWarning extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Merchant $merchant,
        public int $percentUsed,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $limit    = \App\Models\Merchant::AI_MONTHLY_LIMITS[$this->merchant->subscription_tier] ?? 0;
        $used     = $this->merchant->ai_monthly_messages_count;
        $isMax    = $this->percentUsed >= 100;
        $business = $this->merchant->business_name;

        $subject = $isMax
            ? "AI Chat limit reached — {$business}"
            : "AI Chat at {$this->percentUsed}% — {$business}";

        $headline = $isMax
            ? "Your AI auto-reply limit has been reached."
            : "Your AI auto-replies are at {$this->percentUsed}% of your monthly limit.";

        $body = $isMax
            ? "**{$business}** has used all {$limit} AI auto-replies for this month. "
              . "Auto-replies are paused until the 1st of next month. You can still reply manually from your inbox."
            : "**{$business}** has used {$used} out of {$limit} AI auto-replies this month. "
              . "Consider upgrading to the **Ultimate** plan for unlimited AI replies.";

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Hello, {$notifiable->name}!")
            ->line($headline)
            ->line($body)
            ->action('Manage AI Settings', url('/settings/ai'))
            ->line('Thank you for using Nexus CRM.');
    }
}
