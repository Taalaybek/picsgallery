<?php
namespace App\Services;

use App\Contracts\Mediable;
use App\Models\Photo;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Contracts\PhotoResolverInterface;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class PhotoResolverService implements PhotoResolverInterface
{
	protected $path;
	protected $disk;
	protected $model;
	protected $fileName;
	protected $symlinkResolver;
	protected $file = null;
	protected $data = [];
	protected $thumbnails = [];

	/**
	 * Returns edited file's name
	 *
	 * @return mixed
	 */
	protected function getFileName()
	{
		return auth()->user()->username . '_' .
			Str::random(8) .
			str_replace(' ', '_', '_' . $this->fileName);
	}

	/**
	 * Accept file's name
	 *
	 * @param string $fileName
	 * @return $this
	 */
	protected function setFileName(string $fileName)
	{
		$this->fileName = $fileName;
		return $this;
	}

	/**
	 * Accept UploadedFile instance
	 *
	 * @param UploadedFile $file
	 * @return $this
	 */
	protected function setFile(UploadedFile $file)
	{
		$this->file = $file;
		return $this;
	}

	/**
	 * Returns UploadedFile instance
	 *
	 * @return UploadedFile object
	 */
	protected function getFile()
	{
		return $this->file;
	}

	/**
	 * Returns file's size
	 *
	 * @return string
	 */
	protected function getFileSize(): string
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
	protected function getExtension()
	{
		return $this->getFile()->extension();
	}

	/**
	 * Returns base name of file
	 *
	 * @return string
	 */
	protected function getBaseName(): string
	{
		return $this->data['base_name'];
	}

	/**
	 * Returns full name of file with extension
	 *
	 * @return string
	 */
	protected function getFullName(): string
	{
		return $this->data['full_name'];
	}

	/**
	 * Accepts and sets photo name
	 *
	 * @param string $name
	 * @return PhotoResolverService
	 */
	protected function setName(string $name)
	{
		$this->data['name'] = $name;

		return $this;
	}

	/**
	 * @param string $path
	 * @return $this
	 */
	protected function setPath(string $path)
	{
		$this->path = $path;
		return $this;
	}

	/**
	 * Returns original file's path
	 *
	 * @return string
	 */
	protected function getPath()
	{
		return $this->path;
	}

	/**
	 * Returns thumbnails path
	 * @return string
	 */
	public function getThumbnailsPath()
	{
		return $this->disk.'/'.$this->model->id.'/'.'thumbnails';
	}

	public function getThumbnailImagePath(int $width): string
	{
		return $this->getThumbnailsPath().'/'.$this->getBaseName().'_'.$width.'.'.$this->getExtension();
	}

	/**
	 * Accept file and file's name
	 *
	 * @param UploadedFile $file
	 * @param $model
	 * @param string $fileName
	 * @param string $disk
	 * @return PhotoResolverService
	 */
	public function setData(UploadedFile $file, $model, string $fileName = null, string $disk = 'albums')
	{
		if (!\is_null($fileName)) {
			$this->setName($fileName);
			$this->setFileName($fileName);
		} else {
			$this->setFileName(mb_strtolower(env('APP_NAME')));
		}
		$this->symlinkResolver = new SymlinkResolverService($disk, $this);
		$this->setFile($file);
		$this->model = $model;
		$this->disk = $disk;
		$this->data['user_id'] = \auth()->user()->id;
		$this->data['base_name'] = $this->getFileName();
		$this->data['full_name'] = $this->getBaseName().'.'.$this->getExtension();
		$this->data['mime_type'] = $this->getExtension();
		$this->data['size'] = $this->getFileSize();

		return $this;
	}

	/**
	 * Returns formatted data
	 *
	 * @return array
	 */
	public function getData(): array
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
	public function toUpload()
	{
		$this->symlinkResolver->detectDiskFolder()
			->detectDiskSymlink();

		$this->setPath(Storage::putFileAs(
			$this->disk.'/'.$this->model->id,
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
	public function makeThumbnail(string $size, int $width = null, int $height = null)
	{
		$this->symlinkResolver->detectThumbnailsFolder();

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

	/**
	 * Returns Photo object instance
	 *
	 * @return Photo
	 */
	public function save()
	{
		return new Photo($this->getData());
	}
}

