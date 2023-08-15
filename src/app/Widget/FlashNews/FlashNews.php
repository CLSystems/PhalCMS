<?php

namespace CLSystems\PhalCMS\Widget\FlashNews;

use Phalcon\Paginator\Adapter\QueryBuilder as Paginator;
use CLSystems\PhalCMS\Lib\Factory;
use CLSystems\PhalCMS\Lib\Widget;
use CLSystems\PhalCMS\Lib\Mvc\Model\Post;
use CLSystems\PhalCMS\Lib\Mvc\Model\PostCategory;

class FlashNews extends Widget
{
    public function getContent()
    {
        $cid = $this->widget->get('params.categoryIds', []);
        $postsNum = $this->widget->get('params.postsNum', 10, 'uint');

        if (count($cid)) {
            $bindIds = [];
            $nested = new PostCategory;

            foreach ($cid as $id) {
                if ($tree = $nested->getTree((int)$id)) {
                    foreach ($tree as $node) {
                        $bindIds[] = (int)$node->id;
                    }
                }
            }

            if (empty($bindIds)) {
                return null;
            }

            $queryBuilder = Post::query()
                ->createBuilder()
                ->from(['post' => Post::class])
                ->where('post.parentId IN ({cid:array})', ['cid' => array_unique($bindIds)])
                ->andWhere('post.state = :state:', ['state' => 'P']);

            switch ($this->widget->get('params.orderBy', 'latest'))
			{
                case 'random':
					$db = Factory::getService('db');
					$randomIds = [];
					$selectedIds = $db->query("
						SELECT a.id
						FROM wgz1tq_ucm_items a
						JOIN ( SELECT id FROM
								( SELECT id
									FROM ( SELECT MIN(id) + (MAX(id) - MIN(id) + 1 - 50) * RAND() AS start FROM wgz1tq_ucm_items ) AS init
									JOIN wgz1tq_ucm_items y
									WHERE    y.id > init.start
									ORDER BY y.id
									LIMIT 500           -- Inflated to deal with gaps
								) z ORDER BY RAND()
								LIMIT 10                -- number of rows desired (change to 1 if looking for a single row)
							 ) r ON a.id = r.id;
					")->fetchAll();
					foreach ($selectedIds as $selectedId)
					{
						$randomIds[] = $selectedId['id'];
					}

					$queryBuilder
						->andWhere('post.id IN ({ids:array})', ['ids' => $randomIds])
						->orderBy('post.id asc');
                    break;

                case 'views':
                    $queryBuilder->orderBy('hits desc');
                    break;

                case 'titleAsc':
                    $queryBuilder->orderBy('title asc');
                    break;

                case 'titleDesc':
                    $queryBuilder->orderBy('title desc');
                    break;

                default:
                    $queryBuilder->orderBy('createdAt desc');
                    break;
            }

            // Init renderer
            $renderer = $this->getRenderer();
            $partial = 'Content/' . $this->getPartialId();

            if ('BlogList' === $this->widget->get('params.displayLayout', 'FlashNews'))
			{
                $paginator = new Paginator(
                    [
                        'builder' => $queryBuilder,
                        'limit'   => $postsNum,
                        'page'    => Factory::getService('request')->get('page', ['absint'], 0),
                    ]
                );

                $paginate = $paginator->paginate();

                if ($paginate->getTotalItems()) {
                    return $renderer->getPartial(
                        $partial,
                        [
                            'posts'      => $paginate->getItems(),
                            'pagination' => Factory::getService('view')->getPartial(
                                'Pagination/Pagination',
                                [
                                    'paginator' => $paginator,
                                ]
                            ),
                        ]
                    );
                }
            }
			else
			{
                $posts = $queryBuilder->limit($postsNum, 0)->getQuery()->execute();

                if ($posts->count()) {
                    return $renderer->getPartial($partial, ['posts' => $posts]);
                }
            }
        }

        return null;
    }

	public static function toSql(\Phalcon\Mvc\Model\Query\BuilderInterface $builder) : string
	{
		$data = $builder->getQuery()->getSql();

		['sql' => $sql, 'bind' => $binds, 'bindTypes' => $bindTypes] = $data;

		$finalSql = $sql;
		foreach ($binds as $name => $value) {
			$formattedValue = $value;

			if (\is_object($value)) {
				$formattedValue = (string)$value;
			}

			if (\is_string($formattedValue)) {
				$formattedValue = sprintf("'%s'", $formattedValue);
			}
			$finalSql = str_replace(":$name:", $formattedValue, $finalSql);
		}

		return $finalSql;
	}
}
