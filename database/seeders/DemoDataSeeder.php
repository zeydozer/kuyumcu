<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DemoDataSeeder extends Seeder
{
    protected Generator $faker;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->faker = FakerFactory::create('tr_TR');

        $this->seedPersonnel();
        $this->seedCustomers();
        $this->seedCategories();
        $this->seedProducts();
        $this->seedOrders();
    }

    protected function seedPersonnel()
    {
        $password = md5(env('INIT_ADMIN_PASSWORD', 'password'));

        $profiles = [
            [
                'role' => 0,
                'name' => env('INIT_ADMIN_NAME', 'Serkan Demir'),
                'mail' => env('INIT_ADMIN_EMAIL', 'admin@example.com'),
                'pass' => $password,
                'phone' => '05321234567',
                'address' => 'Kuyumcukent Ticaret Merkezi Bahcelievler Istanbul',
                'admin' => 1,
                'created_at' => now()->subMonths(24),
                'updated_at' => now()->subMonths(2),
            ],
            [
                'role' => 0,
                'name' => 'Murat Demir',
                'mail' => 'murat.demir@senbilezik.com',
                'pass' => md5('password'),
                'phone' => '05352334455',
                'address' => 'Kuyumcukent Atolye Bloklari Bahcelievler Istanbul',
                'admin' => 0,
                'created_at' => now()->subMonths(18),
                'updated_at' => now()->subWeeks(3),
            ],
        ];

        foreach ($profiles as $profile) {
            $this->syncFixedUser($profile);
        }
    }

    protected function seedCustomers()
    {
        $showcaseCustomers = [
            [
                'role' => 1,
                'name' => env('INIT_CUSTOMER_NAME', 'Zeynep Kaya'),
                'mail' => env('INIT_CUSTOMER_EMAIL', 'customer@example.com'),
                'pass' => md5(env('INIT_CUSTOMER_PASSWORD', 'password')),
                'phone' => '05301112233',
                'address' => 'Ataturk Mah. Ihlamur Sok. No:12 D:4 Bornova Izmir',
                'admin' => 0,
                'created_at' => now()->subMonths(16),
                'updated_at' => now()->subDays(12),
            ],
            [
                'role' => 1,
                'name' => 'Esra Kaya',
                'mail' => 'esra.kaya@gmail.com',
                'pass' => md5('password'),
                'phone' => '05332221100',
                'address' => 'Cumhuriyet Mah. Menekse Sok. No:8 D:2 Sisli Istanbul',
                'admin' => 0,
                'created_at' => now()->subMonths(14),
                'updated_at' => now()->subDays(5),
            ],
            [
                'role' => 1,
                'name' => 'Merve Yildiz',
                'mail' => 'merve.yildiz@outlook.com',
                'pass' => md5('password'),
                'phone' => '05374445566',
                'address' => 'Barbaros Mah. Cinar Sok. No:31 D:7 Kadikoy Istanbul',
                'admin' => 0,
                'created_at' => now()->subMonths(13),
                'updated_at' => now()->subDays(2),
            ],
            [
                'role' => 1,
                'name' => 'Fatma Sahin',
                'mail' => 'fatma.sahin@hotmail.com',
                'pass' => md5('password'),
                'phone' => '05427778899',
                'address' => 'Yenimahalle Mah. Lale Cad. No:4 D:1 Muratpasa Antalya',
                'admin' => 0,
                'created_at' => now()->subMonths(11),
                'updated_at' => now()->subWeeks(1),
            ],
            [
                'role' => 1,
                'name' => 'Ayse Koc',
                'mail' => 'ayse.koc@gmail.com',
                'pass' => md5('password'),
                'phone' => '05386667788',
                'address' => 'Mimar Sinan Mah. Park Sok. No:19 D:5 Nilufer Bursa',
                'admin' => 0,
                'created_at' => now()->subMonths(10),
                'updated_at' => now()->subWeeks(2),
            ],
            [
                'role' => 1,
                'name' => 'Seda Arslan',
                'mail' => 'seda.arslan@yahoo.com',
                'pass' => md5('password'),
                'phone' => '05413334455',
                'address' => 'Selcuk Mah. Gul Cad. No:27 D:6 Cankaya Ankara',
                'admin' => 0,
                'created_at' => now()->subMonths(9),
                'updated_at' => now()->subDays(8),
            ],
            [
                'role' => 1,
                'name' => 'Elif Cakir',
                'mail' => 'elif.cakir@gmail.com',
                'pass' => md5('password'),
                'phone' => '05395556677',
                'address' => 'Inonu Mah. Deniz Sok. No:3 D:9 Konak Izmir',
                'admin' => 0,
                'created_at' => now()->subMonths(8),
                'updated_at' => now()->subDays(3),
            ],
            [
                'role' => 1,
                'name' => 'Busra Celik',
                'mail' => 'busra.celik@outlook.com',
                'pass' => md5('password'),
                'phone' => '05445550011',
                'address' => 'Gazi Mah. Papatya Sok. No:41 D:11 Sehitkamil Gaziantep',
                'admin' => 0,
                'created_at' => now()->subMonths(7),
                'updated_at' => now()->subDays(1),
            ],
        ];

        foreach ($showcaseCustomers as $customer) {
            $this->syncFixedUser($customer);
        }
    }

    protected function seedCategories()
    {
        $definitions = [
            ['name' => 'Bilezik', 'root' => null],
            ['name' => 'Ajda Bilezik', 'root' => 'Bilezik'],
            ['name' => 'Kelepce Bilezik', 'root' => 'Bilezik'],
            ['name' => 'Kaburga Bilezik', 'root' => 'Bilezik'],
            ['name' => 'Burma Bilezik', 'root' => 'Bilezik'],
            ['name' => 'Klasik Bilezik', 'root' => 'Bilezik'],
            ['name' => 'Fantezi Bilezik', 'root' => 'Bilezik'],
            ['name' => 'Cocuk Bilezik', 'root' => 'Bilezik'],
            ['name' => 'Yatirim Serisi', 'root' => null],
            ['name' => 'Ozel Siparis', 'root' => null],
        ];

        foreach ($definitions as $index => $definition) {
            $category = Category::withTrashed()->where('name', $definition['name'])->first();

            if ($category) {
                if ($category->trashed()) {
                    $category->restore();
                }
                continue;
            }

            $rootId = null;
            if ($definition['root']) {
                $rootId = Category::query()->where('name', $definition['root'])->value('id');
            }

            Category::forceCreate([
                'name' => $definition['name'],
                'root_id' => $rootId,
                'created_at' => now()->subMonths(12 - min($index, 10)),
                'updated_at' => now()->subMonths(2),
            ]);
        }
    }

    protected function seedProducts()
    {
        $targetProductCount = 100;
        $currentProductCount = Product::query()->count();
        $missingProducts = max(0, $targetProductCount - $currentProductCount);

        if ($missingProducts === 0) {
            return;
        }

        $categoryProfiles = [
            'Ajda Bilezik' => ['min_width' => 4.5, 'max_width' => 8.5, 'min_weight' => 7.5, 'max_weight' => 16.5],
            'Kelepce Bilezik' => ['min_width' => 5.0, 'max_width' => 11.0, 'min_weight' => 9.0, 'max_weight' => 23.0],
            'Kaburga Bilezik' => ['min_width' => 6.0, 'max_width' => 12.0, 'min_weight' => 12.0, 'max_weight' => 28.0],
            'Burma Bilezik' => ['min_width' => 3.5, 'max_width' => 7.5, 'min_weight' => 6.0, 'max_weight' => 14.0],
            'Klasik Bilezik' => ['min_width' => 4.0, 'max_width' => 9.0, 'min_weight' => 7.0, 'max_weight' => 18.0],
            'Fantezi Bilezik' => ['min_width' => 5.5, 'max_width' => 10.5, 'min_weight' => 10.0, 'max_weight' => 22.0],
            'Cocuk Bilezik' => ['min_width' => 2.5, 'max_width' => 5.0, 'min_weight' => 3.5, 'max_weight' => 8.5],
            'Yatirim Serisi' => ['min_width' => 7.0, 'max_width' => 12.5, 'min_weight' => 16.0, 'max_weight' => 32.0],
            'Ozel Siparis' => ['min_width' => 4.5, 'max_width' => 12.0, 'min_weight' => 8.0, 'max_weight' => 26.0],
            'Bilezik' => ['min_width' => 4.0, 'max_width' => 8.0, 'min_weight' => 6.0, 'max_weight' => 16.0],
        ];

        $series = ['Parlak Seri', 'Mat Dokulu', 'Bombeli', 'Ince Iscilik', 'Oval Kesim', 'Duz Form', 'Klasik Seri', 'Gelin Seti Uyumlu'];
        $collections = ['22 Ayar', '14 Ayar'];
        $productNames = Product::withTrashed()->pluck('name')->all();
        $nameMap = array_fill_keys($productNames, true);
        $categories = Category::query()->whereNotIn('name', ['Bilezik'])->get()->values();
        $rows = [];

        for ($i = 1; $i <= $missingProducts; $i++) {
            $category = $categories->random();
            $profile = $categoryProfiles[$category->name] ?? $categoryProfiles['Bilezik'];
            $width = $this->randomDecimal($profile['min_width'], $profile['max_width']);
            $weight = $this->randomDecimal($profile['min_weight'], $profile['max_weight']);
            $between = $this->randomDecimal(0.15, 1.80);
            $name = $this->generateUniqueProductName($category->name, $width, $series, $collections, $nameMap);
            $createdAt = $this->faker->dateTimeBetween('-18 months', '-7 days');
            $updatedAt = $this->faker->dateTimeBetween($createdAt, 'now');

            $rows[] = [
                'name' => $name,
                'type' => 'bracelets',
                'photo' => null,
                'ctg_id' => $category->id,
                'width' => $width,
                'weight' => $weight,
                'between' => $between,
                'empty' => $this->faker->boolean(25),
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
                'deleted_at' => null,
            ];
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('products')->insert($chunk);
        }
    }

    protected function seedOrders()
    {
        $targetOrderCount = 1000;
        $currentOrderCount = DB::table('orders')->count();
        $missingOrders = max(0, $targetOrderCount - $currentOrderCount);

        if ($missingOrders === 0) {
            return;
        }

        $customers = User::query()->where('role', 1)->get(['id', 'created_at']);
        $personnel = User::query()->where('role', 0)->get(['id']);
        $products = Product::query()->get([
            'id',
            'name',
            'type',
            'photo',
            'ctg_id',
            'width',
            'weight',
            'between',
            'empty',
        ]);

        if ($customers->isEmpty() || $personnel->isEmpty() || $products->isEmpty()) {
            return;
        }

        $orderNotes = [
            null,
            null,
            null,
            'Musteri teslim tarihini teyit etti.',
            'Hediye paketi talep edildi.',
            'Takim urunlerle uyumlu ton istendi.',
            'Atolye cikisinda son kontrol yapilacak.',
            'Musteri olcu bilgisini telefonda paylasti.',
            'Magaza teslimi olarak hazirlanacak.',
            'Kargo oncesi foto gonderilecek.',
        ];

        $lineNotes = [
            null,
            null,
            'Parlatma yapilacak.',
            'Ic yuzey kontrol edilecek.',
            'Olcu dagilimi musteri talebine gore ayarlandi.',
            'Vitrin urununden referans alindi.',
            'Ciftli kombin icin ayrildi.',
        ];

        DB::transaction(function () use (
            $customers,
            $personnel,
            $products,
            $missingOrders,
            $orderNotes,
            $lineNotes
        ) {
            for ($i = 0; $i < $missingOrders; $i++) {
                $customer = $customers->random();
                $staff = $personnel->random();
                $status = $this->weightedStatus();
                $createdAt = Carbon::instance($this->faker->dateTimeBetween(
                    Carbon::parse($customer->created_at)->copy()->addDays(1),
                    'now'
                ));
                $finishedAt = $this->generateFinishedAt($createdAt, $status);
                $lineCount = $this->faker->numberBetween(1, 4);
                $selectedProducts = $products->shuffle()->take($lineCount);
                $orderQuantity = 0;
                $orderWeight = 0;

                $orderId = DB::table('orders')->insertGetId([
                    'user_id' => $customer->id,
                    'auth_id' => $staff->id,
                    'note' => $this->faker->randomElement($orderNotes),
                    'quantity' => 0,
                    'weight' => 0,
                    'status' => $status,
                    'finished_at' => $finishedAt->toDateString(),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                    'deleted_at' => null,
                ]);

                foreach ($selectedProducts as $product) {
                    $heightMap = $this->generateHeightDistribution(
                        $this->faker->numberBetween(1, $product->empty ? 8 : 6)
                    );
                    $quantity = array_sum($heightMap);
                    $lineWeight = $this->lineWeightFromProduct((float) $product->weight);
                    $width = $this->lineWidthFromProduct((float) $product->width);
                    $weightTotal = round($lineWeight * $quantity, 2);
                    $lineCreatedAt = $createdAt->copy()->addMinutes($this->faker->numberBetween(1, 120));

                    $orderCartId = DB::table('order_carts')->insertGetId([
                        'order_id' => $orderId,
                        'user_id' => $customer->id,
                        'product_id' => $product->id,
                        'width' => $width,
                        'weight' => $lineWeight,
                        'photo' => null,
                        'note' => $this->faker->randomElement($lineNotes),
                        'quantity' => $quantity,
                        'weight_total' => $weightTotal,
                        'created_at' => $lineCreatedAt,
                        'updated_at' => $lineCreatedAt,
                        'deleted_at' => null,
                    ]);

                    DB::table('order_products')->insert([
                        'id' => $product->id,
                        'order_id' => $orderId,
                        'name' => $product->name,
                        'type' => $product->type,
                        'photo' => $product->photo,
                        'ctg_id' => $product->ctg_id,
                        'width' => $product->width,
                        'weight' => $product->weight,
                        'between' => $product->between,
                        'empty' => $product->empty,
                        'created_at' => $lineCreatedAt,
                        'updated_at' => $lineCreatedAt,
                        'deleted_at' => null,
                    ]);

                    DB::table('order_bracelets')->insert(array_merge(
                        [
                            'cart_id' => $orderCartId,
                            'created_at' => $lineCreatedAt,
                            'updated_at' => $lineCreatedAt,
                            'deleted_at' => null,
                        ],
                        $heightMap
                    ));

                    $orderQuantity += $quantity;
                    $orderWeight += $weightTotal;
                }

                DB::table('orders')
                    ->where('id', $orderId)
                    ->update([
                        'quantity' => $orderQuantity,
                        'weight' => round($orderWeight, 2),
                    ]);
            }
        });
    }

    protected function syncFixedUser(array $attributes)
    {
        $existing = User::withTrashed()->where('mail', $attributes['mail'])->first();

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }

            $existing->forceFill(array_merge([
                'token' => null,
                'deleted_at' => null,
            ], $attributes));
            $existing->save();

            return;
        }

        User::forceCreate(array_merge([
            'token' => null,
            'deleted_at' => null,
        ], $attributes));
    }

    protected function generateHeightDistribution(int $totalQuantity): array
    {
        $heights = [];
        for ($size = 56; $size <= 74; $size += 2) {
            $heights["height_$size"] = 0;
        }

        for ($i = 0; $i < $totalQuantity; $i++) {
            $size = $this->faker->randomElement([56, 58, 60, 62, 64, 66, 68, 70, 72, 74]);
            $heights["height_$size"]++;
        }

        return $heights;
    }

    protected function generateUniqueProductName(string $categoryName, float $width, array $series, array $collections, array &$nameMap): string
    {
        $baseCategory = str_replace(' Bilezik', '', $categoryName);

        do {
            $name = sprintf(
                '%s %s %.1fmm %s',
                $this->faker->randomElement($collections),
                $baseCategory,
                $width,
                $this->faker->randomElement($series)
            );
        } while (isset($nameMap[$name]));

        $nameMap[$name] = true;

        return $name;
    }

    protected function randomDecimal(float $min, float $max): float
    {
        return round($this->faker->randomFloat(2, $min, $max), 2);
    }

    protected function weightedStatus(): int
    {
        return $this->faker->randomElement([
            -1,
            0, 0, 0, 0,
            1, 1, 1,
            2, 2, 2, 2,
        ]);
    }

    protected function generateFinishedAt(Carbon $createdAt, int $status): Carbon
    {
        if ($status === 2) {
            $date = $createdAt->copy()->addDays($this->faker->numberBetween(1, 14));
            return $date->greaterThan(now()) ? now()->copy() : $date;
        }

        if ($status === 1) {
            return now()->copy()->addDays($this->faker->numberBetween(1, 10));
        }

        if ($status === 0) {
            return now()->copy()->addDays($this->faker->numberBetween(3, 21));
        }

        return $createdAt->copy()->addDays($this->faker->numberBetween(1, 10));
    }

    protected function lineWeightFromProduct(float $weight): float
    {
        return round(max(1, $weight + $this->faker->randomFloat(2, -0.35, 0.45)), 2);
    }

    protected function lineWidthFromProduct(float $width): float
    {
        return round(max(1, $width + $this->faker->randomFloat(2, -0.20, 0.20)), 2);
    }
}
