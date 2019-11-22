<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Comment;
class CommentsController extends Controller
{
    public function send(Request $request){
        $validator = Validator::make($request->all(),
            [
                'message' => 'required',
            ],
            [
                'message.required' => 'Поле сообщение не может быть пустым!'
            ]
        );


        if ($validator->fails()) {
            return response()->json(['messages' => $validator->errors()], 200);
        }

        if ($file = $request->file('file')){
            Storage::disk('public')->put($file->getFilename().'.'.$file->getClientOriginalExtension(),  File::get($file));
        }

        $new_comment = Comment::create([
            "parent_id"         => $request->parent_id,
            "message"           => $request->message,
            "user_id"           => $request->user()->id,
            "mime"              => ($file)?$file->getClientMimeType():"",
            "original_filename" => ($file)?$file->getClientOriginalName():"",
            "filename"          => ($file)?$file->getFilename().'.'.$file->getClientOriginalExtension():""
        ]);

        $comment = Comment::where('id',$new_comment->id)->with('user')->first();
        return $comment;
    }

    public function all(){
        return Comment::orderBy('created_at', 'asc')->parent()->with('replies.user','user')->paginate(20);
    }

    public function get(Request $request, Comment $comment){
        return $comment->with('user')->first();
    }

}
