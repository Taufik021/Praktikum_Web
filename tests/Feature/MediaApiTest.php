<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_media_crud_endpoints_return_expected_status_codes(): void
    {
        $this->getJson('/api/media')->assertOk();

        $this->postJson('/api/media', [
            'title' => 'FlipPlay API Test',
            'type' => 'Anime',
            'genre' => 'Adventure',
        ])
            ->assertCreated()
            ->assertJsonPath('data.title', 'FlipPlay API Test');

        $media = Media::where('title', 'FlipPlay API Test')->firstOrFail();

        $this->getJson("/api/media/{$media->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $media->id);

        $this->patchJson("/api/media/{$media->id}", [
            'genre' => 'Fantasy',
        ])
            ->assertOk()
            ->assertJsonPath('data.genre', 'Fantasy');

        $this->deleteJson("/api/media/{$media->id}")->assertOk();
        $this->getJson("/api/media/{$media->id}")->assertNotFound();
    }

    public function test_store_and_update_validation_return_422(): void
    {
        $this->postJson('/api/media', [
            'title' => '',
            'type' => '',
            'genre' => '',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'type', 'genre']);

        $media = Media::create([
            'title' => 'Existing Media',
            'type' => 'Movie',
            'genre' => 'Drama',
        ]);

        $this->postJson('/api/media', [
            'title' => 'Existing Media',
            'type' => 'Movie',
            'genre' => 'Drama',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);

        $this->patchJson("/api/media/{$media->id}", [
            'title' => '',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }

    public function test_user_can_attach_list_and_detach_media(): void
    {
        $user = User::factory()->create();
        $media = Media::create([
            'title' => 'Collection Test Media',
            'type' => 'Anime',
            'genre' => 'Action',
        ]);

        $this->postJson("/api/users/{$user->id}/media/{$media->id}")->assertOk();
        $this->postJson("/api/users/{$user->id}/media/{$media->id}")->assertStatus(409);

        $this->getJson("/api/users/{$user->id}/media")
            ->assertOk()
            ->assertJsonPath('data.0.id', $media->id);

        $this->deleteJson("/api/users/{$user->id}/media/{$media->id}")->assertOk();
        $this->deleteJson("/api/users/{$user->id}/media/{$media->id}")->assertNotFound();
    }
}
