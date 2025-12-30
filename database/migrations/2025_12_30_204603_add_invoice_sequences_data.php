<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
         DB::table('invoice_sequences')->insert([
            'type' => 'sale_invoice',
            'last_number' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

         DB::table('invoice_sequences')->insert([
            'type' => 'delivery_note',
            'last_number' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('invoice_sequences')->insert([
            'type' => 'sale_order',
            'last_number' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
