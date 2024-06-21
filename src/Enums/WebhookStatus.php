<?php

namespace RichardPost\FilamentWebhooks\Enums;

enum WebhookStatus: string
{
    case Subscribed = 'subscribed';
    case Unsubscribed = 'unsubscribed';
}
