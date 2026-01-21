<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;

use Illuminate\Http\Request;

use App\Models\User;
use Carbon\Carbon;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;



class PostController extends Controller
{

        public function index(Request $request)
    {  

        $length = $request->input('length', 50);
        $page   = $request->input('page', 1);
        $offset = ($page - 1) * $length;

        $baseQuery = Post::query();

        if($request->has('type') && $request->type != ''){
            $baseQuery->where('posts.type',$request->type);
        }

        // ✅ Clone the query before using count()
        $count = (clone $baseQuery)->count();
        $data = $baseQuery->select([
                    '*'                       
            ])
            ->orderByDesc('id')
            ->skip($offset)
            ->take($length)
            ->get()
            ->map(function($item){
            
                return $item;
            });

        return response()->json([
            'total' => $count,
            'page' => $page,
            'offset' => $offset,
            'last_page' => ceil($count / $length),
            'data' => $data,
        ]);
        
    }

   

}