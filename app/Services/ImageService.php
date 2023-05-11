<?php

namespace App\Services;

use App\Models\Image;
use Cloudinary\Cloudinary;
use Cloudinary\Api\Exception\ApiError;
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Api\Search\SearchApi;
use Cloudinary\Configuration\Configuration;

class ImageService {
	protected Cloudinary $cloudinary;
	protected AdminApi $admin_api;
	public function __construct() {
		$this->cloudinary = new Cloudinary(
			[
				'cloud' => [
					'cloud_name' => 'dotkkdeep',
					'api_key'    => '285966162357411',
					'api_secret' => 'FRgpKogWzlmaCixKj3JFj7lSW0E',
				],
			]
		);
        $config = Configuration::instance();
        $config->cloud->cloudName = 'dotkkdeep';
        $config->cloud->apiKey = '285966162357411';
        $config->cloud->apiSecret = 'FRgpKogWzlmaCixKj3JFj7lSW0E';
        $this->admin_api = new AdminApi();
	}

	public function uploadImage($imageRequest, $parent_id, $parent = 'appointment'){
		$uploader = $this->cloudinary->uploadApi();
		$filename = explode('.',$imageRequest->getClientOriginalName())[0];

		$uploadResponse = $uploader->upload($imageRequest->getPathname(), [
			"public_id" => $filename,
			"resource_type" => "auto"
		]);
		$image = new Image();
		$image->public_id = $uploadResponse['public_id'];
		$image->asset_type = $uploadResponse['resource_type'];
		$image->delivery_type = $uploadResponse['type'];
		$image->file_name = $imageRequest->getClientOriginalName();
		$image->mime =  $uploadResponse['format'];
        $image->parent_type = $parent;
        $image->parent_id = $parent_id;
		$image->save();
		return $image;
	}

	public function getImageUrl($id){
		$image = Image::find($id);
		$url = null;
        $url = $this->admin_api->assetsByIds($image->public_id)['resources'][0]['url'];
		try {
			$url = $this->admin_api->assetsByIds($image->public_id)['resources'][0]['url'];
		} catch (\Exception $e){

		}
		return $url;
	}

	public function updateImage( $id, $imageRequest ) {
		$image          = Image::find( $id );
		$uploader       = $this->cloudinary->uploadApi();
		$uploadResponse = null;
		try {
			$uploadResponse = $uploader->upload( $imageRequest->getPathname(), [
				'public_id' => $image->public_id,
				"resource_type" => "auto"
			] );
		} catch ( \Exception $e ) {
		}
		if ( $uploadResponse ) {
			$image->public_id     = $uploadResponse['public_id'];
			$image->asset_type    = $uploadResponse['resource_type'];
			$image->delivery_type = $uploadResponse['type'];
			$image->file_name     = $imageRequest->getClientOriginalName();
			$image->mime          = $uploadResponse['format'];
			$image->save();

			return [
				'status' => 'OK',
				'data'   => $image,
			];
		}

		return [ 'status' => 'NG' ];
	}

	public function deleteImage( $id ) {
		$image = Image::find( $id );
		if ( ! $image ) {
			return [ 'status' => 'NG' ];
		}
		$deleteResponse = $this->admin_api->deleteAssets( $image->public_id );
		if ( isset( $deleteResponse['deleted'] ) ) {
			$image->delete();

			return 0;
		}

		return 1;
	}

}
