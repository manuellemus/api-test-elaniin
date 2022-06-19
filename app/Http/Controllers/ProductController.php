<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.verify');
    }
    /**
     * @OA\Get(
     *  path="/api/products/",
     *  summary="Get the list of products",
     *  tags={"Products End points"},
     *      @OA\Response(
     *         response=200, 
     *             description="Return a list of products"
     *      ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ups, fail request."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Authorization Token not found."
     *     ),
     *    security={{ "apiAuth": {} }},
     * )
     */
    public function index()
    {
        try {
            $products = Product::orderBy('id', 'desc')->paginate();
            return response()->json([
                'status' => 200,
                'message' => 'success',
                'response' => $products,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Ups, fail request',
                'details' => $e->getFile() . $e->getLine() . $e->getMessage()
            ]);
        }
    }
    /**
     * @OA\Get(
     *  path="/api/products/10",
     *  summary="Get One product by id",
     *  tags={"Products End points"},
     *      @OA\Response(
     *         response=200, 
     *             description="Return one product"
     *      ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ups, fail request."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Authorization Token not found."
     *     ),
     *    security={{ "apiAuth": {} }},
     * )
     */
    public function get_product($id)
    {
        // return $id;
        try {
            $product = Product::find($id);
            return response()->json([
                'status' => 200,
                'message' => 'Data found successfully',
                'response' => $product,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Oops something went wrong',
                'details' => $e->getFile() . $e->getLine() . $e->getMessage()
            ]);
        }
    }

    /**
     * @OA\Post(
     *  path="/api/products/search",
     *  summary="search product by Product Name, Name and SKU",
     *  tags={"Products End points"},
     *      @OA\Parameter(
     *         description="Enter product Name",
     *         name="name",
     *         in="query",
     *         description="product Name",
     *     ),
     *      @OA\Parameter(
     *         description="Enter de SKU",
     *         name="code",
     *         in="query",
     *         description="SKU of product",
     *     ),
     *      @OA\Response(
     *         response=200, 
     *             description="Return one product"
     *      ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ups, fail request."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Authorization Token not found."
     *     ),
     *    security={{ "apiAuth": {} }},
     * )
     */
    public function search(Request $request)
    {
        try {
            $products = Product::select()
                ->orderByDesc('id');

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
    /**
     * @OA\Post(
     *  path="/api/products/store",
     *  summary="Save Product",
     *  tags={"Products End points"},
     *      @OA\Parameter(
     *         description="Enter product Name",
     *         name="name",
     *         in="query",
     *         description="name",
     *         required = true
     *     ),
     *      @OA\Parameter(
     *         description="Enter  amount",
     *         name="amount",
     *         in="query",
     *         description="amount",
     *         example="20",
     *         required = true
     *     ),
     *      @OA\Parameter(
     *         description="Enter  price",
     *         name="price",
     *         in="query",
     *         description="price",
     *         example="40.69",
     *         required = true
     *     ),
     *      @OA\Parameter(
     *         description="Enter description",
     *         name="description",
     *         in="query",
     *         description="description",
     *         example="loremp ipsum",
     *     ),
     *      @OA\Parameter(
     *         description="Enter image url",
     *         name="image",
     *         in="query",
     *         description="image",
     *         example="https://via.placeholder.com/640x480.png/000022?text=quis",
     *         required = true
     *     ),
     *      @OA\Response(
     *         response=200, 
     *             description="Data save successfully"
     *      ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Oops something went wrong."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Authorization Token not found."
     *     ),
     *    security={{ "apiAuth": {} }},
     * )
     */

    public function store(Request $request)
    {
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

    /**
     * @OA\Put(
     *  path="/api/products/update/90",
     *  summary="Update Product",
     *  tags={"Products End points"},
     *      @OA\Parameter(
     *         description="Enter product Name",
     *         name="name",
     *         in="query",
     *         description="name",
     *         required = true
     *     ),
     *      @OA\Parameter(
     *         description="Enter  amount",
     *         name="amount",
     *         in="query",
     *         description="amount",
     *         example="20",
     *         required = true
     *     ),
     *      @OA\Parameter(
     *         description="Enter  price",
     *         name="price",
     *         in="query",
     *         description="price",
     *         example="20",
     *         required = true
     *     ),
     *      @OA\Parameter(
     *         description="Enter description",
     *         name="description",
     *         in="query",
     *         description="description",
     *         example="loremp ipsum",
     *     ),
     *      @OA\Parameter(
     *         description="Enter image url",
     *         name="image",
     *         in="query",
     *         description="image",
     *         example="https://via.placeholder.com/640x480.png/000022?text=quis",
     *         required = true
     *     ),
     *      @OA\Response(
     *         response=200, 
     *             description="Data save successfully"
     *      ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Oops something went wrong."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Authorization Token not found."
     *     ),
     *    security={{ "apiAuth": {} }},
     * )
     */

    public function update(Request $request, $id)
    {
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
    /**
     * @OA\Delete(
     *  path="/api/products/7",
     *  summary="Soft Delete One product by id",
     *  tags={"Products End points"},
     *      @OA\Response(
     *         response=204, 
     *             description="Data deleted successfully"
     *      ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ups, fail request."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Authorization Token not found."
     *     ),
     *    security={{ "apiAuth": {} }},
     * )
     */
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
