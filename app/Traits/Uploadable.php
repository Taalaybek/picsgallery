<?php


namespace App\Traits;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

trait Uploadable
{
	private $path;
	private $disk;
	private $album;
	private $fileName;
	private $file = null;
	private $data = [];
	private $thumbnails = [];

	/**
	 * Returns edited file's name
	 *
	 * @return mixed
	 */
	private function getFileName()
	{
		return auth()->user()->username . '_' .
			Str::random(8) .
			str_replace(' ', '_', '_' . $this->fileName);
	}

	/**
	 * Accept UploadedFile instance
	 *
	 * @param UploadedFile $file
	 * @return $this
	 */
	private function setFile(UploadedFile $file)
	{
		$this->file = $file;
		return $this;
	}

	/**
	 * Returns UploadedFile instance
	 *
	 * @return UploadedFile object
	 */
	private function getFile()
	{
		return $this->file;
	}

	/**
	 * Returns file's size
	 *
	 * @return string
	 */
	private function getFileSize(): string
	{
		$size = round($this->getFile()->getSize() / 1024, 1);
		switch ($size){
			case $size < 1000;
				$size = $size . 'Kb';
				break;
			case $size > 1000;
				$size = round($size / 1024, 2) . 'Mb';
		}
		return $size;
	}

	/**
	 * Returns file's extension
	 *
	 * @return mixed
	 */
	private function getExtension()
	{
		return $this->getFile()->extension();
	}

	/**
	 * Returns base name of file
	 *
	 * @return string
	 */
	private function getBaseName(): string
	{
		return $this->data['base_name'];
	}

	/**
	 * Returns full name of file with extension
	 *
	 * @return string
	 */
	private function getFullName(): string
	{
		return $this->data['full_name'];
	}

	/**
	 * Accept file's name
	 *
	 * @param string $fileName
	 * @return $this
	 */
	private function setFileName(string $fileName)
	{
		$this->fileName = $fileName;
		return $this;
	}

	/**
	 * @param string $path
	 * @return $this
	 */
	private function setPath(string $path)
	{
		$this->path = $path;
		return $this;
	}

	/**
	 * Returns original file's path
	 *
	 * @return string
	 */
	private function getPath()
	{
		return $this->path;
	}

	/**
	 * Returns thumbnails path
	 * @return string
	 */
	private function getThumbnailsPath()
	{
		return $this->disk.'/'.$this->album->id.'/'.'thumbnails';
	}

	private function getThumbnailImagePath(int $width): string
	{
		return $this->getThumbnailsPath().'/'.$this->getBaseName().'_'.$width.'.'.$this->getExtension();
	}

	/**
	 * Accepts and sets photo name
	 *
	 * @param string $name
	 * @return Uploadable $this
	 */
	private function setName(string $name)
	{
		$this->data['name'] = $name;

		return $this;
	}

	/**
	 * Accept file and file's name
	 *
	 * @param UploadedFile $file
	 * @param string $fileName
	 * @param Album $album
	 * @param string $disk
	 * @return Uploadable
	 */
	protected function setData(
		UploadedFile $file,
		Album $album,
		string $fileName = null,
		string $disk = 'albums'
		)
	{
		if (!\is_null($fileName)) {
			$this->setName($fileName);
			$this->setFileName($fileName);
		} else {
			$this->setFileName(mb_strtolower(env('APP_NAME')));
		}

		$this->setFile($file);
		$this->album = $album;
		$this->disk = $disk;
		$this->data['base_name'] = $this->getFileName();
		$this->data['full_name'] = $this->getBaseName().'.'.$this->getExtension();
		$this->data['mime_type'] = $this->getExtension();
		$this->data['size'] = $this->getFileSize();

		return $this;
	}

	/**
	 * Accepts request data and a Photo model
	 *
	 * @param Photo $photo
	 * @param Request $request
	 * @param string $disk
	 * @return void
	 */
	protected function setUpdatableData(
		Photo $photo,
		Request $request,
		string $disk = 'albums'
		)
	{
		$this->album = $photo->album;
		$this->disk = $disk;

		if ($request->has('file_name')) {
			$this->setName($request->get('file_name'));
    }

		if ($request->has('file_name') && $request->hasFile('file') && $request->file('file')->isValid()) {
			$this->setName($request->get('file_name'));
			$this->setFileName($request->get('file_name'));
    }

		if (!$request->has('file_name') && $request->hasFile('file') && $request->file('file')->isValid()) {

			if (!is_null($photo->name)) {
				$this->setFileName($photo->name);
			} else {
				$this->setFileName(mb_strtolower(config('name')));
			}

		}

		if ($request->hasFile('file') && $request->file('file')->isValid()) {
			$this->setFile($request->file('file'));
			$this->data['base_name'] = $this->getFileName();
			$this->data['full_name'] = $this->getBaseName().'.'.$this->getExtension();
			$this->data['mime_type'] = $this->getExtension();
			$this->data['size'] = $this->getFileSize();
		}

		return $this;
	}

	/**
	 * Returns formatted data
	 *
	 * @return array
	 */
	protected function getData(): array
	{
		if (!is_null($this->getFile())) {
			$this->data['thumbnails'] = json_encode($this->thumbnails);
		}

		return $this->data;
	}

	/**
	 * Uploads request file
	 *
	 * @return $this
	 */
	protected function toUpload()
	{
		$this->detectDiskFolder()
			->detectDiskSymlink();

		$this->setPath(Storage::putFileAs(
			$this->disk.'/'.$this->album->id,
			$this->getFile(),
			$this->getFullName()
		));

		$this->data['original_file_path'] = $this->getPath();
		return $this;
	}

	/**
	 * Makes thumbnail
	 *
	 * @param string $size
	 * @param int|null $width
	 * @param int|null $height
	 * @return $this | JsonResponse
	 */
	protected function makeThumbnail(string $size, int $width = null, int $height = null)
	{
		$this->detectThumbnailsFolder();

		try {
			$thumb = null;
			if (Storage::exists($this->getPath())) {
				$thumb = Image::make(Storage::get($this->getPath()));
			}

			if (!is_null($thumb) && !($thumb->getWidth() <= $width)) {
				$thumb->resize($width, $height, function ($constraint) {
					$constraint->aspectRatio();
				});

				$thumb->save(
					Storage::path($this->getThumbnailImagePath($width)),
					100
				);

				$this->thumbnails[$size] = [
					'width' => $width,
					'path' => $this->getThumbnailImagePath($width),
				];
			}
		} catch (FileNotFoundException $e) {
			return response()->json(['message' => $e->getMessage()], 404);
		}

		return $this;
	}

	private function detectDiskFolder()
	{
		if (!file_exists(Storage::path($this->disk))) {
			Storage::makeDirectory($this->disk);
		}

		return $this;
	}

	/**
	 * Determines if the disk folder's symlink
	 * exists and creates it if it doesn't
	 *
	 * @return $this
	 */
	private function detectDiskSymlink()
	{
		if (!file_exists(public_path(storage_path($this->disk)))) {
			Artisan::call('directory:link '.$this->disk);
		}

		return $this;
	}

	/**
	 * Determines if the thumbnails exists
	 * and creates it if it doesn't.
	 *
	 * @return $this
	 */
	private function detectThumbnailsFolder()
	{
		if (!file_exists(Storage::path($this->getThumbnailsPath()))) {
			Storage::makeDirectory($this->getThumbnailsPath());
		}

		return $this;
	}

	/**
	 * Returns Photo object instance
	 *
	 * @return Photo
	 */
	protected function save()
	{
		return new Photo($this->getData());
	}
}
