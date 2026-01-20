<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserProfileResource;
use App\Models\Category;
use App\Models\Department;
use App\Models\ExpenseCategory;
use App\Models\Group;
use App\Models\Lot;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Request;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Container\Attributes\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Faker\Factory as Faker;

class DummyController extends Controller
{

    public $faker;

    public function __construct() {
        $this->faker = Faker::create();
    }
    
       public function start(Request $request)
    {  

        if(Group::count() == 0){
            Group::create(['title' => 'Staff']);
            Group::create(['title' => 'Operator']);
            Group::create(['title' => 'Laiber']);
        }

        if(Department::count() == 0){
            Department::create(['title' => 'Zubair']);
            Department::create(['title' => 'Imtiyaz']);
            Department::create(['title' => 'Shafat']);
        }

        if(User::count() < 2){
            $this->user(100);
        }

        if(Category::count() == 0){
            $cat = ['Shirts','T-Shirts','Jeans','Dresses','Jackets','Sweaters','Activewear','Underwear','Shoes','Accessories'];
            foreach ($cat as $value) {
                Category::create(['title' => $value]);
            }
        }

        if(Product::count() == 0){
            $this->product(100);
        }

        if(Lot::count() == 0){
            $this->lot(100);
        }

    }


        public function user($count)
    {  

        $groups = Group::pluck('id')->toArray();
        $derpartments = Department::pluck('id')->toArray();

        foreach (range(1, $count) as $key => $value) {

            $gender = $this->faker->randomElement(['male', 'female']);
            User::create([
                'name' => $this->faker->name($gender),
                'email' => $this->faker->unique()->safeEmail(),
                'password' => '',
                'data' => [
                    'dob' => $this->faker->date('Y-m-d'),
                    'gender' => $gender,
                    'address' => $this->faker->address(),
                    'group' => $this->faker->randomElement($groups),
                    'department_id' => $this->faker->randomElement($derpartments),
                ],
            ]);

        }

    }


        public function product($count)
    {  
        $categories = Category::pluck('id')->toArray();
        foreach (range(1, $count) as $value) {
            $uniqueCode = strtoupper($this->faker->unique()->lexify('????????')); 
            Product::create([
                'title' => ucwords(fake()->words(3, true)),
                'code' => $uniqueCode,
                'price' => $this->faker->numberBetween(15, 200),
                'category_id' => $this->faker->randomElement($categories),
            ]);
        }

    }


        public function lot($count)
    {  

        $fabricTypes = ["Cotton", "Polyester", "Silk"];
        $colors = ["Red", "Blue", "Black", "White", "Green"];
        $sizes = ["S","M","L","XL","XXL"];
        $users = User::pluck('id')->toArray();
  
        foreach (range(1, $count) as $value) {
            Lot::create([
                'title' => 'LOT-'.$value,
                'date' => Carbon::parse($this->faker->date('Y-m-d')),
                'data' => [
                    'color' => $this->faker->randomElement($colors),
                    'size' => $this->faker->randomElement($sizes),
                    'type' => $this->faker->randomElement($fabricTypes),
                    'quantity' => $this->faker->numberBetween(1, 500),
                ],
                'user_id' => $this->faker->randomElement($users), 
            ]);
        }

    }

    


  
}