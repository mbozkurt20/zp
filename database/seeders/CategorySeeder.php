<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $musicCategories = [
            [
                'name' => "90'lar ",
                'slug' => '90',
                'description' => '90 lar vazgeçilmezlerimiz',
                'image' => '90.jpg',
            ],
            [
                'name' => 'Pop',
                'slug' => 'pop',
                'description' => 'Günümüzün en popüler müzik türlerinden biri.',
                'image' => 'pop.jpg',
            ],
            [
                'name' => 'Rock',
                'slug' => 'rock',
                'description' => 'Elektrik gitarlar ve güçlü vokallerle öne çıkan bir tür.',
                'image' => 'rock.jpg',
            ],
            [
                'name' => 'Jazz',
                'slug' => 'jazz',
                'description' => 'Doğaçlama ve zengin armoniler içeren müzik türü.',
                'image' => 'jazz.jpg',
            ],
            [
                'name' => 'Klasik',
                'slug' => 'klasik',
                'description' => 'Batı klasik müziğini kapsayan geniş bir dönem müziği.',
                'image' => 'klasik.jpg',
            ],
            [
                'name' => 'Hip-Hop',
                'slug' => 'hip-hop',
                'description' => 'Ritimli sözler ve beat tabanlı yapısı ile bilinir.',
                'image' => 'hiphop.jpg',
            ],
            [
                'name' => 'Elektronik',
                'slug' => 'elektronik',
                'description' => 'Synth ve dijital seslerle oluşturulan modern müzik türü.',
                'image' => 'elektronik.jpg',
            ],
            [
                'name' => 'Blues',
                'slug' => 'blues',
                'description' => 'Duygusal sözleri ve gitar ağırlıklı yapısıyla öne çıkar.',
                'image' => 'blues.jpg',
            ],
            [
                'name' => 'Reggae',
                'slug' => 'reggae',
                'description' => 'Jamaika kökenli, ritmik ve barış temalı müzik türü.',
                'image' => 'reggae.jpg',
            ],
            [
                'name' => 'Türk Halk Müziği',
                'slug' => 'turk-halk-muzigi',
                'description' => 'Anadolu’nun geleneksel ezgilerini yansıtır.',
                'image' => 'turk-halk.jpg',
            ],
            [
                'name' => 'Türk Sanat Müziği',
                'slug' => 'turk-sanat-muzigi',
                'description' => 'Klasik Türk müziğini temsil eden zarif bir tür.',
                'image' => 'turk-sanat.jpg',
            ],
        ];

        foreach ($musicCategories as $category) {
            Category::create($category);
        }
    }
}
