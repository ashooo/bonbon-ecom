<?php

namespace Database\Seeders;

use App\Models\ChatAutoReply;
use Illuminate\Database\Seeder;

class ChatSupportSeeder extends Seeder
{
    public function run(): void
    {
        $replies = [
            [
                'trigger_type' => 'offline',
                'keyword' => null,
                'reply_text' => 'Thanks for contacting Bonbon. Our team is away right now, but we will get back to you as soon as we are online.',
                'is_active' => true,
                'sort_order' => 0,
            ],
            [
                'trigger_type' => 'keyword',
                'keyword' => 'price, cost, how much',
                'reply_text' => 'Hi! Our prices vary depending on the item. Could you tell us which product you are interested in? We will gladly provide the exact price!',
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'trigger_type' => 'keyword',
                'keyword' => 'open, hours, time, schedule',
                'reply_text' => 'Hello! We are open daily from 8:00 AM to 8:00 PM. We would love to serve you during those hours!',
                'is_active' => true,
                'sort_order' => 20,
            ],
            [
                'trigger_type' => 'keyword',
                'keyword' => 'location, where, address, store',
                'reply_text' => 'We are located at Paranaque City. You can visit us anytime during store hours. Let us know if you need directions!',
                'is_active' => true,
                'sort_order' => 30,
            ],
            [
                'trigger_type' => 'keyword',
                'keyword' => 'custom, cake design, personalized, order cake',
                'reply_text' => 'Yes, we accept custom cake orders! Please send us your preferred design, size, and date needed so we can assist you better.',
                'is_active' => true,
                'sort_order' => 40,
            ],
            [
                'trigger_type' => 'keyword',
                'keyword' => 'delivery, shipping, deliver, pickup',
                'reply_text' => 'We offer delivery and pickup options! Kindly share your location so we can check availability and delivery fees.',
                'is_active' => true,
                'sort_order' => 50,
            ],
        ];

        foreach ($replies as $reply) {
            ChatAutoReply::updateOrCreate(
                [
                    'trigger_type' => $reply['trigger_type'],
                    'keyword' => $reply['keyword'],
                ],
                [
                    'reply_text' => $reply['reply_text'],
                    'is_active' => $reply['is_active'],
                    'sort_order' => $reply['sort_order'],
                ]
            );
        }
    }
}
