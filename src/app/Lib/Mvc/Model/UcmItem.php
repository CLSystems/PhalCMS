<?php

namespace CLSystems\PhalCMS\Lib\Mvc\Model;

use Phalcon\Db\Adapter\Pdo\Mysql;
use CLSystems\PhalCMS\Lib\Helper\Event as EventHelper;
use CLSystems\PhalCMS\Lib\Helper\UcmItem as UcmItemHelper;
use CLSystems\PhalCMS\Lib\Helper\StringHelper;
use CLSystems\PhalCMS\Lib\Helper\Uri;
use CLSystems\PhalCMS\Lib\Helper\Language;
use CLSystems\PhalCMS\Lib\Form\FormsManager;
use CLSystems\PhalCMS\Lib\Form\Form;
use CLSystems\PhalCMS\Lib\Factory;
use CLSystems\Php\Registry;
use CLSystems\Php\Filter;
use Phalcon\Mvc\Model\ResultsetInterface;
use stdClass;

class UcmItem extends ModelBase
{
	/**
	 * @var integer
	 */
	public $id;

	/**
	 * @var integer
	 */
	public $parentId;

	/**
	 * @var string
	 */
	public $context;

	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var string
	 */
	public $route;

	/**
	 * @var integer
	 */
	public $state;

	/**
	 * @var string
	 */
	public $image;

	/**
	 *
	 * @var string
	 */
	public $summary;

	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var string
	 */
	public $createdAt;

	/**
	 * @var integer
	 */
	public $createdBy = 0;

	/**
	 *
	 * @var string
	 */
	public $modifiedAt = null;

	/**
	 * @var integer
	 */
	public $modifiedBy = 0;

	/**
	 * @var string
	 */
	public $checkedAt = null;

	/**
	 * @var integer
	 */
	public $checkedBy = 0;

	/**
	 * @var string
	 */
	public $metaTitle = '';

	/**
	 * @var string
	 */
	public $metaKeys = '';

	/**
	 * @var string
	 */
	public $metaDesc = '';

	/**
	 *
	 * @var string[]
	 */
	protected $params;

	/**
	 * @var integer
	 */
	public $ordering;

	/**
	 * @var integer
	 */
	public $lft = 0;

	/**
	 * @var integer
	 */
	public $rgt = 1;

	/**
	 * @var integer
	 */
	public $level = 0;

	/**
	 * @var integer
	 */

	public $hits = 0;

	/**
	 * @var boolean
	 */

	public $hasRoute = false;

	/**
	 * @var string
	 */

	protected $titleField = 'title';

	/**
	 * @var boolean
	 */

	protected $standardMetadata = true;

	/** @var array */
	protected $fieldsData = [];

	/** @var array */
	protected $translationsFieldsData = [];

	public function getParent()
	{
		return $this->getRelated('parent');
	}

	/**
	 * Initialize method for model.
	 */

	public function initialize()
	{
		$this->setSource('ucm_items');
	}

	public function getOrderFields()
	{
		return [
			($this instanceof Nested ? 'lft' : 'ordering'),
			'id',
			'title',
			'description',
			'createdAt',
			'createdBy',
		];
	}

	public function getFilterForm()
	{
		return new Form('FilterForm', __DIR__ . '/Form/UcmItem/Filter.php');
	}

	public function getFormsManager()
	{
		$formsManager = new FormsManager;
		$formsManager->set('general', new Form('FormData', __DIR__ . '/Form/UcmItem/General.php'));
		$formsManager->set('aside', new Form('FormData', __DIR__ . '/Form/UcmItem/Aside.php'));
		$formsManager->set('metadata', new Form('FormData', __DIR__ . '/Form/Metadata/Metadata.php'));

		return $formsManager;
	}

	public function isContextPrefix($prefix)
	{
		return preg_match('/^' . $prefix . '/', $this->context) ? true : false;
	}

	public function isContextSuffix($suffix)
	{
		return preg_match('/-' . $suffix . '$/', $this->context) ? true : false;
	}

	public function prepareFormsManager(FormsManager $formsManager)
	{
		$asideForm = $formsManager->get('aside');

		if ($this->id
			&& $asideForm->has('tags')
			&& ($tags = $this->getRelated('tags'))->count()
		)
		{
			$tagIds = [];

			/**
			 * @var Tag $tag
			 */
			foreach ($tags as $tag)
			{
				$tagIds[] = (int)$tag->id;
			}

			$asideForm->getField('tags')->setValue($tagIds);
		}
	}

