<?php

namespace RichardPost\FilamentWebhooks\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use RichardPost\FilamentWebhooks\Action;
use RichardPost\FilamentWebhooks\Models\Webhook;

class WebhooksController extends Controller
{
    public function handleNotification(Request $request, Webhook $webhook)
    {
        $trigger = $webhook->getTriggerConfig();

        $response = $trigger->beforeHandleNotification($request, $webhook);

        if($response !== true) {
            return $response;
        }

        $resources = $trigger->getExternalResources($request, $webhook);

        foreach($resources as $resource) {
            foreach($webhook->actions as $action) {
                $filamentAction = collect(filament('richardpost-filament-webhooks')->getActions())
                    ->filter(fn (Action $foundAction) => $foundAction->getName() === $action['type'])
                    ->firstOrFail();

                $filamentAction->executeAction($action['data'], $resource);
            }
        }

        return $trigger->getSuccessfulResponse();
    }

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
