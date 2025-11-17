<?php

namespace App\Service\Sms;

use App\Service\Sms\SmsSenderInterface;
use App\Exception\SmsRateLimitsExceededException;

class SmsGateway implements SmsSenderInterface // Can also implement the interface for unified use
{
    /** @var SmsSenderInterface[] */
    private iterable $senders;

    // Use Dependency Injection to inject all services tagged as 'app.sms_sender'
    public function __construct(iterable $senders)
    {
        $this->senders = $senders;
    }

    public function canSend(): bool
    {
        foreach ($this->senders as $sender) {
            if ($sender->canSend()) {
                return true;
            }
        }
        return false;
    }

    public function sendSms(string $to, string $content): bool
    {
        // 1. Try to find a sender that hasn't hit its limit yet
        foreach ($this->senders as $sender) {
            if ($sender->canSend()) {
                // Sender is available based on local rate-tracking. Use it.
                return $sender->sendSms($to, $content);
            }
        }
        
        // 2. If no sender is available, throw an exception
        throw new SmsRateLimitsExceededException('All SMS services are currently rate-limited.');
    }
    
    // The getName method for the gateway would be simple or omitted.
    public function getName(): string
    {
        return 'Sms Gateway Manager';
    }
}