	public function controllerDoBeforeSave(&$validData)
	{
		if ($this->id == $this->parentId)
		{
			$this->parentId = 0;
		}

		parent::beforeSave();
	}

	public function beforeSave()
	{
		if (is_array($this->image))
		{
			$this->image = json_encode($this->image);
		}

		parent::beforeSave();
	}

	public function controllerDoAfterSave($validData)
	{
		$isCategory = $this->isContextSuffix('category');
		$modelsManager = $this->getModelsManager();
		$dbo = Factory::getService('db');
		$source = $this->getSource();

		if ($this->hasRoute && empty($this->route))
		{
			if ($this->parentId && ($parent = self::findFirst('id = ' . (int)$this->parentId)))
			{
				$route = Filter::toPath($parent->route . '/' . $this->title);
			}
			else
			{
				$prefix = $isCategory ? '' : $this->context . '/';
				$route = $prefix . Filter::toSlug($this->title);
			}

			// Don't use Phalcon Update or Save.
			// Because Phalcon has conflicts with array references (hasBelongsTo, hasMany...)
			$dbo->query('UPDATE `' . $source . '` SET `route` = :route WHERE `id` = :id', [
				'route' => $route,
				'id'    => (int)$this->id,
			]);
		}

		$modelsManager->executeQuery(
			'DELETE FROM ' . UcmItemMap::class . ' WHERE itemId1 = :itemId1: AND context = :context:',
			[
				'itemId1' => $this->id,
				'context' => 'tag',
			]
		);

		if (!empty($validData['tags']))
		{
			foreach (Filter::clean($validData['tags'], 'unique') as $tagId)
			{
				$tagId = (int)$tagId;

				if ($tagId > 0)
				{
					(new UcmItemMap)
						->assign(
							[
								'itemId1' => $this->id,
								'itemId2' => $tagId,
								'context' => 'tag',
							]
						)->save();
				}
			}
		}

		$context = UcmItemHelper::prepareContext($this->context);
		EventHelper::trigger('afterSaveUcm' . $context, [$this, $validData], ['Cms']);
	}

	public function delete() : bool
	{
		if ($result = parent::delete())
		{
			$context = UcmItemHelper::prepareContext($this->context);
			$previousData = $this->toArray();
			if ('cli' !== PHP_SAPI)
			{
				EventHelper::trigger('afterDeleteUcm' . $context, [$previousData], ['Cms']);
			}
		}

		return $result;
	}

	public function hit($pk = null)
	{
		if (null === $pk)
		{
			$pk = $this->id;
		}

		$session = Factory::getService('session');
		$hitsKey = $this->context . '.hits';
		$hits = $session->get($hitsKey, []);

		if (!isset($hits[$pk]))
		{
			$hits[$pk] = 1;
			$session->set($hitsKey, $hits);

			/** @var Mysql $db */
			$db = $this->getDI()->get('db');
			$db->execute('UPDATE ' . $this->getSource() . ' SET hits = hits + 1 WHERE id = :id', [
				'id' => $pk,
			]);

			if ($pk == $this->id)
			{
				$this->hits++;
			}
		}

		return $this;
	}

	public function t($field)
	{
		if (Uri::isClient('administrator'))
		{
			return $this->{$field};
		}

		static $translated = [];
		$k = $this->id . $field;

		if (!isset($translated[$k]))
		{
			$translationData = parent::getTranslations();

			if (isset($translationData[$field]))
			{
				$value = $translationData[$field];

				switch ($field)
				{
					case 'route':
						if (empty($value))
						{
							$value = $this->route;
						}
					break;

					case 'image':
						if (empty($value) || in_array($value, ['[]', '{}']))
						{
							$value = $this->image;
						}
					break;

					case 'params':
						$tranValue = $value;
						$value = new Registry($this->{$field});
						$value->merge($tranValue);
					break;
				}
			}
			else
			{
				$value = $this->{$field};
			}

			$translated[$k] = $value;
		}

		return $translated[$k];
	}

