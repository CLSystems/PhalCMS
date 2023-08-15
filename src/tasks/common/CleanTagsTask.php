<?php

namespace CLSystems\PhalCMS\Tasks\Common;

use CLSystems\PhalCMS\Lib\Mvc\Model\Tag;
use CLSystems\PhalCMS\Lib\Mvc\Model\UcmItem;
use CLSystems\PhalCMS\Lib\Mvc\Model\UcmItemMap;
use CLSystems\PhalCMS\Tasks\MainTask;
use CLSystems\Php\Filter;
use Phalcon\Db\Adapter\Pdo\Mysql;

/**
 * Class CleanTagsTask
 *
 * @package CLSystems\PhalCMS\Tasks\Common
 */
class CleanTagsTask extends MainTask
{
    /**
     * Main function for cron
     */
    public function mainAction()
    {
        // Get tags, the newest first.
        $tags = Tag::find([
//            'conditions' => 'checkedAt IS NULL',
            'order'      => 'id desc',
//            'limit'      => 1000,
        ]);

        if (false === empty($tags))
        {
            $this->info('Found ' . $tags->count() . ' tag(s)');
            foreach ($tags as $tag)
            {
                $this->info('### Processing tag ' . $tag->title . ' (' . $tag->id . ')');
                // Numeric shizzle, delete
                if (true === is_numeric($tag->title))
                {
                    $this->deleteTag($tag);
                }
                else
                {
                    $this->cleanupTag($tag);
                    $tag->checkedAt = date('Y-m-d H:i:s');
                    $tag->checkedBy = 3;
                    $tag->save();
                }
                sleep(1);
            }
        }
    }

    /**
     * @return void
     */
    public function deleteAction()
    {
        // Get tags, the newest first.
        $tags = Tag::find([
            'order'      => 'id desc',
//            'limit'      => 1000,
        ]);

        if (false === empty($tags))
        {
            $this->info('Found ' . $tags->count() . ' tag(s)');
            foreach ($tags as $tag)
            {
                $checked = is_numeric($tag->title);
                if (true === $checked)
                {
                    $this->deleteTag($tag);
                    sleep(1);
                }
            }
        }
    }

    public function adjustAction($params)
    {
        [$currentMapping, $newMapping] = explode(',', $params);
        if (true === empty($currentMapping) || true === empty($newMapping))
        {
            $this->info('One or more params missing...');
            var_dump($params); die;
        }

        // Adjust mapping
        /** @var Mysql $db */
        $db = $this->getDI()->get('db');
        $prefixTable = $this->modelsManager->getModelPrefix();
        $db->execute(
            'UPDATE ' . $prefixTable . 'ucm_item_map SET itemId2 = :new_mapping  WHERE itemId2 = :current_mapping ON DUPLICATE KEY UPDATE itemId2=itemId2;',
            [
                'current_mapping' => $currentMapping,
                'new_mapping' => $newMapping,
            ]
        );

//        $this->getDI()
//            ->getShared('modelsManager')
//            ->executeQuery('UPDATE ' . UcmItemMap::class . ' SET itemId2 = ' . $newMapping . ' WHERE itemId2 = ' . $currentMapping);


//        $items = UcmItemMap::find([
//            'conditions' => 'context = "tag" AND itemId2 = :item_id2:',
//            'bind' => [
//                'item_id2' => $currentMapping,
//            ],
//        ]);
//        /** @var UcmItemMap $item */
//        foreach ($items as $item)
//        {
//            $this->info('Updating mapped item(s): ' . $item->itemId1 . ' = ' . $currentMapping . ' -> ' . $newMapping . ')');
//            $item->itemId2 = $newMapping;
//            $item->update();
//            usleep(100000);
//            $item->save();
//        }

    }

