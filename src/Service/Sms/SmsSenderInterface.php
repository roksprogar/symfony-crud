<?php

namespace App\Service\Sms;

interface SmsSenderInterface
{
    /**
     * Sends an SMS message to the specified recipient.
     * @param string $to The recipient's phone number.
     * @param string $content The message content.
     * @return bool True on successful sending, false otherwise.
     * @throws SmsRateLimitExceededException If the sender cannot send the message due to rate limits.
     */
    public function sendSms(string $to, string $content): bool;

    /**
     * Checks if the sender is currently able to send a message without hitting the rate limit.
     */
    public function canSend(): bool;
    
    /**
     * Gets a unique identifier for the service.
     */
    public function getName(): string;
}