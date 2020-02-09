<?php


namespace App\Services;


use App\Contracts\PhotoResolverInterface;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SymlinkResolverService
{
	protected $disk;
	protected $resolver;

	public function __construct(string $disk, PhotoResolverInterface $resolver)
	{
		$this->disk = $disk;
		$this->resolver = $resolver;
	}

	public function detectDiskFolder()
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
	public function detectDiskSymlink()
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
	public function detectThumbnailsFolder()
	{
		if (!file_exists(Storage::path($this->resolver->getThumbnailsPath()))) {
			Storage::makeDirectory($this->resolver->getThumbnailsPath());
		}

		return $this;
	}
}
