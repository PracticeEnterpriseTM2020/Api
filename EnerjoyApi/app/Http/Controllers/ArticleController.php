<?php

namespace App\Http\Controllers;

use App\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Validator;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware("can:human-resources")->only(["create", "update", "delete", "restore"]);
    }

    public function getAll()
    {
        return Article::all();
    }

    public function getById(Article $article)
    {
        return $article;
    }

    public function filter(Request $request)
    {
        $cols = Schema::getColumnListing("articles");
        $validator = Validator::make($request->all(), [
            "sort" => Rule::in($cols),
            "order" => Rule::in(["asc", "desc"]),
            "amount" => "integer|gt:0"
        ]);
        if ($validator->fails()) return response()->json(["error" => $validator->messages()->all()], 400);

        $sort = $request->input("sort", "id");
        $order = $request->input("order", "asc");
        $search = $request->input("search", "");
        $amount = $request->input("amount", 5);

        $response = Article::where("title", "like", "%$search%")
            ->orWhere("description", "like", "%$search%")
            ->orderBy($sort, $order)
            ->paginate($amount);

        return collect(["sort" => $sort, "order" => $order, "search" => $search])->merge($response);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "title" => "required|string",
            "description" => "required|string|max:65535",
            "creator_id" => "required|integer|exists:employees,id"
        ]);
        if ($validator->fails()) return response()->json(["error" => $validator->messages()->all()], 400);

        $article = Article::create($request->all());
        return response()->json($article, 201);
    }

    public function update(Request $request, Article $article)
    {
        $validator = Validator::make($request->all(), [
            "title" => "required|string",
            "description" => "required|string|max:65535",
            "creator_id" => "required|integer|exists:employees,id"
        ]);
        if ($validator->fails()) return response()->json(["error" => $validator->messages()->all()], 400);

        $article->update($request->all());
        return response()->json($article, 200);
    }

    public function delete(Article $article)
    {
        $article->delete();
        return response()->json(null, 204);
    }

    public function restore($id)
    {
        $article = Article::onlyTrashed()->findOrFail($id);
        $article->restore();
        return $article;
    }
}
