<?php

if (!function_exists('getCurrencyName')) {
    function getCurrencyName($code)
    {
        if (empty($code)) {
            return 'غير محدد';
        }
        
        $currencies = [
            'USD' => 'دولار أمريكي',
            'EUR' => 'يورو',
            'GBP' => 'جنيه استرليني',
            'ILS' => 'شيكل إسرائيلي',
            'JOD' => 'دينار أردني',
            'TRY' => 'ليرة تركية',
        ];

        return $currencies[$code] ?? $code;
    }
}

if (!function_exists('getVoucherRouteName')) {
    function getVoucherRouteName($modelClass)
    {
        $baseName = strtolower(class_basename($modelClass));
        if ($baseName === 'paymentvoucher') {
            return 'payment-vouchers';
        }
        if ($baseName === 'expensevoucher') {
            return 'expense-vouchers';
        }
        return Str::plural($baseName);
    }
} 