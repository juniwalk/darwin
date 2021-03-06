<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin;

use SplFileInfo as File;

final class Rule
{
	/** @var string */
	private $pattern;

	/** @var string */
	private $owner;

	/** @var int[] */
	private $modes;


	/**
	 * @param string  $pattern
	 * @param string  $owner
	 * @param int[]  $modes
	 */
	public function __construct(
		string $pattern,
		string $owner,
		iterable $modes
	) {
		$this->pattern = $pattern;
		$this->owner = $owner;
		$this->modes = $modes;
	}


	/**
	 * @return static
	 */
	public static function createOpen(): self
	{
		return new self('/\/(.*)/i', 'www-data', [644, 755]);
	}


	/**
	 * @param  File  $file
	 * @return bool
	 */
	private function isDesiredType(File $file): bool
	{
		if (isset($this->modes[0]) && $file->isFile()) {
			return true;
		}

		if (isset($this->modes[1]) && $file->isDir()) {
			return true;
		}

		return true;
	}


	/**
	 * @param  File  $file
	 * @return bool
	 */
	public function apply(File $file): bool
	{
		$path = $file->getRealPath();

		if (!$this->isDesiredType($file) || !preg_match($this->pattern, $path)) {
			return false;
		}

		chown($path, $this->owner);

		if ($file->isFile()) {
			chmod($path, octdec((string) $this->modes[0]));
		}

		if ($file->isDir()) {
			chmod($path, octdec((string) $this->modes[1]));
		}

		return true;
	}
}
