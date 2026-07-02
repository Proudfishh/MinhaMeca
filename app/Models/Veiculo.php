<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Veiculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'cliente_id',
        'marca',
        'modelo',
        'ano',
        'cor',
        'placa',
        'chassi',
        'combustivel',
        'cambio',
        'km',
        'imagem',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
