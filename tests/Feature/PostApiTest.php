<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed some tags for testing
        Tag::create(['name' => 'Ashot']);
        Tag::create(['name' => 'Brat']);
    }

    /** @test */
    public function it_creates_a_post_with_new_tags()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/posts', [
            'title' => 'My First Post',
            'content' => 'Lorem ipsum',
            'tags' => ['Ashot', 'NewTag'],
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Post created successfully',
                'post' => [
                    'title' => 'My First Post',
                    'content' => 'Lorem ipsum',
                    'user_id' => $user->id,
                ],
            ]);

        $this->assertDatabaseHas('posts', ['title' => 'My First Post']);
        $this->assertDatabaseHas('tags', ['name' => 'NewTag']);
        $this->assertDatabaseHas('post_tag', [
            'post_id' => Post::latest()->first()->id,
            'tag_id' => Tag::where('name', 'Ashot')->first()->id,
        ]);
    }

    /** @test */
    public function it_fails_to_update_post_without_authorization()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($otherUser);
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->putJson("/api/posts/{$post->id}", [
            'title' => 'Unauthorized Update',
            'content' => 'Updated content',
            'tags' => ['Ashot'],
        ]);

        $response->assertStatus(500);
    }

    /** @test */
    public function it_fails_to_update_nonexistent_post()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/posts/999', [
            'title' => 'Nonexistent Post',
            'content' => 'Lorem ipsum',
            'tags' => ['Ashot'],
        ]);

        $response->assertStatus(404);
    }
}
