<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('veiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->string('marca');
            $table->string('modelo');
            $table->unsignedSmallInteger('ano')->nullable();
            $table->string('cor')->nullable();
            $table->string('placa');
            $table->string('chassi')->nullable();
            $table->string('combustivel')->nullable();
            $table->string('cambio')->nullable();
            $table->unsignedInteger('km')->default(0);
            $table->string('imagem')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('veiculos');
    }
};
