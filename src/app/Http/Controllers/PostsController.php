<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostFormRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $posts = DB::insert('insert into posts (title, excerpt, body, image_path, is_published, min_to_read) values (?, ?, ?, ?, ?, ?)', [
            // 'HelloWorld', 'test', 'test', 'test', true, 1
        // ]);
        // $posts = DB::select('select * from posts where id = :id', ['id' => 1]);
        // $posts = DB::update('update posts set body = ? where id= ?', [
            // 'body last updated', 103
        // ]);
        // $posts = DB::delete('delete from posts where id = ?', [103]);
        // $posts = DB::table('posts')
            // -> select('*')
            // -> where('is_published', true)
            // -> where('id', '>', 50)
            // -> whereBetween('min_to_read', [2, 6])
            // -> whereIn('min_to_read', [2, 6, 8])
            // -> get();
        // $posts = DB::table('posts') -> get();
        // $posts = Post::orderBy('id', 'desc')->take(10)->get();
        // dd($posts);
        $posts = Post::orderBy('updated_at', 'desc')->paginate(20);
        return view('blog.index', [
            'posts' => $posts
        ]);
        // return view('blog.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('blog.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostFormRequest $request)
    {
        /*$post = new Post();
        $post -> title -> $request -> title;
        $post -> excerpt -> $request -> excerpt;
        $post -> body -> $request -> body;
        $post -> image_path = 'temporary';
        $post -> is_published = $request -> is_published === 'on';
        $post -> min_to_read = $request -> min_to_read;
        $post -> save();*/

        $request -> validated();

        Post::create([
            'title' => $request -> title,
            'excerpt' => $request -> excerpt,
            'body' => $request -> body,
            'image_path' => $this -> storeImage($request),
            'is_published' => $request -> is_published === 'on',
            'min_to_read' => $request -> min_to_read,
        ]);

        return redirect(route('blog.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::findOrFail($id);
        // dd($posts);
        return view('blog.show', [
            'post' => $post
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('blog.edit', [
            'post' => Post::where('id', $id) -> first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostFormRequest $request, string $id)
    {
        $request -> validated();

        Post::where('id', $id) -> update([$request -> except(['_token', '_method'])]);
        return redirect(route('blog.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Post::destroy($id);

        return redirect(route('blog.index')) -> with('message', 'post has been deleted.');
    }

    private function storeImage(Request $request){
        $newImageName = uniqid() . '-'.$request -> title . '.' . $request -> image -> extension();
        return $request -> image -> move(public_path('images'), $newImageName);
    }
}
