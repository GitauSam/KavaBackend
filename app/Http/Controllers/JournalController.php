<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JournalController extends Controller
{

    use ApiResponser;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $posts = Journal::where('user', '=', auth()->user()->id)->get();
            return $this->success([
                'posts' => $posts
            ]);
        } catch (Exception $e) {
            return $this->error(
                'failed to get posts',
                101
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::debug("create endpoint hit");
        Log::debug("payload: " . $request);

        $req = $request->validate(
            [
                'message' => 'required|string',
            ]
        );

        Log::debug("validated");

        $allowedfileExtension=['pdf','jpg','png'];
        $file = $request->file('image'); 
        $errors = [];
 
        $extension = $file->getClientOriginalExtension();

        $check = in_array($extension,$allowedfileExtension);

        if($check) {
            $imageName = time().'.'.$request->image->extension();
            // $request->image->storeAs('images', $imageName);
            // storage/app/images/file.png
            $request->image->move(public_path('images'), $imageName);
        } else {
            return response()->json(['invalid_file_format'], 422);
        }

        $post = Journal::create(
                                [
                                    'message' => $req['message'],
                                    'user' => auth()->user()->id,
                                    'image_uri' => '/images/' . $imageName
                                ]
                            );

        Log::debug("created");

        try {
            return $this->success([
                'post' => $post
            ]);
        } catch(Exception $e) {
            return $this->error(
                'failed to create posts',
                102
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $post = Journal::find($id);
            return $this->success([
                'post' => $post
            ]);
        } catch(Exception $e) {
            return $this->error(
                'failed to get post',
                101
            );
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $req = $request->validate(['message' => 'required|string']);
        try {
            $journal = Journal::find($id);
            $journal->message = $req['message'];
            $journal->save();

            return $journal;
        } catch(Exception $e) {
            return $this->error(
                'failed to update post',
                103
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
