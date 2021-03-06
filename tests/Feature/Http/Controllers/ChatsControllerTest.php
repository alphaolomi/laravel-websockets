<?php

namespace Tests\Feature\Http\Controllers;

use App\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function testIndexWithAuth()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('chats.index'));

        $response->assertStatus(200);
        $this->assertAuthenticated($guard = null);
    }

    /** @test */
    public function testIndexWithoutAuth()
    {
        $this->markTestIncomplete();
        $response = $this->get(route('chats.index'));

        $response->assertLocation('/login');
    }


    /** @test */
    public function testFetchMessages()
    {
        $users = \App\Models\User::factory()->count(3)
            ->create()
            ->each(function ($user) {
                $user->messages()->save(\App\Models\Message::factory()->make());
            });

        $messages = \App\Models\Message::with('user')->get();

        $response = $this->actingAs(\App\Models\User::first())
            ->get(route('chats.fetch'));

        $response->assertStatus(200);
        $response->assertJson($messages->toArray(), $strict = false);
        $this->assertAuthenticated($guard = null);
    }


    /** @test */
    public function testSendMessage()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('chats.send'));

        $response
            ->assertOk();

        $this->assertAuthenticated($guard = null);
    }
}
