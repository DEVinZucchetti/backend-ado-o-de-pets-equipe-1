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
        Schema::create('adoptions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cpf');
            $table->string('email');
            $table->string('contact', 20);
            $table->text('observations');
            $table->enum('status', ['PENDENTE', 'NEGADO','APROVADO']);
            $table->text('observation');

            $table->unsignedBigInteger('pets_id');
            $table->foreign('pets_id')->references('id')->on('pets');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adoptions');
    }
};
