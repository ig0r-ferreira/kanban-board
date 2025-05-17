<?php

use Illuminate\Support\Facades\DB;
use App\Models\Sequence;

if (!function_exists('nextSequence')) {
    function nextSequence(string $name): int
    {
        return DB::transaction(function () use ($name) {
            $sequence = Sequence::lockForUpdate()->firstOrCreate(
                ['name' => $name],
                ['value' => 0]
            );

            $sequence->increment('value');
            return $sequence->value;
        });
    }
}
