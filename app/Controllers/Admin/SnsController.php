<?php

namespace App\Controllers\Admin;

namespace App\Controllers\Admin;

use App\Core\Http\Request;
use App\Core\Http\Response;
use Throwable;

class SnsController
{

    /** @throws */
    public function handleBounce(Request $request): Response
    {
        $notification = $this->getNotification($request);
        if ($notification && $notification['Type'] === 'SubscriptionConfirmation') {
            $this->confirmSubscription($notification['SubscribeURL']);
        } elseif ($notification) {
            $this->processBounce($notification);
        }
        return response()->json(['message' => 'Bounce handled']);

    }

    /** @throws */
    public function handleComplaint(Request $request): Response
    {
        $notification = $this->getNotification($request);
        if ($notification && $notification['Type'] === 'SubscriptionConfirmation') {
            $this->confirmSubscription($notification['SubscribeURL']);
        } elseif ($notification) {
            $this->processComplaint($notification);
        }
        return response()->json(['message' => 'Complaint handled']);
    }

    /** @throws */
    protected function getNotification(Request $request): ?array
    {
        $message = $request->get('Message', null);
        if ($message) {
            return json_decode(
                $message,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        }
        return null;
    }

    protected function processBounce($notification): void
    {
        logger("Bounce notification: {$notification}");
        // Extract relevant data from $notification and take action
    }

    protected function processComplaint($notification): void
    {
        logger("Complaint notification: {$notification}");
        // Extract relevant data from $notification and take action
    }

    protected function confirmSubscription(string $subscribeUrl): void
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $subscribeUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);

            if ($response === false) {
                logger('Curl error: ' . curl_error($ch));
            } else {
                logger("Subscription Confirmation Response: " . $response);
            }
            curl_close($ch);
        } catch (Throwable $e) {
            logger('Exception in confirmSubscription: ' . $e->getMessage());
        }
    }


}