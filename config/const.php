<?php

return [
  'status' => [
    -1 => [
      'name' => 'iptal',
      'icon' => 'x',
      'theme' => 'warning',
    ],
    0 => [
      'name' => 'beklemede',
      'icon' => 'clock',
      'theme' => 'info',
    ],
    1 => [
      'name' => 'hazırlanıyor',
      'icon' => 'gear',
      'theme' => 'info',
    ],
    2 => [
      'name' => 'tamamlandı',
      'icon' => 'check2-all',
      'theme' => 'info',
    ],
  ],
  'productType' => [
    'bracelets' => [
      'min' => 56,
      'max' => 74,
      'between' => 2
    ]
  ],
  'reportType' => [
    'product' =>  [
      'title' => 'Ürün',
    ],
    'customer' =>  [
      'title' => 'Müşteri',
    ],
  ],
  'redirect' => 'orders?status=0',
];
