<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class UploadController extends Controller
{
    public function index(Request $request)
    {
        return 'Hello';
    }

    public function upload(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
  
        $image = $request->file('image');
        $image_name = time().'.'.$image->extension();
     
        $destinationPathCompressed = $this->public_path('images_compressed');

        $img = Image::make($image->path());

        $img->resize(100, 100, function ($constraint) {
            $constraint->aspectRatio(); // config aspect ratio
        })->save($destinationPathCompressed.'/'.$image_name);
   
        $destinationPath = $this->public_path('images');
        $image->move($destinationPath, $image_name);
   

        $data = [
            'message' => 'Success',
            'original_path' => $destinationPath.'/'.$image_name,
            'compressed_path' => $destinationPathCompressed.'/'.$image_name
        ];

        return response()->json($data);
    }

    /**
     * Get the path to the public folder.
     *
     * @param  string  $path
     * @return string
     */
    function public_path($path = null)
    {
        return rtrim(app()->basePath('public/' . $path), '/');
    }
}
