<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class S3TestController extends Controller
{
    public function upload()
    {
        Storage::disk('s3')->put('files/test.txt', 'Hello S3');

        return 'Uploaded to S3';
    }
}
