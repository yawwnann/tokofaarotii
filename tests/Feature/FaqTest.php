<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\Faq;
use App\Models\User;

class FaqTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_add_faq()
    {
        $user = User::factory()->create(['role' => 'admin_master']);
        
        $response = $this->actingAs($user)->post(route('faq.store'), [
            'pertanyaan' => 'Pertanyaan Test?',
            'jawaban'    => 'Jawaban Test',
        ]);
        
        $response->assertRedirect(route('faq.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('faqs', [
            'pertanyaan' => 'Pertanyaan Test?',
            'jawaban'    => 'Jawaban Test',
        ]);
    }

    public function test_admin_can_update_faq()
    {
        $user = User::factory()->create(['role' => 'admin_master']);
        $faq = Faq::create([
            'pertanyaan' => 'Lama',
            'jawaban'    => 'Lama',
        ]);
        
        $response = $this->actingAs($user)->put(route('faq.update', $faq->id), [
            'pertanyaan' => 'Baru',
            'jawaban'    => 'Baru',
        ]);
        
        $response->assertRedirect(route('faq.index'));
        $this->assertDatabaseHas('faqs', [
            'id' => $faq->id,
            'pertanyaan' => 'Baru',
        ]);
    }
}
