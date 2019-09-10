<?php
namespace App\Http\Controllers\Api\v1;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\User;
use Illuminate\Http\Request;

// Add this line
use Unlu\Laravel\Api\QueryBuilder;

class UserController extends Controller {
    public function index(Request $request)
    {
        $queryBuilder = new QueryBuilder(new User, $request);

        return response()->json([
            'data' => $queryBuilder->build()->get(),
        ]);
    }
}
