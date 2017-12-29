<?php
namespace App\Models\/*{$o->module()*/Module/*}*/;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class /*{$o->name()*/Name/*}*/ extends Model
{
    use SoftDeletes, Notifiable;
    protected $table = /*{php_encode($o->table())*/'table_name'/*}*/;
    protected $fillable = /*{$o->fillable()*/[]/*}*/;
    protected $attributes = /*{$o->attributes()*/[]/*}*/;
    protected $casts = /*{$o->casts()*/[]/*}*/;
    protected $events = /*{$o->eventsList()*/[]/*}*/;
    /*{$o->relationships($node, $this)*/
    public function /*{$e->name()*/relationship/*}*/()
    {
        return $this->/*{$e->type()*/belongsTo/*}*/(
            /*{$e->related()*/\App\Models\UserAdministration\Tarea/*}*/::class,
            /*{json_encode($e->foreignKey())*/'id'/*}*/,
            /*{json_encode($e->localKey())*/'relationship_id'/*}*/
        );
    }
    /*}*/
    /*{$o->methods($node, $this)*/
    public /*{$e->method()*/function method()
    {
    }/*}*/
    /*}*/
}