    /**
     * @param Tag $tag
     * @return void
     */
    private function cleanupTag(Tag $tag)
    {
        $needsCleanup = false;
        $cleanupDashes = false;
        $cleanupCommas = false;
        $cleanupSlashes = false;
        $cleanupWaves = false;
        $cleanupUnderscores = false;
        $newTagIds = [];

        // Checks
        $multipleDashes = substr_count($tag->title, '-');
        if (1 <= $multipleDashes)
        {
            $this->info('Found dashes, needs cleanup ' . $tag->title . ' (' . $tag->slug . ')');
            $needsCleanup = true;
            $cleanupDashes = true;
        }

        $multipleCommas = substr_count($tag->title, ',');
        if (1 <= $multipleCommas)
        {
            $this->info('Found commas, needs cleanup ' . $tag->title . ' (' . $tag->slug . ')');
            $needsCleanup = true;
            $cleanupCommas = true;
        }

        $multipleSlashes = substr_count($tag->title, '/');
        if (1 <= $multipleSlashes)
        {
            $this->info('Found slashes, needs cleanup ' . $tag->title . ' (' . $tag->slug . ')');
            $needsCleanup = true;
            $cleanupSlashes = true;
        }

        $multipleWaves = substr_count($tag->title, '~');
        if (1 <= $multipleWaves)
        {
            $this->info('Found waves, needs cleanup ' . $tag->title . ' (' . $tag->slug . ')');
            $needsCleanup = true;
            $cleanupWaves = true;
        }

        $multipleUnderscores = substr_count($tag->title, '_');
        if (1 <= $multipleUnderscores)
        {
            $this->info('Found underscores, needs cleanup ' . $tag->title . ' (' . $tag->slug . ')');
            $needsCleanup = true;
            $cleanupUnderscores = true;
        }

        // Do the actual cleanup
        if (true === $needsCleanup)
        {
            // Split up the current title and turn it into slugs
            $dashItems = [];
            if (true === $cleanupDashes)
            {
                $dashItems = explode('-', $tag->title);
            }

            $commaItems = [];
            if (true === $cleanupCommas)
            {
                $commaItems = explode(',', $tag->title);
            }

            $slashItems = [];
            if (true === $cleanupSlashes)
            {
                $slashItems = explode('/', $tag->title);
            }

            $waveItems = [];
            if (true === $cleanupWaves)
            {
                $waveItems = explode('~', $tag->title);
            }

            $underscoreItems = [];
            if (true === $cleanupUnderscores)
            {
                $underscoreItems = explode('_', $tag->title);
            }

            $tagItems = array_unique(array_merge($dashItems, $commaItems, $slashItems, $waveItems, $underscoreItems));
            foreach ($tagItems as $tagItem)
            {
                $this->info('Processing slug ' . $tagItem);
                if (true === empty($tagItem))
                {
                    // If empty, get on with the next one...
                    $this->info('Slug empty, continuing...');
                    continue;
                }
                $tagItemSlug = Filter::toSlug($tagItem);
                // Check for existing slug
                /** @var Tag $existingTag */
                $existingTag = Tag::findFirst([
                    'conditions' => 'slug = :slug_string:',
                    'bind'       => [
                        'slug_string' => 'tag-' . $tagItemSlug,
                    ],
                ]);
                if (false === empty($existingTag))
                {
                    $this->info('Found existing tag (' . $existingTag->id . ' : ' . $existingTag->slug . ')');
                    $newTagIds[] = $existingTag->id;
                }
                else
                {
                    // If not exist, create
                    $newTag = new Tag();
                    $newTag->title = $tagItem;
                    $newTag->slug = 'tag-' . $tagItemSlug;
                    $newTag->createdAt = date('Y-m-d H:i:s');
                    $newTag->createdBy = 3;
                    $newTag->checkedAt = date('Y-m-d H:i:s');
                    $newTag->checkedBy = 3;
                    $newTag->save();
                    $existingTag = $newTag->refresh();
                    $this->info('Created new tag (' . $existingTag->id . ' : ' . $existingTag->slug . ')');
                    $newTagIds[] = $existingTag->id;
                }
            }

            // Get all rows that relate/map to this tag
            $mappedItems = UcmItemMap::find([
                'conditions' => 'itemId2 = :item_id2:',
                'bind'       => [
                    'item_id2' => $tag->id,
                ],
            ]);
            foreach ($mappedItems as $mappedItem)
            {
                $this->info('Found mapped item ' . $mappedItem->itemId1 . ' (' . $mappedItem->itemId2 . ')');
                // Insert mapped item with new tags
                foreach ($newTagIds as $newTagId)
                {
                    $insertMappedItem = new UcmItemMap();
                    $insertMappedItem->context = 'tag';
                    $insertMappedItem->itemId1 = $mappedItem->itemId1;
                    $insertMappedItem->itemId2 = $newTagId;
                    $this->info('Inserting mapped item ' . $mappedItem->itemId1 . ' - ' . $newTagId);
                    $insertMappedItem->save();
                }
            }

            // Remove the cleaned tag from tag-table
            $this->deleteTag($tag);
        }
    }

    /**
     * @param Tag $tag
     * @return void
     */
    private function deleteTag(Tag $tag)
    {
        $this->info('Deleting tag ' . $tag->id . ' (' . $tag->title . ')');
        $removeId = $tag->id;
        $tag->delete();

        // Remove mapped items
        $items = UcmItemMap::find([
            'conditions' => 'context = "tag" AND itemId2 = :item_id2:',
            'bind' => [
                'item_id2' => $removeId,
            ],
        ]);
        foreach ($items as $item)
        {
            $this->info('Deleting mapped item(s) ' . $item->itemId1 . ' (' . $item->itemId2 . ')');
            $item->delete();
        }
    }
}
