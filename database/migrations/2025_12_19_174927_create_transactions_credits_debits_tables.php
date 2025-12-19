<?php

use App\Enums\TransactionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payer_user_id');
            $table->unsignedBigInteger('payee_user_id');
            $table->enum('type', TransactionType::values());
            $table->timestamps();

            $table->foreign('payer_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('payee_user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index('payer_user_id');
            $table->index('payee_user_id');
        });

        Schema::create('credits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->integer('original_amount');
            $table->timestamps();

            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');

            $table->index('transaction_id');
        });

        Schema::create('debits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('credit_id');
            $table->integer('amount');
            $table->timestamps();

            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('credit_id')->references('id')->on('credits')->onDelete('cascade');

            $table->unique(['transaction_id', 'credit_id']);
            $table->index('credit_id');
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debits');
        Schema::dropIfExists('credits');
        Schema::dropIfExists('transactions');
    }
};
