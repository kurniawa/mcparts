<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInstance extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public $timestamps = false;

    static function list_of_user_instances() {
        return [
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'instance_type'=>'bank',
                'instance_name'=>'BCA',
                'branch'=>'MC-Parts',
                'account_number'=>'1673008511',
                'timerange'=>'triwulan',
            ],[
                'user_id'=>7,
                'username'=>'Albert21',
                'instance_type'=>'bank',
                'instance_name'=>'Danamon',
                'branch'=>'MC-Parts',
                'account_number'=>'3602383121',
                'timerange'=>'triwulan',
            ],[
                'user_id'=>7,
                'username'=>'Albert21',
                'instance_type'=>'bank',
                'instance_name'=>'Danamon',
                'branch'=>'Demardi',
                'account_number'=>'93914307',
                'timerange'=>'triwulan',
            ],[
                'user_id'=>7,
                'username'=>'Albert21',
                'instance_type'=>'safe',
                'instance_name'=>'storage',
                'branch'=>'Albert',
                'account_number'=>null,
                'timerange'=>'triwulan',
            ],[
                'user_id'=>2,
                'username'=>'kuruniawa',
                'instance_type'=>'bank',
                'instance_name'=>'BCA',
                'branch'=>'Demardi',
                'account_number'=>'1670957931',
                'timerange'=>'triwulan',
            ],[
                'user_id'=>2,
                'username'=>'kuruniawa',
                'instance_type'=>'bank',
                'instance_name'=>'BRI',
                'branch'=>'Demardi',
                'account_number'=>'115101015149506',
                'timerange'=>'triwulan',
            ],[
                'user_id'=>2,
                'username'=>'kuruniawa',
                'instance_type'=>'safe',
                'instance_name'=>'storage',
                'branch'=>'Akhun',
                'account_number'=>null,
                'timerange'=>'triwulan',
            ],[
                'user_id'=>6,
                'username'=>'Dian',
                'instance_type'=>'safe',
                'instance_name'=>'storage',
                'branch'=>'Dian',
                'account_number'=>null,
                'timerange'=>'triwulan',
            ],[
                'user_id'=>7,
                'username'=>'Albert21',
                'instance_type'=>'bank',
                'instance_name'=>'BG',
                'branch'=>'BG',
                'account_number'=>null,
                'timerange'=>'triwulan',
            ],
        ];
    }
}
