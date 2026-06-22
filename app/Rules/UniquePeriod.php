<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UniquePeriod implements ValidationRule
{
    protected string $table;
    protected $year;
    protected $ignoreId;
    protected ?string $additionalColumn;
    protected $additionalValue;

    public function __construct(
        string $table,
        $year,
        $ignoreId = null,
        ?string $additionalColumn = null,
        $additionalValue = null
    ) {
        $this->table = $table;
        $this->year = $year;
        $this->ignoreId = $ignoreId;
        $this->additionalColumn = $additionalColumn;
        $this->additionalValue = $additionalValue;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Gunakan pencegahan dini jika variabel utama yang dibutuhkan kosong
        if (blank($this->year)) {
            return;
        }

        $query = DB::table($this->table)
            ->where('month', $value)
            ->where('year', $this->year);

        // Industri Pratika: Gunakan fungsi bawaan Laravel `filled()` atau `is_not_null` untuk keamanan tipe data
        if (!is_null($this->additionalColumn) && !is_null($this->additionalValue)) {
            $query->where($this->additionalColumn, $this->additionalValue);
        }

        if (!is_null($this->ignoreId)) {
            $query->where('id', '!=', $this->ignoreId);
        }

        if ($query->exists()) {
            $fail('Kombinasi periode bulan dan tahun tersebut sudah terdaftar.');
        }
    }
}