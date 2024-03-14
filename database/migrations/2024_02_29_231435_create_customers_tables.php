<?php

use App\Models\Customer;
use App\Models\Service;
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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cpf')->nullable();
            $table->string('gender');
            $table->date('birthdate');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('customer_guardians', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cpf');
            $table->char('gender');
            $table->foreignIdFor(Customer::class)->index()->constrained()->restrictOnDelete();
            $table->date('birthdate');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('customer_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Customer::class)->index()->constrained()->restrictOnDelete();
            $table->foreignIdFor(Service::class)->constrained()->restrictOnDelete();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
        Schema::dropIfExists('customer_guardians');
        Schema::dropIfExists('customer_discounts');
    }
};
