<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sale_invoices', function (Blueprint $table) {
            $table->string('prefix')->nullable();
        });

        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->string('prefix')->nullable();
        });

        Schema::table('sale_orders', function (Blueprint $table) {
            $table->string('prefix')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
