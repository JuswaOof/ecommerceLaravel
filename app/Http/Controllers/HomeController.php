<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Order;
use App\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $products = Product::all();
        $orders = Order::all();
        $permissions = Permission::all();
        $roles = Role::all();

        $availableProductCount = Product::where('isAvailable', 'Available')->count();
        $notAvailableProductCount = Product::where('isAvailable', 'Not Available')->count();

        return view('home', compact('products', 'orders', 'availableProductCount', 'notAvailableProductCount', 'permissions', 'roles'));
    }

    // add product method
    public function add_product(Request $request)
    {
        // return($request);
        $filename = null;

        if ($request->hasFile('imageUpload')) {
            $file = $request->file('imageUpload');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('images', $filename, 'public');
        }

        $validated = $request->validate([
            'productName' => ['required', 'string','max:255'],
            'productDescription' => ['required' , 'string' , 'max:255'],
            'price' => ['required' , 'numeric'],
            'imageUpload' => ['nullable' , 'image' , 'mimes:jpeg,png,jpg,gif'],
            'isFeatured' => ['required' , 'string' , 'max:255'],
            'category' => ['required' , 'string' , 'max:255'],
            'isAvailable' => ['required' , 'string' , 'max:255'],
        ]);

        Product::create([
            'productName' => $request->input('productName'),
            'productDescription' => $request->input('productDescription'),
            'isAvailable' => $request->input('isAvailable'),
            'price' => $request->input('price'),
            'isFeatured' => $request->input('isFeatured'),
            'category' => $request->input('category'),
            'imageUpload' => $filename,
        ]);
        return redirect()->back()->with('success', 'Product added successfully!');
    }

    // show product method
    public function show_product(Request $request)
    {
        $query = Product::query();

        if ($search = $request->input('search.value')) {
            $query->where('productName', 'like', "%{$search}%")
                ->orWhere('productDescription', 'like', "%{$search}%")
                ->orWhere('isAvailable', 'like', "%{$search}%")
                ->orWhere('price', 'like', "%{$search}%")
                ->orWhere('isFeatured', 'like', "%{$search}%");
        }

        if ($order = $request->input('order.0.column')) {
            $columns = ['productName', 'productDescription', 'imageUpload', 'isAvailable', 'price', 'isFeatured'];
            $direction = $request->input('order.0.dir', 'asc');
            $query->orderBy($columns[$order], $direction);
        }

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $total = $query->count();

        $products = $query->offset($start)->limit($length)->get();

        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $products,
        ]);
    }

    // edit product method
    public function edit_product(Request $request)
    {
        // return($request->input('editCategory'));
        $validatedData = $request->validate([
            'id' => ['required', 'exists:products,id'],
            'editproductName' => ['required', 'string', 'max:255'],
            'editProductDescription' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'editImageUpload' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif'],
            'editIsFeatured' => ['required', 'in:Featured,Not Featured'],
            'editIsAvailable' => ['required', 'in:Available,Not Available'],
            'editCategory' => ['required', 'in:Acoustic,Electric,Bass'],
        ]);
        $product = Product::findOrFail($request->input('id'));
        

        $product->productName = $request->input('editproductName');
        $product->productDescription = $request->input('editProductDescription');
        $product->price = $request->input('price');
        $product->isFeatured = $request->input('editIsFeatured');
        $product->isAvailable = $request->input('editIsAvailable');
        $product->category = $request->input('editCategory');

        if ($request->hasFile('editImageUpload')) {
            if ($product->imageUpload) {
                Storage::disk('public')->delete('images/' . $product->imageUpload);
            }
            $file = $request->file('editImageUpload');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('images', $filename, 'public');
            $product->imageUpload = $filename;
        }
        $product->save();
    }

    //delete product method
    public function delete_product(Request $request)
    {
        
        $ids = $request->input('ids');
        // dd($ids);
        // return($ids);

        if (!is_array($ids)) {
            return response()->json(['message' => 'Invalid data'], 400);
        }

        Product::whereIn('id', $ids)->delete();
    }

    // add order method NOTE: PUT VALIDATION
    public function add_order(Request $request)
    {
        $user = Auth::user();

        $order = new Order();

        $order->productId =  $request->id;
        $order->productName =  $request->productName;
        $order->userId =  $user->id;
        $order->customer =  $user->name;
        $order->status =  $request->status;
        $order->price =  $request->price;
        
        $order->save();
    }

    // show order x datatables
    public function show_order(Request $request)
    {
        $query = Order::query();

        if ($search = $request->input('search.value')) {
            $query->where('productName', 'like', "%{$search}%")
                ->orWhere('customer', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhere('price', 'like', "%{$search}%")
                ->orWhere('created_at', 'like', "%{$search}%");
        }

        if ($order = $request->input('order.0.column')) {
            $columns = ['productName', 'customer', 'created_at', 'status', 'price'];
            $direction = $request->input('order.0.dir', 'asc');
            $query->orderBy($columns[$order], $direction);
        }

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $total = $query->count();

        $products = $query->offset($start)->limit($length)->get();

        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $products,
        ]);
    }

    // send order data to highCharts specifically productName and rowcount
    public function order_data(Request $request)
    {
        $productCounts = Order::select('productName')->groupBy('productName')->selectRaw('count(*) as count')->get();

        return response()->json($productCounts);
    }

    // show roles
    public function show_users(Request $request)
    {
        $query = User::with('roles');
        // $query = User::query();
        // dd(json_encode($query));

        if ($search = $request->input('search.value')) {
            $query->where('productName', 'like', "%{$search}%")
                ->orWhere('customer', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhere('price', 'like', "%{$search}%")
                ->orWhere('created_at', 'like', "%{$search}%");
        }

        if ($order = $request->input('order.0.column')) {
            $columns = ['productName', 'customer', 'created_at', 'status', 'price'];
            $direction = $request->input('order.0.dir', 'asc');
            $query->orderBy($columns[$order], $direction);
        }

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $total = $query->count();

        $roles = $query->offset($start)->limit($length)->get();

        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $roles,
        ]);
    }
    //add user
    public function add_user(Request $request)
    {
        
        // return($request->input('status'));
        // return($request->role);

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'regex:/^09\d{9}$/', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'address' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:255'],
            'password' => [
                            'required',
                            'string',
                            'min:8',
                            'regex:/[a-z]/',      // must contain at least one lowercase letter
                            'regex:/[A-Z]/',      // must contain at least one uppercase letter
                            'regex:/[0-9]/',      // must contain at least one digit
                            'regex:/[@$!%*?&#]/', // must contain a special character
                            'confirmed'
                            ]
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'status' => $request->input('status'),
            'password' => Hash::make($request->input('password')),
        ]);
        
        // if($request->input('role') == 'chatSupport'){
        //     $user->attachRole('chatSupport');
        // }elseif($request->input('role') == 'administrator'){
        //     $user->attachRole('administrator');
        // }elseif($request->input('role') == 'superAdministrator'){
        //     $user->attachRole('superAdministrator');
        // }

        $user->attachRole($request->role);

    }
    // populate edit user form
    public function get_user(Request $request)
    {
        // return ($request->id);

        // $user = User::findOrFail($request->id);

        $user = User::with('roles')->findOrFail($request->id);

        return response()->json($user);
        // return($user);
        
    }
    //edit user
    public function edit_user(Request $request)
    {
        // return $request->input('id');
        // return $request->input('editStatus');
        $validatedData = $request->validate([
            'editName' => ['required', 'string', 'max:255'],
            'editPhone' => ['required', 'regex:/^09\d{9}$/'],
            'editEmail' => ['required', 'string', 'email', 'max:255'],
            'editAddress' => ['required', 'string', 'max:255'],
            'editStatus' => ['required', 'string', 'max:255'],
        ]);
        $user = User::findOrFail($request->input('id'));

        $user->roles()->detach();

        $user->attachRole($request->input('editRole'));

        $user->name = $request->input('editName');
        $user->phone = $request->input('editPhone');
        $user->email = $request->input('editEmail');
        $user->address = $request->input('editAddress');
        $user->status = $request->input('editStatus');

        

        $user->save();


        
        return $user;
    }
    // show permissions
    public function show_permissions(Request $request)
    {
        $query = Role::with('permissions');

        if ($search = $request->input('search.value')) {
            $query->where('productName', 'like', "%{$search}%")
                ->orWhere('customer', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhere('price', 'like', "%{$search}%")
                ->orWhere('created_at', 'like', "%{$search}%");
        }

        if ($order = $request->input('order.0.column')) {
            $columns = ['productName', 'customer', 'created_at', 'status', 'price'];
            $direction = $request->input('order.0.dir', 'asc');
            $query->orderBy($columns[$order], $direction);
        }

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $total = $query->count();

        $permissions = $query->offset($start)->limit($length)->get();

        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $permissions,
        ]);
    }

    //i manually put permissions
    // public function add_permission(Request $request)
    // {
    //     $permissions = [
    //         //products
    //         [
    //             'name' => 'product-create',
    //             'display_name' => 'Create Product',
    //             'description' => 'Create Product',
    //         ],
    //         [
    //             'name' => 'product-edit',
    //             'display_name' => 'Edit Product',
    //             'description' => 'Edit Product',
    //         ],
    //         [
    //             'name' => 'product-update',
    //             'display_name' => 'Update Product',
    //             'description' => 'Update Product',
    //         ],
    //         [
    //             'name' => 'product-delete',
    //             'display_name' => 'Delete Product',
    //             'description' => 'Delete Product',
    //         ],
    //         //users
    //         [
    //             'name' => 'user-create',
    //             'display_name' => 'Create User',
    //             'description' => 'Create User',
    //         ],
    //         [
    //             'name' => 'user-edit',
    //             'display_name' => 'Edit User',
    //             'description' => 'Edit User',
    //         ],
    //         [
    //             'name' => 'user-update',
    //             'display_name' => 'Update User',
    //             'description' => 'Update User',
    //         ],
    //         [
    //             'name' => 'user-delete',
    //             'display_name' => 'Delete User',
    //             'description' => 'Delete User',
    //         ],
    //     ];
        
    //     Permission::insert($permissions);
            
    // }

    // add permission
    public function add_permission(Request $request)
    {
        // return $request->product;
        // return $request->permissions;

        $validatedData = $request->validate([
            'previlegeName' => ['required', 'string', 'max:255'],
            'previlegeDisplayName' => ['required', 'string', 'max:255'],
            'previlegeDescription' => ['required', 'string', 'max:255'],
        ]);

        $role = Role::create([
            'name' => $validatedData['previlegeName'],
            'display_name' => $validatedData['previlegeDisplayName'],
            'description' => $validatedData['previlegeDescription'],
        ]);

        if(!empty($request->permissions)){
            $role->attachPermissions($request->permissions);
        };


        return $request;
    }
    // get permission
    public function get_permission(Request $request)
    {
        // return $request->id;
        $permission = Role::with('permissions')->findOrFail($request->id);

        return response()->json($permission);
    }
    // edit permission
    public function edit_permission(Request $request)
    {
        // return $request->permissions;

        $validatedData = $request->validate([
            'editPrevilegeName' => ['required', 'string', 'max:255'],
            'editPrevilegeDisplayName' => ['required', 'string', 'max:255'],
            'editPrevilegeDescription' => ['required', 'string', 'max:255'],
        ]);

        $role = Role::findOrFail($request->input('id'));

        $role->name = $request->input('editPrevilegeName');
        $role->display_name = $request->input('editPrevilegeDisplayName');
        $role->description = $request->input('editPrevilegeDescription');

        $role->save();

        $role->syncPermissions($request->permissions);

        // if(!empty($request->permissions)){
        //     $role->attachPermissions($request->permissions);
        // };

        
    }




}
