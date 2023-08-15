<?php

namespace CLSystems\PhalCMS\Lib\Helper;

use Exception;
use Imagecow\Image as ImageHandler;
use Phalcon\Image\Adapter\Gd;
use Phalcon\Image\Enum;

class Image
{
	protected $imageUri;
	protected $imageFile;
	protected $imageThumbUri;

	public function __construct($imageFile)
	{
		if (strpos($imageFile, BASE_PATH . '/public/upload/') !== 0)
		{
			$imageFile = BASE_PATH . '/public/upload/' . $imageFile;
		}

		$this->imageFile     = $imageFile;
		$this->imageUri      = str_replace(BASE_PATH . '/public', DOMAIN, $this->imageFile);
		$this->imageThumbUri = dirname($this->imageUri) . '/thumbs';
	}

	public function getResize($width = null, $height = null)
	{
		if (null === $width && null === $height)
		{
			$width = 100;
		}

		preg_match('#^.*(\.[^.]*)$#', $this->imageFile, $matches);
		$extension = $matches[1];
		$thumbName = basename($this->imageFile, $extension) . '_' . ($width ?: 0) . 'x' . ($height ?: 0) . $extension;
		$thumbPath = dirname($this->imageFile) . '/thumbs';

		if (!is_file($thumbPath . '/' . $thumbName))
		{
			if (!is_dir($thumbPath))
			{
				mkdir($thumbPath, 0777, true);
			}

			if ($width && $height)
			{
				$master = Enum::AUTO;
			}
			elseif ($width)
			{
				$master = Enum::WIDTH;
			}
			else
			{
				$master = Enum::HEIGHT;
			}

			try
			{
				$handler = new Gd($this->imageFile);
				$handler->resize($width, $height, $master);
				$handler->save($thumbPath . '/' . $thumbName, 100);
			}
			catch (Exception $exception)
			{
//				debugVar($exception);
				try
				{
					$handler = ImageHandler::fromFile($this->imageFile, ImageHandler::LIB_IMAGICK);
					$handler->quality(100);
					$handler->resize($width, $height);
					$handler->format('jpg');
					$handler->save($thumbPath . '/' . $thumbName);
				}
				catch (Exception $exception)
				{
					$this->imageFile = BASE_PATH . '/public/upload/image_not_found.png';
					$handler = new Gd($this->imageFile);
					$handler->resize($width, $height, $master);
					$handler->save($thumbPath . '/' . $thumbName, 100);
				}
			}

		}

		return $this->imageThumbUri . '/' . $thumbName;
	}

	public function getRatio($width = null)
	{
		$imageLoc = self::getResize($width);
		if (false === empty($imageLoc))
		{
			[$width, $height, $type, $attr] = getimagesize($imageLoc);
			if ($height === 0 || true === empty($height))
			{
				$height = 1;
			}
			$aspect = $width / $height;
		}
		else
		{
			$aspect = 1;
		}
		return $aspect;
	}

	public function getUri()
	{
		return $this->imageUri;
	}

	public function exists()
	{
		return is_file($this->imageFile);
	}

	public static function loadImage($imageString, $returnFirst = true)
	{
		$imageString = trim($imageString);
		$imageList   = [];

		if (strpos($imageString, '[') === 0
			|| strpos($imageString, '{') === 0
		)
		{
			$images = json_decode($imageString, true) ?: [];
		}
		else
		{
			$images = [$imageString];
		}

		if ($images)
		{
			foreach ($images as $image)
			{
				$handler = new Image($image);

				if ($handler->exists())
				{
					$imageList[] = $handler;
				}
			}

			if ($imageList)
			{
				return $returnFirst ? $imageList[0] : $imageList;
			}
		}

		return false;
	}
}
