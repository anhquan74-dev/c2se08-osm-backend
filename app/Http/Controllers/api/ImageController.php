<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\ImageService;
use Illuminate\Http\Request;

class ImageController extends Controller
{
	public function uploadImage(Request $request){
		$image = $request->image;
		$imageService = new ImageService();

		return $imageService->uploadImage($image);
	}

	public function getImageUrl($id){
		$imageService = new ImageService();

		return $imageService->getImageUrl($id);
	}

	public function updateImage($id, Request $request){
		$image = $request->image;
		$imageService = new ImageService();

		return $imageService->updateImage($id, $image);
	}

	public function delete($id){
		$imageService = new ImageService();

		return $imageService->deleteImage($id);
	}
}
