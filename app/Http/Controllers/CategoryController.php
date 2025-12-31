<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoryModel;

class CategoryController extends Controller {
    public function index(Request $request) {
        $query = CategoryModel::latest();
        if($request->search){
            $query->where('title','like','%'. $request->search .'%')
                  ->orWhere('description','like','%'. $request->search .'%');
        }
        $categories = $query->paginate($request->per_page ?? 5);
        return $this->actionSuccess( 'Category fetch successfully',$categories);

    }

    public function store( Request $request ) {
        $validated = $request->validate( [
            'title' => 'required|string|max:255|unique:category,title',
            'description' => 'nullable|string',
        ] );
        $exists = CategoryModel::where( 'title', $request->title )
        ->exists();
        if ( $exists ) {
            return $this->actionFailure( 'This category title already exists!' );
        }

        $category = CategoryModel::create( $validated );
        return $this->actionSuccess( 'Category added successfully', $category );
    }
    

    public function show( $id ) {
        $category = CategoryModel::findOrFail( $id );
        return $this->actionSuccess( 'Category show', $category );
    }

    public function update( Request $request, $id ) {
        $category = CategoryModel::findOrFail( $id );
        $exists = CategoryModel::where( 'title', $request->title )
        ->whereNot( 'id', $request->id )
        ->exists();
        if ( $exists ) {
            return $this->actionFailure( 'This category title already exists.' );
        }
        $category->update( $request->only( [ 'title', 'description' ] ) );
        return $this->actionSuccess( 'Category updated successfully', $category );
    }

    public function destroy( $id ) {
        $category = CategoryModel::findOrFail( $id );
        $category->delete();
        return $this->actionSuccess( 'Category deleted successfully' );
    }

}
