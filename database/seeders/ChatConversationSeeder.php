<?php

namespace Database\Seeders;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ChatPresence;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ChatConversationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@example.com')->first();
        $customer = User::query()->updateOrCreate(
            ['email' => 'maryynahbrazils@gmail.com'],
            [
                'name' => 'Mary Ynah Brazil Sese',
                'password' => bcrypt('12345678'),
                'phone' => '09171234567',
                'is_admin' => false,
            ]
        );

        $this->seedCustomerConversation($customer, $admin);
        $this->seedGuestConversation($admin);
    }

    private function seedCustomerConversation(User $customer, ?User $admin): void
    {
        $conversation = ChatConversation::query()->updateOrCreate(
            ['user_id' => $customer->id],
            [
                'guest_session_id' => null,
                'guest_name' => null,
                'guest_email' => null,
                'status' => 'open',
            ]
        );

        $this->resetConversationState($conversation);

        $baseTime = Carbon::now()->subHours(2);

        $messages = [
            [
                'sender_type' => 'customer',
                'user_id' => $customer->id,
                'body' => 'Hi! Do you have a price list for your cookie boxes?',
                'created_at' => $baseTime->copy(),
            ],
            [
                'sender_type' => 'admin',
                'user_id' => $admin?->id,
                'body' => 'Hello! Yes, we do. Tell us which size or set you want and we can send the latest price.',
                'created_at' => $baseTime->copy()->addMinutes(4),
            ],
            [
                'sender_type' => 'customer',
                'user_id' => $customer->id,
                'body' => 'Can you also do pickup tomorrow afternoon?',
                'created_at' => $baseTime->copy()->addMinutes(12),
            ],
            [
                'sender_type' => 'system',
                'user_id' => null,
                'body' => 'We offer delivery and pickup options! Kindly share your location so we can check availability and delivery fees.',
                'created_at' => $baseTime->copy()->addMinutes(12),
            ],
        ];

        $this->seedMessages($conversation, $messages);

        $conversation->update([
            'last_message_at' => $baseTime->copy()->addMinutes(12),
            'customer_last_read_at' => $baseTime->copy()->addMinutes(12),
            'admin_last_read_at' => $baseTime->copy()->addMinutes(12),
        ]);

        if ($admin) {
            ChatPresence::query()->updateOrCreate(
                [
                    'conversation_id' => null,
                    'user_id' => $admin->id,
                    'session_id' => null,
                    'role' => 'admin',
                ],
                [
                    'last_seen_at' => now()->subMinute(),
                ]
            );
        }
    }

    private function seedGuestConversation(?User $admin): void
    {
        $conversation = ChatConversation::query()->updateOrCreate(
            [
                'guest_name' => 'Guest Shopper',
                'guest_email' => 'guest@example.com',
            ],
            [
                'user_id' => null,
                'guest_session_id' => 'seeded-guest-session',
                'status' => 'open',
            ]
        );

        $this->resetConversationState($conversation);

        $baseTime = Carbon::now()->subMinutes(55);

        $messages = [
            [
                'sender_type' => 'customer',
                'user_id' => null,
                'body' => 'Hello, what time do you open?',
                'created_at' => $baseTime->copy(),
            ],
            [
                'sender_type' => 'system',
                'user_id' => null,
                'body' => 'Hello! We are open daily from 8:00 AM to 8:00 PM. We would love to serve you during those hours!',
                'created_at' => $baseTime->copy(),
            ],
            [
                'sender_type' => 'admin',
                'user_id' => $admin?->id,
                'body' => 'We are open today, and you can also message us here anytime.',
                'created_at' => $baseTime->copy()->addMinutes(6),
            ],
        ];

        $this->seedMessages($conversation, $messages);

        $conversation->update([
            'last_message_at' => $baseTime->copy()->addMinutes(6),
            'customer_last_read_at' => $baseTime->copy()->addMinutes(6),
            'admin_last_read_at' => $baseTime->copy()->addMinutes(6),
        ]);
    }

    private function resetConversationState(ChatConversation $conversation): void
    {
        $conversation->messages()->delete();
        ChatPresence::query()->where('conversation_id', $conversation->id)->delete();
    }

    private function seedMessages(ChatConversation $conversation, array $messages): void
    {
        foreach ($messages as $message) {
            $timestamp = $message['created_at'];

            ChatMessage::query()->create([
                'conversation_id' => $conversation->id,
                'user_id' => $message['user_id'],
                'sender_type' => $message['sender_type'],
                'body' => $message['body'],
                'delivered_at' => $timestamp,
                'seen_at' => $timestamp,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }
    }
}
