<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    public function index()
    {
        try {
            $products = Product::paginate();
            return response()->json([
                'status' => 200,
                'message' => 'success',
                'data' => $products,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Ups, fail request',
                'details' => $e->getFile() . $e->getLine() . $e->getMessage()
            ]);
        }
    }
    public function get_product($id)
    {
        // return $id;
        try {
            $product = Product::find($id);
            return response()->json([
                'status' => 200,
                'message' => 'Data found successfully',
                'data' => $product,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Oops something went wrong',
                'details' => $e->getFile() . $e->getLine() . $e->getMessage()
            ]);
        }
    }

    public function search(Request $request)
    {
        try {
            $products = Product::select()
            ->orderByDesc('id');

            // return $request->name;
            if (!empty($request->name)) {
                $products->NAME($request->name);
            }
            if (!empty($request->code)) {
                $products->CODE($request->code);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Data found',
                'data' => $products->get()
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Oops something went wrong',
                'details' => $e->getFile() . $e->getLine() . $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        // return 'hehehe';
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50',
            'amount' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 500,
                'message' => $validator->errors(),
            ]);
        }

        try {
            DB::beginTransaction();

            $product = new product();
            $product->name = $request->name;
            $product->amount = $request->amount;
            $product->price = $request->price;
            $product->description = !empty($request->description) ? $request->description : null;
            $product->image = !empty($request->image) ? $request->image : null;
            $product->save();
            $product->code = strtoupper(substr($product->name, 0, 3)) . '-' . $product->id;
            $product->save();

            DB::commit();

            return response()->json([
                'status' => 201,
                'message' => 'Data save successfully',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Oops something went wrong',
                'details' => $e->getFile() . $e->getLine() . $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        // return 'hehehe';
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50',
            'amount' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 500,
                'message' => $validator->errors(),
            ]);
        }

        try {
            DB::beginTransaction();

            $product = product::find($id);
            $product->name = $request->name;
            $product->amount = $request->amount;
            $product->price = $request->price;
            $product->description = !empty($request->description) ? $request->description : null;
            $product->image = !empty($request->image) ? $request->image : null;
            $product->save();
            $product->code = strtoupper(substr($product->name, 0, 3)) . '-' . $product->id;
            $product->save();

            DB::commit();

            return response()->json([
                'status' => 204,
                'message' => 'Data modified successfully',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Oops something went wrong',
                'details' => $e->getFile() . $e->getLine() . $e->getMessage()
            ]);
        }
    }
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $product = product::find($id);

            $product->delete();

            DB::commit();

            return response()->json([
                'status' => 204,
                'message' => 'data deleted successfully',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Oops something went wrong',
                'details' => $e->getFile() . $e->getLine() . $e->getMessage()
            ]);
        }
    }
}
