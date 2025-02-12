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
        Schema::table('invoices', function (Blueprint $table) {
            $table->text('customer')->nullable()->after('number');
            $table->date('invoice_date')->nullable()->after('customer');
            $table->date('payment_date')->nullable()->after('invoice_date');
            $table->date('service_date')->nullable()->after('payment_date');
            $table->string('payment_method')->nullable()->after('payment_date');
            $table->string('vat')->nullable()->after('payment_method');
            $table->integer('total')->nullable()->after('vat');
            $table->jsonb('items')->nullable()->after('total');
            $table->string('email')->nullable()->after('items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('customer');
            $table->dropColumn('email');
            $table->dropColumn('invoice_date');
            $table->dropColumn('payment_date');
            $table->dropColumn('service_date');
            $table->dropColumn('payment_method');
            $table->dropColumn('vat');
            $table->dropColumn('items');
            $table->dropColumn('total');
        });
    }
};
