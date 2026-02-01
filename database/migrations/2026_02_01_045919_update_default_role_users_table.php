<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // kalau kolom role sudah ada, kita update nilai yang lama jika perlu
        if (Schema::hasColumn('users', 'role')) {
            // contoh: ubah admin lama jadi superadmin
            DB::table('users')->where('role', 'admin')->update(['role' => 'superadmin']);

            // kalau ada bendahara/unit, biarkan atau mapping sesuai kebutuhan
        }
    }

    public function down(): void
    {
        // optional: rollback mapping (tidak wajib)
    }
};
