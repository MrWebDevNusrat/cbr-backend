<?php

namespace App\Http\Controllers\Resources;

use App\Models\Resources\Mediables;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use MediaUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use App\Models\Resources\Media;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Intervention\Image\Facades\Image;

class FileController extends Controller
{
    public function storeFile(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'files.*' => 'required|mimetypes:application/zip,application/x-dosexec,application/x-msdownload,application/exe,application/x-exe,application/dos-exe,vms/exe,application/x-winexe,application/msdos-windows,application/x-msdos-program,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,video/x-ms-asf,application/x-mpegURL,video/MP2T,video/avi,video/mp4,video/mpeg,video/x-matroska,video/x-flv,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/x-m4v,video/3gpp,video/3gpp2,application/octet-stream|max:4096000',
                'on_duplicate' => \Plank\Mediable\MediaUploader::ON_DUPLICATE_REPLACE,
            ]
        );

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $files = $request->file('files');
        $media = [];

        foreach ($files as $file) {
            $media[] = MediaUploader::fromSource($file)
                ->onDuplicateIncrement()
                ->useFilename(time())
                ->toDirectory('assets')
                ->upload();
        }
        $media = $this->full_url($media);

        return $this->successResponse($media);
    }

    public function storeImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'files.*' => 'required|image|mimes:jpeg,jpg,png|max:307200',
            'on_duplicate' => \Plank\Mediable\MediaUploader::ON_DUPLICATE_REPLACE,
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }
        $files = $request->file('files');
        $media = [];
        foreach ($files as $file) {
            $media[] = MediaUploader::fromSource($file)
                ->useFilename(time())
                ->onDuplicateIncrement()
                ->toDirectory('assets')
                ->setStrictTypeChecking(true)
                ->setAllowUnrecognizedTypes(true)
                ->setAllowedMimeTypes(['image/jpeg', 'image/png'])
                ->setAllowedExtensions(['jpg', 'jpeg', 'png'])
                ->setAllowedAggregateTypes(['image'])
                ->upload();
        }
        $media = $this->full_url($media);

        return $this->successResponse($media);
    }

    public function storeFileEditor(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'files.*' => 'required|mimetypes:application/zip,application/x-dosexec,application/x-msdownload,application/exe,application/x-exe,application/dos-exe,vms/exe,application/x-winexe,application/msdos-windows,application/x-msdos-program,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,video/x-ms-asf,application/x-mpegURL,video/MP2T,video/avi,video/mp4,video/mpeg,video/x-matroska,video/x-flv,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/x-m4v,video/3gpp,video/3gpp2,application/octet-stream|max:1024000',
                'on_duplicate' => \Plank\Mediable\MediaUploader::ON_DUPLICATE_REPLACE,
            ]
        );

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        if (!File::isDirectory('file_editor/file')) {
            Storage::disk('public')->makeDirectory('file_editor/file');
        }

        $files = $request->file('files');
        $media = [];

        foreach ($files as $file) {
            $media[] = MediaUploader::fromSource($file)
                ->onDuplicateIncrement()
                ->useFilename(time())
                ->toDirectory('file_editor/file')
                ->upload();
        }

        $media = $this->full_url($media);

        return $this->successResponse($media);
    }

    public function storeImageEditor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'files.*' => 'required|image|mimes:jpeg,jpg,png|max:204800',
            'on_duplicate' => \Plank\Mediable\MediaUploader::ON_DUPLICATE_REPLACE,
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        if (!File::isDirectory('file_editor/image')) {
            Storage::disk('public')->makeDirectory('file_editor/image');
        }

        $files = $request->file('files');
        $media = [];
        foreach ($files as $file) {
            $media[] = MediaUploader::fromSource($file)
                ->onDuplicateIncrement()
                ->useFilename(time())
                ->toDirectory('file_editor/image')
                ->setStrictTypeChecking(true)
                ->setAllowUnrecognizedTypes(true)
                ->setAllowedMimeTypes(['image/jpeg', 'image/png'])
                ->setAllowedExtensions(['jpg', 'jpeg', 'png'])
                ->setAllowedAggregateTypes(['image'])
                ->upload();
        }
        $media = $this->full_url($media);

        return $this->successResponse($media);
    }

    protected function full_url($media)
    {

        foreach ($media as $i => $item) {
            $media[$i]->file_url = url(
                Storage::url(
                    $item->directory . '/' .
                    $item->filename . '.' .
                    $item->extension));
        }

        return $media;
    }

    public function moveFolderFile($uuid, $model_id, $modelName)
    {
        $media = Media::find((int)$uuid);

        if (!File::isDirectory($modelName . DIRECTORY_SEPARATOR . $model_id . DIRECTORY_SEPARATOR . 'files')) {
            Storage::disk('public')->makeDirectory($modelName . DIRECTORY_SEPARATOR . $model_id . DIRECTORY_SEPARATOR . 'files');
        }

        File::move($this->mediaUrl($uuid), storage_path('app' . DIRECTORY_SEPARATOR . $media->disk . DIRECTORY_SEPARATOR . $modelName . DIRECTORY_SEPARATOR . $model_id . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $media->filename . '.' . $media->extension));

        $media->directory = $modelName . '/' . $model_id . '/' . 'files';
        $media->save();

        return $this->successResponse($media);
    }

    public function moveFolderImage($uuid, $model_id, $modelName)
    {
        $media = Media::find((int)$uuid);

        if (!File::isDirectory($modelName . DIRECTORY_SEPARATOR . $model_id . DIRECTORY_SEPARATOR . 'images')) {
            Storage::disk('public')->makeDirectory($modelName . DIRECTORY_SEPARATOR . $model_id . DIRECTORY_SEPARATOR . 'images');
        }

        File::move($this->mediaUrl($uuid), storage_path('app' . DIRECTORY_SEPARATOR . $media->disk . DIRECTORY_SEPARATOR . $modelName . DIRECTORY_SEPARATOR . $model_id . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $media->filename . '.' . $media->extension));

        $media->directory = $modelName . '/' . $model_id . '/' . 'images';
        $media->save();

        return $this->successResponse($media);
    }

    public function showImage($uuid, Request $request)
    {
        try {
            $model = Media::where([
                ['id', '=', $uuid]
            ])->first();

            return $this->renderFile($model, $request->get('width', null), $request->get('height', null), $request->get('type', null));

        } catch (ModelNotFoundException $e) {
            return $this->renderPlaceholder($request->get('width', null), $request->get('height', null));
        }
    }

    public function renderFile($model, $width, $height, $type)
    {
        if ($model) {
            $mediable_id = $this->FileUrl($model->id);
            $image = $this->makeFromPath($mediable_id, $width, $height, $type);
            return $image->response();
        } else {

            return $this->errorResponse('Media not found', 404);
        }
    }

    public function renderPlaceholder($width, $height)
    {
        $image = Image::cache(function ($image) use ($width, $height) {
            $img = $image->canvas(800, 800, '#FFFFFF');
            $this->resize($img, $width, $height);

            return $img;
        }, 10, true);

        return $image->response();
    }

    protected function makeFromPath($mediable_id, $width, $height, $type)
    {
        return Image::cache(function ($image) use ($type, $mediable_id, $width, $height) {
            $img = $image->make($mediable_id);
            if ($type == 'resize') {
                $this->resize($img, $width, $height);
            } elseif ($type == 'fit') {
                $this->fit($img, $width, $height);
            }
            return $img;
        }, 10, true);
    }

    protected function resize($img, $width, $height)
    {
        if (!empty($width) && !empty($height)) {
            $img->resize($width, $height);
        } elseif (!empty($width)) {
            $img->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        } elseif (!empty($height)) {
            $img->resize(null, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        return $img;
    }

    protected function fit($img, $width, $height)
    {
        if (!empty($width) && !empty($height)) {
            $img->fit($width, $height);
        } elseif (!empty($width)) {
            $img->fit($width, null, function ($constraint) {
                $constraint->upsize();
            });
        } elseif (!empty($height)) {
            $img->fit(null, $height, function ($constraint) {
                $constraint->upsize();
            });
        }
        return $img;
    }

    public function mediaUrl($uuid)
    {
        $media = Media::find($uuid);
        $pathToFile = [];
        if ($media) {
            $pathToFile = storage_path('app' . DIRECTORY_SEPARATOR . $media->disk . DIRECTORY_SEPARATOR . $media->directory . DIRECTORY_SEPARATOR . $media->filename . '.' . $media->extension);
        }

        return $pathToFile ? $pathToFile : '';
    }

    public function FileUrl($uuid)
    {
        $media = Media::find($uuid);
        $pathToFile = [];
        if ($media) {
            $pathToFile = url(
                Storage::url($media->disk . '/' . $media->directory . '/' . $media->filename . '.' . $media->extension));
        }

        return $pathToFile ? $pathToFile : '';
    }

    public function download($uuid)
    {
        $media = Media::find($uuid);

        $pathToFile = [];
        if ($media) {
            $pathToFile = storage_path('app' . DIRECTORY_SEPARATOR . $media->disk . DIRECTORY_SEPARATOR . $media->directory . DIRECTORY_SEPARATOR . $media->filename . '.' . $media->extension);
        }

        return $pathToFile ? Response::download($pathToFile) : '';
    }

    public function storeTestFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'files.*' => 'required|mimetypes:application/vnd.openxmlformats-officedocument.wordprocessingml.document|max:102400',
            'on_duplicate' => \Plank\Mediable\MediaUploader::ON_DUPLICATE_REPLACE,
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }
        if (!File::isDirectory('test')) {
            Storage::disk('public')->makeDirectory('test');
        }

        $files = $request->file('files');

        $media = [];
        foreach ($files as $file) {
            $media[] = MediaUploader::fromSource($file)
                ->onDuplicateIncrement()
                ->useFilename(time())
                ->toDirectory('test')
                ->upload();
        }

        $media = $this->full_url($media);

        return $this->successResponse($media);
    }
}
