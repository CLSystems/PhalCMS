<?php

namespace CLSystems\PhalCMS\Tasks\Common;

use CLSystems\PhalCMS\Lib\Mvc\Model\Post;
use CLSystems\PhalCMS\Tasks\MainTask;

/**
 * Class DeleteOldImagesTask
 *
 * @package CLSystems\PhalCMS\Tasks\Common
 */
class DeleteOldImagesTask extends MainTask
{
    /**
     * @var string
     */
    private $dirToScan;

    /**
     * Main function for cron
     */
    public function mainAction()
    {
        $this->dirToScan = '/var/www/html/korting-en-acties.nl/public/upload/deals';
        $dirs = $this->dirToArray($this->dirToScan);
        foreach ($dirs as $dir => $results)
        {
            $this->info('Found ' . count($results) . ' files to process...');
            $this->processResults($dir, $results);
        }
    }

    private function processResults($dir, $results)
    {
        $removed = 0;
        foreach ($results as $filename)
        {
            $this->info('Processing ' . $filename . '...');
            // Try to find a record with this filename as image (LIKE '%' . $filename)
            $posts = Post::find([
                'conditions' => 'image LIKE :image:',
                'bind'       => [
                    'image' => '%' . $filename . '%',
                ],
            ])->toArray();

            // If not found, delete/unlink image + thumbs
            if (true === empty($posts))
            {
                $this->info('FOUND ORPHAN - ' . $dir . '/' . $filename . ', removing...');
                unlink($this->dirToScan . '/' . $dir . '/' . $filename);
                [$basename, $ext] = explode('.', $filename);
                foreach (glob($this->dirToScan . '/' . $dir . '/thumbs/' . $basename . '*') as $file)
                {
                    $this->info('Deleting thumb ' . $file);
                    unlink($file);
                }

                ++$removed;
            }
            else
            {
                $this->info('NOT ORPHAN - ' . $this->dirToScan . '/' . $dir . '/' . $filename . ', skipping...');
            }
            usleep(250000);
        }
        $this->info('Removed ' . $removed . ' images.');
    }

    private function dirToArray($dir) : array
    {
        $result = [];

        $cdir = scandir($dir);
        foreach ($cdir as $key => $value)
        {
            if (!in_array($value, [".", "..", "thumbs"]))
            {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value))
                {
                    $result[$value] = self::dirToArray($dir . DIRECTORY_SEPARATOR . $value);
                }
                else
                {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

}
