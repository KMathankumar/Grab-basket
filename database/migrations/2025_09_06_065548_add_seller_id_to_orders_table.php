<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::table('orders', function (Blueprint $table) {
        $table->foreignId('product_id')->nullable()->after('id')->constrained()->onDelete('set null');
        $table->foreignId('seller_id')->nullable()->after('product_id')->constrained('sellers')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropForeign(['product_id']);
        $table->dropForeign(['seller_id']);
        $table->dropColumn(['product_id', 'seller_id']);
    });
}
};
