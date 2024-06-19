<?php

namespace RichardPost\FilamentWebhooks\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use RichardPost\FilamentWebhooks\Models\Webhook;

class WebhooksController extends Controller
{
    /**
     * @throws \Exception
     */
    public function handleNotification(Request $request, Webhook $webhook)
    {
        $trigger = $webhook->getTriggerConfig();

        $trigger->getBeforeHandleNotification()($request, $webhook->trigger);

        $resources = $trigger->getExternalResources($webhook);

        foreach($resources as $resource) {
            //Execute actions

            continue;
        }
    }
}
