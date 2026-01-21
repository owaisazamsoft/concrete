<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\SaleInvoice;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Container\Attributes\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

        public function index(Request $request)
    {  
        $length = $request->input('length', 50);
        $page   = $request->input('page', 1);
        $offset = ($page - 1) * $length;

        $baseQuery = User::where('name','!=','admin');
        
     

        if($request->has('search') && $request->search != ''){
            $search = $request->search;
            $baseQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('id', 'like', "%{$search}%");
            });
        }

            // ✅ Clone the query before using count()
            $count = (clone $baseQuery)->count();
            $data = $baseQuery->select([
                        '*'                       
                ])
                ->skip($offset)
                ->take($length)
                ->get();

            return response()->json([
                'total' => $count,
                'page' => $page,
                'offset' => $offset,
                'last_page' => ceil($count / $length),
                'data' => $data,
            ]);
    }



       public function store(Request $request)
    {
        
        $user = new User();
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            ],
        );

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }
    
        $user->name = $request->name;
        $user->password = '';
        $user->email = 'email'.rand(100, 999) .'@example.com';

       
        $data = [];
        $data['phone'] = $request->phone;
        $data['group_id'] = $request->group_id;
        $data['department_id'] = $request->department_id;
        $data['nic'] = $request->nic;
        $data['gender'] = $request->gender;
        $data['dob'] = $request->dob;
        $data['address'] = $request->address;
        $user->data =  $data;

        $user->save();
   
        return response()->json([
            'message' => "Record Created Successfuly",
            'data' => $user,
        ],200);


    }

  

        public function show(Request $request,$id)
    {
        
        $user = User::find($id);
        if(!$user){
            return response()->json(['message' => 'Record Not Found'],400);
        }

        return response()->json([
            'message' => '',
            'data' => $user,
        ]);

    }


      public function update(Request $request,$id)
    {
        
        $user = User::find($id);

        if(!$user){
            return response()->json(['message' => 'Record Not Found'],400);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            ],
        );

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        $user->name = $request->name;
        $data = [];
        $data['phone'] = $request->phone;
        $data['group_id'] = $request->group_id;
        $data['department_id'] = $request->department_id;
        $data['nic'] = $request->nic;
        $data['gender'] = $request->gender;
        $data['dob'] = $request->dob;
        $data['address'] = $request->address;
        $user->data =  $data;

        $user->save();

        return response()->json([
            'message' => "Record Updated Successfuly",
            'data' => $user,
        ],200);

    }


    
        public function destroy(Request $request,$id)
    {

        $user = User::find($id);
        if(!$user){
            return response()->json(['message' => 'Record Not Found'],400);
        }

        $user->delete();

        return response()->json([
            'message' => 'Record Deleted',
        ],200);

    }

}
