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

    public function uploadToSquare(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
  
        $image = $request->file('image');
        $image_name = time().'.'.$image->extension();
   
        $destinationPath = $this->public_path('images');

        $image = Image::make($request->file('image'));

        // Get the desired size of the square canvas
        $size = max($image->getWidth(), $image->getHeight());

        // Create a new canvas with the desired size and white background
        $canvas = Image::canvas($size, $size, '#ffffff');

        // Calculate the desired padding for the image
        $paddingX = ($size - $image->getWidth()) / 2;
        $paddingY = ($size - $image->getHeight()) / 2;

        // Insert the original image onto the canvas with the desired padding
        $canvas->insert($image, 'top-left', $paddingX, $paddingY);

        // Save the image
        $canvas->save($destinationPath.'/' . $image_name);

        $data = [
            'message' => 'Success',
            'path' => $destinationPath.'/'.$image_name,
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
