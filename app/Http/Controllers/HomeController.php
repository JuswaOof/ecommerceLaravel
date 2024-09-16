<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Order;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


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

        $availableProductCount = Product::where('isAvailable', 'Available')->count();
        $notAvailableProductCount = Product::where('isAvailable', 'Not Available')->count();

        return view('home', compact('products', 'orders', 'availableProductCount', 'notAvailableProductCount'));
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

    
    // public function order_data(Request $request)
    // {
    //     $productCounts = Order::select('productName')->groupBy('productName')->selectRaw('count(*) as count')->get();

    //     return response()->json($productCounts);
    // }



}
