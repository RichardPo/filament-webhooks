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

        $response = $trigger->beforeHandleNotification($request, $webhook);

        if($response !== true) {
            return $response;
        }

        $resources = $trigger->getExternalResources($request, $webhook);

        foreach($resources as $resource) {
            //Execute actions

            continue;
        }

        return $trigger->getSuccessfulResponse();
    }

    /**
     * @throws \Exception
     */
    public function handleLifecycleNotification(Request $request, Webhook $webhook)
    {
        $trigger = $webhook->getTriggerConfig();

        $response = $trigger->beforeHandleNotification($request, $webhook);

        if($response !== true) {
            return $response;
        }

        return $trigger->handleLifecycleNotification($request, $webhook);
    }
}
