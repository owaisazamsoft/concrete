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

        $baseQuery = User::query();
        
        if($request->has('group') && $request->group != ''){
            $baseQuery->where('group',$request->group);
        }

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

        $data = $request->data ?? [];
        // $data['phone'] = $request->phone;
        // $data['group'] = $request->group;
        // $data['role'] = $request->role;
        $user->data = $data;

        $user->save();
   
        return response()->json([
            'message' => "Record Created Successfuly",
            'data' => $user,
        ],200);


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
        $user->data = $request->data ?? [];

        // if ($request->file('image')) {
            
        //     // Remove existing thumbnail if it exists
        //     if ($user->image && file_exists(public_path('uploads/' . $user->image))) {
        //         unlink(public_path('uploads/' . $user->image));
        //     }

        //     $fileName = time() . '__ff__' . $request->file('image')->getClientOriginalName();
        //     $filePath = public_path('uploads/');
        //     $request->file('image')->move($filePath, $fileName);
        //     $user->image = $fileName;
        // }

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

        // if(Payment::where('user_id',$id)->first()){
        //     return response()->json(['message' => 'Cannot Delete Record it Used In Payments'],400);
        // }

        // if(SaleInvoice::where('user_id',$id)->first()){
        //     return response()->json(['message' => 'Cannot Delete Record it Used In Invoice'],400);
        // }
        
        $user->delete();

        return response()->json([
            'message' => 'Record Deleted',
        ],200);

    }




}


