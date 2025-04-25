<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $append = Array(
        'is_deploy_status'
    );

    public function getIsDeployStatusAttribute() {
        return $this->isDeploy();
    }

    public function isDeploy() {

        if($this->is_deploy) return 'Yes';
        return 'No';

    }
}
