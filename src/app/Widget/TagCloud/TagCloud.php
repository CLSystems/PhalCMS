<?php

namespace CLSystems\PhalCMS\Widget\TagCloud;

use CLSystems\PhalCMS\Lib\Mvc\Model\Tag;
use CLSystems\PhalCMS\Lib\Mvc\Model\UcmItemMap;
use CLSystems\PhalCMS\Lib\Widget;

/**
 * Class Homepage
 *
 * @package CLSystems\PhalCMS\Widget\TagCloud
 */
class TagCloud extends Widget
{

    /**
     *
     * @var array Tags.
     */
    public $tags = [];

    /**
     *
     * @var integer The smallest font-size.
     */
    public $minFontSize = 12;

    /**
     *
     * @var integer The largest font-size.
     */
    public $maxFontSize = 36;

    /**
     *
     * @var integer The smallest count.
     */
    private $minWeight = 1;

    /**
     *
     * @var integer The largest count.
     */
    private $maxWeight = 1;

    public function getContent()
    {
        $tagsNum = $this->widget->get('params.tagsNum', 10, 'uint');

        // Fetch the most used tags
//		$tags = UcmItemMap::find([
//			'conditions' => "context = 'tag'",
//			'columns'    => 'COUNT(*) as tagCount, itemId2',
//			'group'      => 'itemId2',
//			'order'      => 'tagCount DESC',
//			'limit'      => $tagsNum,
//		]);

        $queryBuilder = UcmItemMap::query()
            ->createBuilder()
            ->columns('COUNT(*) as tagCount, itemId2')
            ->from(['item_map' => UcmItemMap::class])
            ->where("context = 'tag'")
            ->groupBy('itemId2')
            ->orderBy('tagCount DESC');
        $this->tags = $queryBuilder->limit($tagsNum, 0)->getQuery()->execute()->toArray();

        if (true === empty($this->tags))
        {
            return null;
        }

        foreach ($this->tags as &$tag)
        {
            $tag['tag'] = Tag::query()
                ->where('id = ' . $tag['itemId2'])
                ->execute()->toArray();
        }

//		$tagIds = array_column($tags, 'itemId2');
//
//		$tagNames = Tag::query()
//			->where('id IN (' . implode(',', $tagIds) . ')')
//			->execute();
//
//		if (true === empty($tagNames))
//		{
//			return null;
//		}

        // Init renderer
        $renderer = $this->getRenderer();
        $partial = 'Content/' . $this->getPartialId();

        if (count($this->tags) > 0)
        {
            $this->calculateMinMaxWeight();
            $this->calculateFontSizes();
            return $renderer->getPartial($partial, ['tags' => $this->tags]);
        }

        return null;
    }


    private function calculateFontSizes()
    {
        foreach ($this->tags as &$conf) {
            $conf['font-size'] = $this->calculateFontSizeByWeight($conf['tagCount']);
        }
    }

    private function calculateFontSizeByWeight($weight) : float
    {
        $difference = $this->maxWeight - $this->minWeight;
        if ($this->maxWeight == $this->minWeight) {
            $difference = 1;
        }
        return round(((($weight - $this->minWeight) * ($this->maxFontSize - $this->minFontSize)) / ($difference)) + $this->minFontSize);
    }

    private function calculateMinMaxWeight()
    {
        foreach ($this->tags as $conf) {
            if ($this->minWeight > $conf['tagCount']) {
                $this->minWeight = $conf['tagCount'];
            }
            if ($this->maxWeight < $conf['tagCount']) {
                $this->maxWeight = $conf['tagCount'];
            }
        }
    }

}
