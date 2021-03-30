<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Tools;

use SplFileInfo as File;

final class Rule
{
	/** @var string */
	private $pattern;

	/** @var string */
	private $type;

	/** @var string */
	private $owner;

	/** @var int[] */
	private $modes;


	/**
	 * @param string  $pattern
	 * @param string  $type
	 * @param string  $owner
	 * @param int[]  $modes
	 */
	public function __construct(
		string $pattern,
		string $type,
		string $owner,
		iterable $modes
	) {
		$this->pattern = $pattern;
		$this->type = $type;
		$this->owner = $owner;
		$this->modes = $modes;
	}


	/**
	 * @param  File  $file
	 * @return bool
	 */
	private function isDesiredType(File $file): bool
	{
		if ($this->type == 'any') {
			return true;
		}

		if ($file->isFile() && $this->type == 'file') {
			return true;
		}

		if ($file->isDir() && $this->type == 'dir') {
			return true;
		}

		return false;
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
