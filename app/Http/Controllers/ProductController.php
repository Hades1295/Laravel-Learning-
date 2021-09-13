<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $product =DB::table('products')
        ->leftJoin('product_stock', 'products.id', '=', 'product_stock.product_id')
        ->get();

        $products = Product::latest()->paginate(5);
        return view('products.index',compact('products'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('products.create'); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {              
            $request->validate([
                'name' => 'required',
                'sku'=>'required',
                'description' => 'required',
                'price' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $input = $request->all();
            if ($request->file('image')) {
                $destinationPath = 'image/';
                $image = $request->file('image');
                $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $profileImage);
                $input['image'] = "$profileImage";         
            }
            $product =  Product::create($input);

            $productId = $product->id;
            ProductStock::create([
                'product_id'=> $productId,
                'isInStock' =>$input['stock'],
                'stockCount' =>$input['stockCount']
            ]);
            return redirect()->route('products.index')
                ->with('success', 'Product created successfully.');
        } catch (\Throwable $th) {
            dd($th);
        }
       
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {   
        $products =DB::table('products')
        ->leftJoin('product_stock', 'products.id', '=', 'product_stock.product_id')
        ->get();
        foreach ($products as $product) {
         if($product->product_id == $id){
            return view('products.show', compact('product',$product));
         }
        }
        
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {   
        $products =DB::table('products')
        ->leftJoin('product_stock', 'products.id', '=', 'product_stock.product_id')
        ->get();    
        foreach ($products as $product) {
            if($product->product_id == $id){
                return view('products.edit', compact('product',$product));
            }
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'sku'=>'required',
            'description' => 'required',
            'price' => 'required'
        ]);
        $input = $request->all();
        try {
            $input['image'] ='';
            if ($request->hasFile('image')) {
                $destinationPath = 'image/';
                $image = $request->file('image');
                $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $profileImage);
                $input['image'] = "$profileImage";
            }
             Product::find($input['productId'])->update([
                'name'=>$input['name'],
                'sku'=>$input['sku'],
                'description'=>$input['description'],
                'price'=>$input['price'],
                'image'=>$input['image']
            ]);
             ProductStock::where('product_id',$input['productId'])->update([
                'isInStock' =>$input['stock'],
                'stockCount' =>$input['stockCount']
            ]);
            return redirect()->route('products.index')
                ->with('success', 'Product Updated successfully.');
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
      
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $id)
    {
        try {
            $id->delete();
            return redirect()->route('products.index')
            ->with('success', 'Product Deleted successfully.');
        } catch (\Throwable $th) {
            report($th);
            return false;
        }

    }

    public function exportCsv()
    {
        try {
            $fileName = 'products.csv';
            $products =DB::table('products')
            ->leftJoin('product_stock', 'products.id', '=', 'product_stock.product_id')
            ->get();
            $headers = array(
             'Content-Type' => 'text/csv',
            );
             $i =0;   
             $columns = array('No','Name', 'Sku','Image','Description', 'Price', 'Is_In_Stock','Stock_Count');
             $file = fopen($fileName, 'w');
             fputcsv($file, $columns);
             foreach ($products as $product) {                     
                fputcsv($file, array(
                     ++$i,   
                     $product->name,
                     $product->sku,
                     $product->image,
                     $product->description,
                     $product->price,
                     $product->isInStock,
                     $product->stockCount));   
                     }
                fclose($file);
                     return response()->download($fileName, 'products.csv', $headers);        
        } catch (\Throwable $th) {
             dd($th);
        }     
    }

    public function importCsv(Request $request)
    {
        $data           =       array();
        $request->validate([
            "csv_file" => "required",
        ]);
         $file = $request->file("csv_file");
         $csvData = file_get_contents($file);
         $rows = array_map("str_getcsv", explode("\n", $csvData));
         $header = array_shift($rows);
        foreach ($rows as $row) {
            if (isset($row[0])) {
                if ($row[0] != "") {
                    $row = array_combine($header, $row);
                    $productData = array(
                        "name" => $row['Name'],
                        "sku" => $row['Sku'],
                        "image" => $row["Image"],
                        "description" => $row["Description"],
                        "price" => $row["Price"]
                    );
                    //----------- check if lead already exists ----------------
                    $productLead        =   DB::table('products')
                                        ->where('sku', "=", $row["Sku"])->get();
                      if (!is_null($productLead)) {
                        try {
                            $updateLead   = Product::where("sku", "=", $row["Sku"])->update($productData);
                            $products =DB::table('products')
                            ->leftJoin('product_stock', 'products.id', '=', 'product_stock.product_id')
                            ->get();
                            foreach ($products as $product) {

                                if($row["Sku"] == $product->sku ){
                                    ProductStock::where('product_id',$product->product_id)->update([
                                        'isInStock' =>$row['Is_In_Stock'],
                                        'stockCount' =>$row['Stock_Count']
                                    ]);
                                    if($updateLead == '1') {
                                        return redirect()->route('products.index')
                                        ->with('success', 'Product Imported successfully.');
                                    }
                                }
                            }
                        } catch (\Throwable $th) {
                            return redirect()->route('products.index')
                            ->with('error', '$th->message');
                        }
                    }
                    else {
                        try {
                            $product = Product::create($productData);
                            $productId = $product->id;
                            ProductStock::create([
                               'product_id'=> $productId,
                               'isInStock' =>$row['Is_In_Stock'],
                               'stockCount' =>$row['Stock_Count']
                             ]);
                            if(!is_null($product)) {
                                return redirect()->route('products.index')
                                ->with('success', 'Product Imported successfully.');
                            }
                        } catch (\Throwable $th) {
                            throw $th;
                        }
                    }
                }
            }
        }
        return redirect()->route('products.index');
    }
}   