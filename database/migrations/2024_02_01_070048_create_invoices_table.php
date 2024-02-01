<?php



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->decimal('total_before_discount', 10, 2);
            $table->decimal('total_after_discount', 10, 2);
            $table->string('status')->default('pending'); // 'pending', 'paid', 'cancelled', etc.
            $table->string('payment_type')->nullable(); // 'credit_card', 'cash', 'bank_transfer', etc.
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
