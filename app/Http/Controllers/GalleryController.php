<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = array(
            'id' => "posts",
            'menu' => 'Gallery',
            'galleries' => array()
        );
        return view('gallery.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('gallery.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required',
            'picture' => 'image|nullable|max:1999'
        ]);
        if ($request->hasFile('picture')) {
            $filenameWithExt = $request->file('picture')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture')->getClientOriginalExtension();
            $basename = uniqid() . time();
            $smallFilename  = "small_{$basename}.{$extension}";
            $mediumFilename  = "medium_{$basename}.{$extension}";
            $largeFilename  = "large_{$basename}.{$extension}";
            $filenameSimpan = "{$basename}.{$extension}";
            $path = $request->file('picture')->storeAs('posts_image', $filenameSimpan);
        } else {
            $filenameSimpan = 'noimage.png';
        }
        // dd($request->input());
        $post = new Post;
        $post->picture = $filenameSimpan;
        $post->title = $request->input('title');
        $post->description = $request->input('description');
        $post->save();
        return redirect('gallery')->with('success', 'Berhasil menambahkan data baru');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/gallery",
     *     tags={"gallery"},
     *     summary="Returns a Sample API gallery response",
     *     description="A sample gallery to test out the API",
     *     operationId="galler",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent
     *           (example={
     *               "success": true,
     *               "message": "Berhasil memproses galleries",
     *               "galleries": {
     *                  {
     *                      "id": 1,
     *                      "title": "galllery 1",
     *                      "description": "deskripsi galllery 1",
     *                      "picture": "672ad27a5fe841730859642.jpeg",
     *                      "created_at": "2024-11-06T02:20:42.000000Z",
     *                      "updated_at": "2024-11-06T02:20:42.000000Z"
     *                  }
     *              }
     *          }),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data tidak ditemukan",
     *         @OA\JsonContent
     *           (example={
     *               "detail": "strings"
     *          }),
     *     )
     * )
     */
    public function api()
    {
        $data = array(
            'message' => 'Berhasil memproses galleries',
            'success' => true,
            'galleries' => Post::where('picture', '!=', '')->whereNotNull('picture')->orderBy('created_at', 'desc')->get()
        );
        return response()->json($data);
    }
}
