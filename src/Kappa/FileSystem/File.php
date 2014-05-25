<?php
/**
 * This file is part of the Kappa\FileSystem package.
 *
 * (c) Ondřej Záruba <zarubaondra@gmail.com>
 *
 * For the full copyright and license information, please view the license.md
 * file that was distributed with this source code.
 */

namespace Kappa\FileSystem;

use Kappa\Utils\Validators;
use Nette\Http\FileUpload;
use Nette\Image;

/**
 * Class File
 * @package Kappa\FileSystem
 */
class File
{
	/** @var string */
	private $path;

	/**
	 * @param string $path
	 */
	private function __construct($path)
	{
		$this->path = realpath($path);
	}

	/**
	 * @param string $path
	 * @param string|null $content
	 * @return File
	 * @throws IOException
	 */
	public static function create($path, $content = null)
	{
		if (is_file($path)) {
			throw new FileAlreadyExistException("File '{$path}' has not been created because already exist");
		}
		if (@file_put_contents($path, $content) === false) {
			throw new IOException("File '{$path}' has not been created");
		}

		return new self($path);
	}

	/**
	 * @param string $path
	 * @return File
	 * @throws FileNotFoundException
	 */
	public static function open($path)
	{
		if (!is_file($path)) {
			throw new FileNotFoundException("File '{$path}' has not been found");
		}

		return new self($path);
	}

	/**
	 * @param FileUpload $fileUpload
	 * @param string|Directory $target
	 * @return File
	 * @throws InvalidArgumentException
	 * @throws IOException
	 * @throws FileAlreadyExistException
	 */
	public static function upload(FileUpload $fileUpload, $target)
	{
		if (!is_string($target) && !$target instanceof Directory) {
			throw new InvalidArgumentException(__METHOD__ . ": Target must be string or instance of Direcotry");
		}
		if ($target instanceof Directory) {
			$target = $target->getInfo()->getPathname() . DIRECTORY_SEPARATOR . $fileUpload->getSanitizedName();
		}
		if (is_file($target)) {
			throw new FileAlreadyExistException("File '{$target}' already exist");
		}
		if (!$fileUpload->isOk()) {
			throw new IOException("File '{$fileUpload->getName()}' has not been saved");
		}
		$fileUpload->move($target);

		return File::open($target);
	}

	/**
	 * @param Image $image
	 * @param string $path
	 * @param int|null $quality
	 * @param int|null $type
	 * @return File
	 */
	public static function fromImage(Image $image, $path, $quality = null, $type = null)
	{
		$image->save($path, $quality, $type);

		return File::open($path);
	}

	public static function fromUrl($source, $target, $overwrite = false)
	{
		if (!Validators::checkHttpStatus($source, array(200))) {
			throw new UrlNotFoundException("Url '{$source}' has not been found");
		}
		if (!$overwrite && is_file($target)) {
			throw new FileAlreadyExistException("File '{$target}' already exist");
		}
		$fp = fopen ($target, 'a+');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $source);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);

		return File::open($target);
	}

	/**
	 * @return SplFileInfo
	 */
	public function getInfo()
	{
		return new SplFileInfo($this->path);
	}

	/**
	 * @return string
	 */
	public function read()
	{
		return file_get_contents($this->path);
	}

	/**
	 * @param string|null $content
	 * @return bool
	 */
	public function overwrite($content = null)
	{
		return file_put_contents($this->path, $content) === false ? : true;
	}

	/**
	 * @return bool
	 */
	public function clear()
	{
		return $this->overwrite(null);
	}

	/**
	 * @param string $content
	 * @param bool $newLine
	 * @return bool
	 */
	public function append($content, $newLine = true)
	{
		if ($newLine) {
			$content = PHP_EOL . $content;
		}
		return (file_put_contents($this->path, $content, FILE_APPEND) === false) ? : true;
	}

	/**
	 * @return Image
	 */
	public function toImage()
	{
		return Image::fromFile($this->path);
	}
}