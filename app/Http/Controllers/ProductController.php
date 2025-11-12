<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use Illuminate\Http\Request;

class ProductController extends Controller {
    public function index() {
        $products = ProductModel::with('category')->latest()->get();
        return $this->actionSuccess( 'Products fetch successfully', $products );

    }

    public function delete( $id ) {
        ProductModel::where( 'id', $id )->delete();
        $products = ProductModel::latest()->get();
        return $this->actionSuccess( 'Product deleted successfully', $products );
    }

    public function store( Request $request ) {
        $request->validate( [
            'title'=>'required|string|unique:products,title',
            'description'=> 'required|string',
            'category_id'=> 'required|exists:category,id',
            'price'=> 'required|numeric|min:0|max:100'
        ] );


        ProductModel::create( [
            'title'=>$request->title,
            'description'=>$request->description,
            'category_id'=>$request->category_id,
            'price'=>$request->price
        ] );
        return $this->actionSuccess( 'Product added successfully' );
    }

    public function show( $id ) {
        $product = ProductModel::findOrFail( $id );
        return $this->actionSuccess( 'Product show', $product );
    }

    public function update( Request $request, $id ) {
        $product = ProductModel::findOrFail( $id );
        $request->validate( [
            'title'=>'required|string',
            'description'=> 'required|string',
            'category_id'=> 'required|exists:category,id',
            'price'=> 'required|numeric|min:0|max:100'
        ] );
        $exists = ProductModel::where( 'title', $request->title )
        ->whereNot( 'id', $request->id )->exists();
        if ( $exists ) {
            return $this->actionFailure( 'Product title name already exists' );
        }
        $product->update( [
            'title'=> $request->title,
            'description'=> $request->description,
            'category_id'=> $request->category_id,
            'price'=> $request->price
        ] );
            return $this->actionSuccess("Product updated successfully", $product);
    }

}
