<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence();

        return [
            'event_id' => Event::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::random(5),
            'file_path' => 'documents/'.Str::random(10).'.pdf',
            'original_filename' => 'test-file.pdf',
            'is_active' => true,
        ];
    }
}
