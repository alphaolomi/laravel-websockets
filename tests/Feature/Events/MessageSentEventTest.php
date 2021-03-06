<?php

namespace Tests\Feature\Events;

use App\Events\MessageSent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MessageSentEventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function testMessageSent()
    {
        Event::fake();

        $m = \App\Models\Message::factory()->make();
        $user = \App\Models\User::factory()->create();

        $message = $user->messages()->create([
            'message' => $m->message
        ]);

        broadcast(new MessageSent($user, $message))->toOthers();

        Event::assertDispatched(MessageSent::class, function ($e) use ($message) {
            return $e->message->id === $message->id;
        });
    }
}
