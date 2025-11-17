<?php

namespace App\Service\Sms;

use Psr\Cache\CacheItemPoolInterface;
use App\Exception\SmsRateLimitsExceededException;

class ServiceASender implements SmsSenderInterface
{
    private const MAX_PER_MINUTE = 5;
    private const CACHE_KEY = 'sms_a_rate_limit_';
    private CacheItemPoolInterface $cache;
    
    // Inject the cache service (e.g., 'cache.rate_limiter')
    public function __construct(CacheItemPoolInterface $rateLimitCache) 
    {
        $this->cache = $rateLimitCache;
    }

    public function getName(): string
    {
        return 'Service A';
    }
    
    // Logic to check the current count in the cache
    public function canSend(): bool
    {
        $currentMinute = (new \DateTime())->format('YmdHi');
        $cacheKey = self::CACHE_KEY . $currentMinute;

        $item = $this->cache->getItem($cacheKey);
        $count = $item->get() ?? 0;

        return $count < self::MAX_PER_MINUTE;
    }

    public function sendSms(string $to, string $content): bool
    {
        if (!$this->canSend()) {
            // Throw a specific exception to signal the gateway to switch
            throw new SmsRateLimitsExceededException('Service A rate limit exceeded.');
        }

        // --- Simulate actual external API call ---
        // $externalApi->send($to, $content);
        // ... (check response from external service)
        
        // On success: Increment the counter and set the expiration to the end of the current minute
        $currentMinute = (new \DateTime())->format('YmdHi');
        $cacheKey = self::CACHE_KEY . $currentMinute;
        $item = $this->cache->getItem($cacheKey);
        
        $count = $item->get() ?? 0;
        $item->set($count + 1);
        
        // Calculate seconds remaining in the minute
        $secondsToMinuteEnd = 60 - (int)(new \DateTime())->format('s');
        $item->expiresAfter($secondsToMinuteEnd);
        
        $this->cache->save($item);

        return true; // Assume success
    }
}