	public function getLink()
	{
		return Uri::route($this->t('route'));
	}

	public function summary($fallbackDescStrLen = 100)
	{
		$summary = trim($this->t('summary'));

		if (empty($summary))
		{
			$summary = StringHelper::truncate($this->t('description'), $fallbackDescStrLen);
		}

		return $summary;
	}

	public function rating()
	{
		$rating =  Rating::findFirst([
			'conditions' => 'externalMerchantId = :external_merchant_id: AND sourceId = :source_id: ',
			'bind'       => [
				'external_merchant_id' => $this->externalMerchantId,
				'source_id'            => $this->sourceId,
			],
		]);

		return $rating;
	}

	public function vouchers()
	{
		$vouchers = [];
		$voucherIds = [];
		if ((int)$this->parentId === 117) // Merken
		{
			$vouchers = $this->find([
				'conditions' => 'externalMerchantId = :external_merchant_id: AND sourceId = :source_id: AND parentId = 118', // Kortingscodes
				'bind' => [
					'external_merchant_id' => $this->externalMerchantId,
					'source_id'            => $this->sourceId,
				],
			])->toArray();

			if (false === empty($vouchers))
			{
				$voucherIds = array_column($vouchers, 'id');
			}
//			$ads = Ad::find([
//				'conditions' => 'externalMerchantId = :external_merchant_id: AND sourceId = :source_id:',
//				'bind' => [
//					'external_merchant_id' => $this->externalMerchantId,
//					'source_id'            => $this->sourceId,
//				],
//			])->toArray();

//			$vouchers += $ads;
		}


		// Set one if there is a prefUrl
//		if (0 === count($vouchers) && (int)$this->parentId === 117 && 0 < strlen($this->prefUrl))
		if ((int)$this->parentId === 117 && 0 < strlen($this->prefUrl))
		{
			$voucherIds[] = $this->id;
			$vouchers[] = $this->find([
				'conditions' => 'externalId = :external_id: AND sourceId = :source_id:',
				'bind' => [
					'external_id' => $this->externalMerchantId,
					'source_id'   => $this->sourceId,
				],
			])->toArray();
		}

		// If both still empty, try to find an alternative
		if (true === empty($voucherIds) && true === empty($vouchers))
		{
			$path = $this->route;
			$parts = explode('-', $path);
			foreach ($parts as $key => $part)
			{
				if (false !== stristr($part, 'kortingen'))
				{
					unset($parts[$key]);
				}
				if (false !== stristr($part, 'kortingscodes'))
				{
					unset($parts[$key]);
				}
				if (false !== stristr($part, 'aanbiedingen'))
				{
					unset($parts[$key]);
				}
				if (true === is_numeric($part))
				{
					unset($parts[$key]);
				}
			}
			$newRoute = implode('-', $parts);
			$post = UcmItem::findFirst([
				'conditions' => "route LIKE '" . $newRoute . "%' AND route != '" . $this->route . "' AND LENGTH(prefUrl) >= 1 AND state = 'P'",
			]);
			if (null !== $post)
			{
				$voucherIds[] = $post->id;
				$vouchers[] = $post->toArray();
			}
		}

		if (false === empty($vouchers))
		{
//			$voucherIds += array_column($vouchers, 'id');
			$vouchers = $this->find([
				'conditions' => 'id IN (' . implode(',', $voucherIds) . ')',
			]);
		}

		return $vouchers;
	}

	public function ads()
	{
		$ads = [];
		if ((int)$this->parentId === 117) // Merken
		{
			$ads = Ad::find([
				'conditions' => 'externalMerchantId = :external_merchant_id: AND sourceId = :source_id:',
				'bind' => [
					'external_merchant_id' => $this->externalMerchantId,
					'source_id'            => $this->sourceId,
				],
			])->toArray();
		}

		// Nothing found, set at least one if there is a prefUrl
		if (0 === count($ads) && (int)$this->parentId === 117 && 0 < strlen($this->prefUrl))
		{
			$ads = $this->find([
				'conditions' => 'externalId = :external_id: AND sourceId = :source_id:',
				'bind' => [
					'external_id' => $this->externalMerchantId,
					'source_id'   => $this->sourceId,
				],
			])->toArray();
		}

		if (false === empty($ads))
		{
			$adIds = array_column($ads, 'id', 'id');
			$ads = Ad::find([
				'conditions' => 'id IN (' . implode(',', $adIds) . ')',
			]);
		}

		return $ads;
	}

