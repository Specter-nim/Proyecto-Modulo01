<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PredeterminedPrice extends Model
{
    use SoftDeletes;

    protected $table = 'predetermined_prices'; // Opcional si el nombre del modelo no sigue la convención

    // Campos que se pueden asignar de forma masiva
    protected $fillable = ['name', 'price'];

    // Para que Laravel maneje automáticamente las fechas (created_at, updated_at, deleted_at)
    protected $dates = ['deleted_at'];

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'predetermined_price_id');
    }
}
