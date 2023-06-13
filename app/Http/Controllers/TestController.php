<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestModel;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    public function index()
    {
       $tests = TestModel::get();

        return response(view('Test.index', ["tests" => $tests]));
    }

    public function addImage(Request $request)
    {
       
        $photo = $request->file('photos');
        $name = $photo->getClientOriginalName();
        $filePath  = "Test/";
        Storage::disk('ceph')->put($filePath, $photo, [
            'visibility' => 'private',
            'mimetype' => 'image/svg+xml'
        ]);

        // if ($request->hasFile('photos')) {
        //     $file = $request->file('photos');
        //     $destinationPath = storage_path('app/uploads');
        //     // $name = $file->getClientOriginalName();
        //     $file->move($destinationPath, $file->getClientOriginalName());
        //     // Storage::put("$destinationPath/$name", $file);
        // }

        return 'success';
        
    }

    public function getImage()
    {
        $photoPath = "Test/nDr49mWFsx3y0Jt7dxXTi6YZk22CpjHu6TPN6G93.mp4";

        $content = Storage::disk('ceph')->get($photoPath);

        return response($content)->header('Content-Type', 'audio/mp4');
    }
}