	public function getFieldsData()
	{
		if (empty($this->fieldsData) && $this->id)
		{
			/** @var \Phalcon\Mvc\Model\Resultset\Simple $values */
			$values = $this->getModelsManager()
				->createBuilder()
				->columns('name, fieldId, value')
				->from(['fieldValue' => UcmFieldValue::class])
				->innerJoin(UcmField::class, 'field.id = fieldValue.fieldId', 'field')
				->where('field.context = :context:', ['context' => $this->context])
				->andWhere('fieldValue.itemId = :thisId:', ['thisId' => $this->id])
				->getQuery()
				->execute();

			if (0 < $values->count())
			{
				$fields = [];

				foreach ($values as $value)
				{
					$this->fieldsData[$value->name] = $value->value;
					$fields[$value->fieldId] = $value->name;
				}

				if (Language::isMultilingual())
				{
					$language = Language::getLanguageQuery();
					$isSite = Uri::isClient('site');

					if (Uri::isClient('site') && '*' === $language)
					{
						return $this->fieldsData;
					}

					// Find translation fields values
					$query = $this->getModelsManager()
						->createBuilder()
						->from(Translation::class)
						->columns('translationId, translatedValue')
						->where('translationId LIKE :translationId:', [
							'translationId' => ($isSite ? $language : '%') . '.ucm_field_values.fieldId=%,itemId=' . $this->id . '%',
						]);

					$trans = $query->getQuery()->execute();

					if ($trans->count())
					{
						foreach ($trans as $tran)
						{
							$parts = explode('.', $tran->translationId);
							$langCode = $parts[0];
							$refKey = explode(',', $parts[2], 2);
							$fieldId = str_replace('fieldId=', '', $refKey[0]);

							if (isset($fields[$fieldId]))
							{
								$fieldName = $fields[$fieldId];

								if ($isSite)
								{
									$this->translationsFieldsData[$fieldName] = $tran->translatedValue;
								}
								else
								{
									$this->translationsFieldsData[$fieldName][$langCode] = $tran->translatedValue;
								}
							}
						}
					}
				}
			}
		}

		return $this->fieldsData;
	}

	public function getTranslationsFieldsData()
	{
		return $this->translationsFieldsData;
	}

	public function getFieldValue($fieldName, $defaultValue = null)
	{
		$value = isset($this->translationsFieldsData[$fieldName])
			? $this->translationsFieldsData[$fieldName]
			: (isset($this->fieldsData[$fieldName]) ? $this->fieldsData[$fieldName] : $defaultValue);

		if (strpos($value, '{') === 0 || strpos($value, '[') === 0)
		{
			$value = implode(', ', json_decode($value, true) ?: []);
		}

		return $value;
	}

	public function copy()
	{
		if ($result = parent::copy())
		{
			$fieldsValues = UcmFieldValue::find(
				[
					'conditions' => 'itemId = :itemId:',
					'bind'       => [
						'itemId' => $this->id,
					],
				]
			);

			if ($fieldsValues->count())
			{
				$values = '';
				$bind = [];

				foreach ($fieldsValues as $fieldsValue)
				{
					$values .= '(?, ?, ?),';
					$bind[] = $fieldsValue->fieldId;
					$bind[] = $result->id;
					$bind[] = $fieldsValue->value;
				}

				$source = $this->getModelsManager()->getModelPrefix() . 'ucm_field_values';
				$this->getDI()->get('db')->execute('INSERT INTO ' . $source . ' (fieldId, itemId, value) VALUES ' . rtrim($values, ','), $bind);
			}
		}

		return $result;
	}

	protected function afterDelete()
	{
		parent::afterDelete();
		$source = $this->getModelsManager()->getModelPrefix() . 'ucm_field_values';
		$this->getDI()->get('db')->execute('DELETE FROM ' . $source . ' WHERE itemId LIKE :itemId', [
			'itemId' => $this->id,
		]);
	}

	public function getParams()
	{
		if (!($this->params instanceof Registry))
		{
			$this->params = new Registry($this->params);
		}

		return $this->params;
	}
}
