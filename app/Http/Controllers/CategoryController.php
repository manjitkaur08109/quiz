<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoryModel;

class CategoryController extends Controller {
    public function index() {
        $data = CategoryModel::latest()->get();
        return $this->actionSuccess( 'Category fetch successfully', $data );

    }

    public function store( Request $request ) {
        $validated = $request->validate( [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ] );

        $category = CategoryModel::create( $validated );
        return $this->actionSuccess('Category added successfully',$category);
    }

    public function show( $id ) {
        $category = CategoryModel::findOrFail( $id );
        return $this->actionSuccess('Category show',$category);
    }

    public function update( Request $request, $id ) {
        $category = CategoryModel::findOrFail( $id );
        $category->update( $request->only( [ 'title', 'description' ] ) );

        return $this->actionSuccess('Category updated successfully',$category);
    }

    public function destroy( $id ) {
        $category = CategoryModel::findOrFail( $id );
        $category->delete();
        return $this->actionSuccess( 'Category deleted successfully' );
    }

}
