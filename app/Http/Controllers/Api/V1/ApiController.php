<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;

class ApiController extends Controller
{
    use ApiResponses;
    protected $policyClass;

    public function include(string $relationship): bool
    {
        $param = request()->get('include');

        if (!isset($param)) {
            return false;
        }
        $includedValues = explode(',', strtolower($param));
        return in_array(strtolower($relationship), $includedValues);
    }

    public function isAble($ability, $targetModel)
    {
        try {
            $this->authorize($ability, [$targetModel, $this->policyClass]);
            return true;
        } catch (AuthorizationException $ex) {
            return false;
        }
    }
}
