<?php
namespace App\Services;


use App\Models\Photo;
use Illuminate\Http\Request;

class AlbumsPhotoResolver extends PhotoResolverService
{
	/**
	 * Accepts request data and a Photo model
	 *
	 * @param Photo $photo
	 * @param Request $request
	 * @param string $disk
	 * @return $this
	 */
	public function setMutableData(
		Photo $photo,
		Request $request,
		string $disk = 'albums'
	)
	{
		$this->model = $photo->album;
		$this->symlinkResolver = new SymlinkResolverService($disk, $this);

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

		$this->data['user_id'] = auth()->user()->id;

		return $this;
	}
}

