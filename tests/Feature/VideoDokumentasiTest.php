<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;

class VideoDokumentasiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_public_video_view_displays_uploaded_thumbnail()
    {
        Storage::fake('public');

        $video = Video::create([
            'judul' => 'Video Test Thumbnail',
            'url' => 'https://youtube.com/watch?v=12345678901',
            'deskripsi' => 'Testing video dengan custom thumbnail',
            'gambar' => 'video/test-thumbnail.jpg'
        ]);

        $response = $this->get(route('video.public'));

        $response->assertStatus(200);
        $response->assertSee('video/test-thumbnail.jpg');
    }

    public function test_admin_can_add_video()
    {
        $user = \App\Models\User::factory()->create(['role' => 'admin_master']);

        $response = $this->actingAs($user)->post(route('dokumentasi.video.store'), [
            'judul' => 'Video Baru',
            'url' => 'https://youtube.com/watch?v=test',
            'deskripsi' => 'Deskripsi',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('videos', [
            'judul' => 'Video Baru',
            'url' => 'https://youtube.com/watch?v=test',
        ]);
    }
}
