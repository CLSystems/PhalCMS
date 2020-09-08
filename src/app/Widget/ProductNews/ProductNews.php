<?php

namespace CLSystems\PhalCMS\Widget\ProductNews;

use Phalcon\Paginator\Adapter\QueryBuilder as Paginator;
use CLSystems\PhalCMS\Library\Factory;
use CLSystems\PhalCMS\Library\Widget;
use CLSystems\PhalCMS\Library\Mvc\Model\Product;
use CLSystems\PhalCMS\Library\Mvc\Model\ProductCategory;

class ProductNews extends Widget
{
    public function getContent()
    {
        $cid = $this->widget->get('params.categoryIds', []);
        $productsNum = $this->widget->get('params.productsNum', 5, 'uint');

        if (count($cid)) {
            $bindIds = [];
            $nested = new ProductCategory;

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

            $queryBuilder = Product::query()
                ->createBuilder()
                ->from(['product' => Product::class])
                ->where('product.parentId IN ({cid:array})', ['cid' => array_unique($bindIds)])
                ->andWhere('product.state = :state:', ['state' => 'P']);

            switch ($this->widget->get('params.orderBy', 'latest')) {
                case 'hits':
                    $queryBuilder->orderBy('hits desc');
                    break;

                case 'random':
                    $queryBuilder->orderBy('RAND()');
                    break;

                case 'titleAsc':
                    $queryBuilder->orderBy('title asc');
                    break;

                case 'titleDesc':
                    $queryBuilder->orderBy('title desc');
                    break;

                default:
                case 'latest':
                    $queryBuilder->orderBy('createdAt desc');
                    break;
            }

            // Init renderer
            $renderer = $this->getRenderer();
            $partial = 'Content/' . $this->getPartialId();

            if ('BlogList' === $this->widget->get('params.displayLayout', 'ProductNews')) {
                $paginator = new Paginator(
                    [
                        'builder' => $queryBuilder,
                        'limit'   => $productsNum,
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
            } else {
                $products = $queryBuilder->limit($productsNum, 0)->getQuery()->execute();

                if ($products->count()) {
                    return $renderer->getPartial($partial, ['products' => $products]);
                }
            }
        }

        return null;
    }
}
