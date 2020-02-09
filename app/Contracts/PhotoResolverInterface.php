<?php
namespace App\Contracts;

use Illuminate\Http\UploadedFile;

interface PhotoResolverInterface
{
	/**
	 * Sets data of photo file
	 *
	 * @param UploadedFile $file
	 * @param $model
	 * @param string $fileName
	 * @param string $disk
	 * @return $this
	 */
	public function setData(UploadedFile $file, $model, string $fileName = null, string $disk = 'albums');

	/**
	 * Uploads file
	 *
	 * @return $this
	 */
	public function toUpload();

	/**
	 * Makes thumbnails
	 *
	 * @param string $size
	 * @param integer $width
	 * @param integer $height
	 * @return $this
	 */
	public function makeThumbnail(string $size, int $width = null, int $height = null);

	/**
	 * Returns new Photo instance
	 *
	 * @return $this
	 */
	public function save();

	/**
	 * Returns array of photo's fields
	 *
	 * @return array
	 */
	public function getData(): array;
}

