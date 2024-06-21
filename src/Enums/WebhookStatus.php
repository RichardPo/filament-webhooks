<?php

namespace RichardPost\FilamentWebhooks\Enums;

enum WebhookStatus: string
{
    case Subscribed = 'subscribed';
    case Unsubscribed = 'unsubscribed';

    public static function getLabel(self $case): string
    {
        return match($case) {
            self::Subscribed => 'Subscribed',
            self::Unsubscribed => 'Not subscribed'
        };
    }

    public static function getColor(self $case): string
    {
        return match($case) {
            self::Subscribed => 'success',
            self::Unsubscribed => 'danger'
        };
    }
}
