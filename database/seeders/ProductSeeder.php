<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Consultoria',
                'price' => 150.00,
            ],
            [
                'name' => 'Desenvolvimento Web',
                'price' => 2000.00,
            ],
            [
                'name' => 'Suporte Mensal',
                'price' => 300.00,
            ],
            [
                'name' => 'Licença de Software',
                'price' => 500.00,
            ],
            [
                'name' => 'Formação',
                'price' => 750.00,
            ],
            [
                'name' => 'Design UI/UX',
                'price' => 1200.00,
            ],
            [
                'name' => 'Marketing Digital',
                'price' => 800.00,
            ],
            [
                'name' => 'SEO',
                'price' => 400.00,
            ],
            [
                'name' => 'Manutenção',
                'price' => 100.00,
            ],
            [
                'name' => 'Hosting',
                'price' => 25.00,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('Produtos criados com sucesso!');
    }
}
