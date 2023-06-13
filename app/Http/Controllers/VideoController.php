<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use function PHPUnit\Framework\throwException;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
// use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Storage\ChunkStorage;
use Pion\Laravel\ChunkUpload\Exceptions\UploadFailedException;

class VideoController extends Controller
{
    public function videoStore(Request $request)
    {

        if($request->hasFile('video'))
        {
            $video = $request->file('video');

            $videoName = pathinfo($video->getClientOriginalName(), PATHINFO_FILENAME);
    
            $filePath = "video/$videoName";

            // Storage::disk('ceph')->put($filePath, $video, [
            //     'visibility' => 'private',
            //     'mimetype' => 'audio/mp4'
            // ]);

            // Storage::disk('ceph')->putFileAs($filePath, $video, "$videoName.mp4");
 
            // $destinationPath = storage_path('app/uploads');
            // $video->move($destinationPath, "$videoName.mp4");

           try {
            $disk = Storage::disk('minio');

            $disk->put($filePath, fopen($video, 'r+'));
           } catch (\Throwable $th) {
            dd($th);
           }

            // Video::create([
            //     'title' => $videoName,
            //     'filePath' => $filePath . $videoName . ".mp4 "
            // ]);

            return 'success';
        }else{
            return 'error';
        }

    }

    public function getVideo($id)
    {
        try {

            $video = Video::select('filePath')->findOrFail($id);

        } catch (\Throwable $th) {

           dd($th);

        }

        $content = Storage::disk('ceph')->get($video->filePath);

        return response($content)->header('Content-Type', 'audio/mp4');
    }

    public function addVideo(Request $request)
    {
        if($request->hasFile('video'))
        {

            $video = $request->file('video');

            // $filename = $video->getClientOriginalName();

            // $disk = Storage::disk('ceph');

            // $stream = $disk->put($filename, '');

            // if ($stream === false) {

            //     return response()->json(['error' => 'Unable to open the destination file'], 500);

            // }

            // $tempPath = $video->getRealPath();

            // $file = fopen($tempPath, 'r');

            // $chunkSize = 1024 * 1024; // 1MB

            // while ($chunk = fread($file, $chunkSize)) {
            //     fwrite($stream, $chunk);
            // }

            // fclose($file);
            // fclose($stream);

            // return response()->json(['message' => 'Video uploaded successfully']);

            $videoName = $video->getClientOriginalName();

            $filePath = "Test/" . $videoName;

            // Storage::disk('ceph')->putFileAs($filePath, fopen($video, 'r+'), $videoName);
            $disk = Storage::disk('ceph');

            $disk->put($filePath, fopen($video, 'r+'));


            Video::create([
                'title' => $videoName,
                'filePath' => $filePath . $videoName
            ]);

            return response()->json([
                'data' => true
            ]);
        }

        return response()->json([
            'data' => false
        ]);
    }

    public function editVideo(Request $request)
    {
        $videos = Video::findOrFail($request->id);

        if($request->hasFile('video'))
        {

            Storage::disk('ceph')->delete($videos->filePath);

            $video = $request->file('video');

            $videoName = $video->getClientOriginalName();

            $filePath = "Test/";

            Storage::disk('ceph')->putFileAs($filePath, $video, $videoName);

            $videos->update([
                'filePath' => $filePath . $videoName
            ]);
        }

        $videos->update([
            'title' => $request->title,
        ]);

        return response()->json([
            'data' => true
        ]);
        
    }

    public function deleteVideo($id)
    {
        $video = Video::find($id);

        Storage::disk('ceph')->delete($video->filePath);

        $video->delete();

        return 'success';
    }

    public function showVideo($id)
    {
        $video = Video::find($id);

        $videoURL = Storage::disk('ceph')->get($video->filePath);

        return response($videoURL)->header('Content-Type', 'audio/mp4');
    }

    public function indexVideo()
    {
        $videos = Video::get();

        return response()->success($videos, 'Video List', 200);
    }

    public function uploadVue(Request $request)
    {
    
        $this->uploadOrEdirVideoToStorage($request, 'add');

        // $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));

        // $fileReceived = $receiver->receive('file');

        // if ($fileReceived->isFinished()) { 

        //     $file = $fileReceived->getFile(); 

        //     $extension = $file->getClientOriginalExtension();

        //     $fileName = md5(time()) . '.' . $extension;

        //     $filePath = "videos/$fileName";

        //     Storage::disk('ceph')->put($filePath, fopen($file, 'r+'));

        //     unlink($file->getPathname());

            //local
            // $destinationPath = storage_path('app/uploads');

            // $file->move($destinationPath, $fileName);

        // }

        return response()->json(['message' => 'success']);
    }

    public function updateVideo(Request $request)
    {
        $this->uploadOrEdirVideoToStorage($request, 'update');

        return response()->json([
            'message' => 'Successfully Updated video'
          ]);
    }

    public function showVueVideo($id)
    {
        $video = Video::select('id', 'title', 'filePath')->findOrFail($id);

        return response()->success($video, 'Video', 200);
       
    }

    public function showVideos(Request $request)
    {
        $content = Storage::disk('ceph')->get($request->filePath);

        return response($content)->header('Content-Type', 'audio/mp4');
    }

    public function updateVideoData($id, Request $request)
    {
        Video::where('id', $id)->update([
            'title' => $request->title
        ]);

        return response()->success(null, 'Successfully Update Video Data', 200);
    }

    public function uploadOrEdirVideoToStorage($request, $status)
    {
        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));

        if (!$receiver->isUploaded()) {
            
        }

        $id = $request->input('id');
        $title = $request->input('title');

        $fileReceived = $receiver->receive('file');

        if ($fileReceived->isFinished()) { 
            $file = $fileReceived->getFile(); 

            //create file name
            $extension = $file->getClientOriginalExtension();
            $fileName = md5(time()) . '.' . $extension;

            $filePath = "videos/$fileName";

            if($status == 'update')
            {
                $video = Video::find($id);

                Storage::disk('ceph')->delete($video->filePath);

                $video->update([
                    'title' => $title,
                    'filePath' => $filePath
                ]);

            }else{
                
                Video::create([
                    'title' => $title,
                    'filePath' => $filePath
                ]);

            }

            // Storage::disk('ceph')->putFileAs($filePath, fopen($file->getPathname(), 'r'), $fileName);

            Storage::disk('minio')->put($filePath, fopen($file, 'r+'));

            unlink($file->getPathname());

            return true;

        }

    }
}
