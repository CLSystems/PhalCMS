<?php

namespace CLSystems\PhalCMS\Tasks;

ini_set('memory_limit', '4G');
setlocale(LC_ALL, 'nl_NL@euro', 'nl_NL', 'nl', 'dutch');

include_once __DIR__ . '/../../vendor/clsystems/ibm-translator-client/src/Translator/Factory.php';

use CLSystems\IBMWatson\Translator\Translator\Factory;
use CLSystems\PhalCMS\Lib\Helper\Date;
use CLSystems\PhalCMS\Lib\Helper\Encoding;
use CLSystems\PhalCMS\Lib\Mvc\Model\ApiCallLog;
use CLSystems\PhalCMS\Lib\Mvc\Model\Media;
use CLSystems\PhalCMS\Lib\Mvc\Model\ModelBase;
use CLSystems\PhalCMS\Lib\Mvc\Model\Post;
use CLSystems\PhalCMS\Lib\Mvc\Model\Tag;
use CLSystems\PhalCMS\Lib\Mvc\Model\UcmField;
use CLSystems\PhalCMS\Lib\Mvc\Model\UcmFieldValue;
use CLSystems\PhalCMS\Lib\Mvc\Model\UcmItemMap;
use CLSystems\Php\Filter;
use Exception;
use Onnov\DetectEncoding\EncodingDetector;
use Phalcon\Cli\Task;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\ModelInterface;
use voku\helper\UTF8;

class MainTask extends Task
{
	/**
	 * @const integer
	 */
	public const SOURCE_ID_DAISYCON     = 1;
	public const SOURCE_ID_TRADETRACKER = 2;
	public const SOURCE_ID_ADMITADS     = 3;
	public const SOURCE_ID_TRADEDOUBLER = 4;
	public const SOURCE_ID_SHAREASALE   = 5;
	public const SOURCE_ID_CLICKDEALER  = 6;
	public const SOURCE_ID_ADCELL       = 7;
	public const SOURCE_ID_YIELDKIT     = 8;
	public const SOURCE_ID_BRANDREWARD  = 9;
	public const SOURCE_ID_AWIN         = 10;
	public const SOURCE_ID_ZANOX        = 11;
	public const SOURCE_ID_RAKUTEN      = 12;
	public const SOURCE_ID_SKIMLINKS    = 13;
	public const SOURCE_ID_ADCOCKTAIL   = 14;
	public const SOURCE_ID_IMPACT       = 15;
    public const SOURCE_ID_LINKBUX      = 16;
    public const SOURCE_ID_PEPPERJAM    = 17;
    public const SOURCE_ID_MONETAG      = 18;
	public const SOURCE_ID_ECOMNIA      = 19;
	public const SOURCE_ID_GEKKO        = 20;

	/**
	 * @var mixed|null
	 */
	protected $insertedBrands;

	/**
	 * @var mixed|null
	 */
	protected $updatedBrands;

	/**
	 * @var int
	 */
	protected $addedCount = 0;

	/**
	 * @var int
	 */
	protected $updatedCount = 0;

	/**
	 * @var int
	 */
	protected $insertedCount = 0;

	/**
	 * @var int
	 */
	protected $skippedCount = 0;

	/**
	 * Allowed time before an image fetch will time out
	 *
	 * @const int
	 */
	const TIMEOUT_MILLISECONDS = 2000;

	/** @var ModelBase | null $model */
	public $model = null;

	/** @var string */
	public $dataKey = null;

	/** @var string $uploadPath */
	protected $uploadPath = BASE_PATH . '/public/upload';

	/**
	 * @param string $model
	 * @return mixed
	 * @throws Exception
	 */
	public function setupModel(string $model)
	{
		$this->model = $model;
		if (is_string($this->model))
		{
			$modelClass = 'CLSystems\\PhalCMS\\Lib\\Mvc\\Model\\' . $this->model;

			if (class_exists($modelClass))
			{
				$this->model = new $modelClass;
			}
		}

		if (!$this->model instanceof Model)
		{
			throw new Exception('Could not instantiate ' . $this->model);
		}

		$this->dataKey = $this->model->getIgnorePrefixModelName();

		if ($context = $this->dispatcher->getParam('context', ['trim', 'string']))
		{
			$this->dataKey .= $context;
		}
		return $this->model;
	}

    public function mainAction()
    {
        echo 'This is the default task and the default action' . PHP_EOL;
    }

	public function info(string $message)
	{
		print_r($message . PHP_EOL);
	}

	protected static function getBlackList() : array
	{
		return [
			'.com/nl', '.nl/com', '.co.uk', '.nl', '.be', '.de', '.dk', '.no', '.fi', '.com', '.fr', '.mx', '.se', '.eu', '.net', '.ee', '.pl', '.uk',
			' WW', 'Many', 'MANY', 'Geos', 'Geo s', 'GEOS', 'GEO S', 'GEOs', 'GEO s', 'GEO\'s', 'GEO\'s', 'Many', 'Many GEOs', 'Many Geos', '-Many-GEOs',
			'[WEB]', '[MOB]', '[WEB+MOB]', '[', ']', '(', ')', '_', '!', ':', '+', '&', '/', '--',
			'cps', 'CPS', 'cpl', 'CPL', 'SOI', 'DOI', 'MOB', 'CPP', 'CPA', 'LLC',
			'(NL)', '(BE)', '(DE)', '(DK)', '(NO)', '(FI)', '(UK)', '(INT)', '(AUS)', '(BR)', '(SE)', '(DACH)', '(US)', '(EU)',
			' -NL', ' -FL', ' -FR',
			' NL', ' BE', ' DE', ' DK', ' NO', ' FI', ' UK', ' INT', ' AUS', ' BR', ' SE', ' DACH', ' US', ' MX', ' CA', ' FR', ' GG', ' CC', ' UA', ' AU', ' ES', ' EUR',
			' EU', ' MY', ' TH',  ' ENG', ' AR', ' KSA',
			' AT', ' ES', ' -FR', ' -NL',
			'Affiliate Program', 'Multi-language', 'Multiple', 'Esprit', '-promotie',
			'German', 'Germany', ' - Familyblend', ' - FamilyBlend',
		];
	}

	/**
	 * @param int $sourceId
	 * @param string $uri
	 * @param string $script
	 * @return void
	 */
	protected function logApiCall(int $sourceId, string $uri, string $script) : void
	{
//        $prefix = $this->model->getModelsManager()->getModelPrefix();
		// Check if record exists
		$params = [
			'sourceId' => $sourceId,
			'date'     => date('Y-m-d'),
			'uri'      => $uri,
			'script'   => $script,
		];
		$paramString = '';
		foreach ($params as $key => $value)
		{
			$paramString .= "{$key} = :{$key}: AND ";
		}

		$row = ApiCallLog::findFirst([
			substr($paramString, 0, -5),
			'bind' => $params,
		]);

		if (true === empty($row))
		{
			// Insert
			$model = new ApiCallLog();
			$params['count'] = 1;
			$model->assign($params);
			$model->save();
		}
		else
		{
			// Update
			//$params['count'] = $row->count + 1;
			$row->assign(
				['count' => $row->count + 1]
			);
			$row->save();
		}
	}

	/**
	 * @param string $name
	 * @return string
	 */
	protected function cleanProgramName(string $name) : string
	{
		$string = str_replace(self::getBlackList(), ' ', $name);
		$string = preg_replace(['~[^a-zA-Z0-9\\-%_]~', '~[\-]{2,}~'], [' ', '-'], html_entity_decode($string, null, 'UTF-8'));
		$string = preg_replace(['~[ ]{2,}~'], [' '], html_entity_decode($string, null, 'UTF-8'));

		return trim($string, '- ');
	}

	/**
	 * @param string $string
	 * @param array $stop_words
	 * @param int $max_count
	 * @return string
	 */
	protected function extractWords(string $string, array $stop_words, int $max_count = 5): string
	{
//		$string = preg_replace('/\s\s+/i', ' ', $string);
		$string = trim($string); // trim the string
		$string = preg_replace('/[^a-zA-Z -]/', '', $string);
		$string = strip_tags(html_entity_decode($string));
		$string = strtolower($string); // make it lowercase

		preg_match_all('/\b.*?\b/i', $string, $matchWords);
		$matchWords = $matchWords[0];

		foreach ($matchWords as $key => $item)
		{
			if ($item == '' || in_array(strtolower($item), $stop_words) || strlen($item) <= 3)
			{
				unset($matchWords[$key]);
			}
		}

		$word_count = str_word_count(implode(' ', $matchWords), 1);
		$frequency = array_count_values($word_count);
		arsort($frequency);

		$keywords = array_slice(array_flip($frequency), 0, $max_count);
		return implode(',', $keywords);
	}

	/**
	 * @param string $description
	 * @param string $targetLanguage
	 * @return array
	 */
	public function generateTranslations(string $description, string $targetLanguage = 'nl'): array
	{
		// Early out
		if (true === empty($description))
		{
			return [
				'description' => '',
				'summary'     => '',
				'metaDesc'    => '',
				'metaKeys'    => '',
			];
		}

        $description = self::getTranslation($description, $targetLanguage);
        // Fix the encoding
        $description = UTF8::cleanup($description);
		$stripped = strip_tags(html_entity_decode($description), '<p><br><br/><br /><b><strong>');
		$firstDot = strpos($stripped, '.');
		if (false === $firstDot)
		{
			$firstDot = 99;
		}
		$summary = substr(strip_tags($stripped), 0, $firstDot+1);
		$metaDesc = substr(strip_tags($stripped), 0, (($firstDot+1) > 250 ? 250 : ($firstDot+1)));

		$metaKeys = null;
		if (true === is_file(__DIR__ . '/common/stop_words_' . $targetLanguage . '.txt'))
		{
			/**
			 * @var array $stopWords
			 */
			$stopWords = file(__DIR__ . '/common/stop_words_' . $targetLanguage . '.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			$metaKeys = self::extractWords(strip_tags(html_entity_decode($stripped)), $stopWords);
		}

		return [
			'description' => $stripped,
			'summary'     => $summary,
			'metaDesc'    => $metaDesc,
			'metaKeys'    => $metaKeys ?? '',
		];
	}

	/**
	 * @param string $description
	 * @param string $targetLanguage
	 * @return string|null
	 */
	public function getTranslation(string $description, string $targetLanguage = 'nl') : ?string
	{
//		if ($targetLanguage === $this->detectLanguage($description))
//		{
//			return $description;
//		}
		// Try DeepL
		$translation = $this->getDeepLTranslation($description, $targetLanguage);

		if (true === empty($translation))
		{
			// Try IBM
			$translation = $this->getIbmTranslation($description, $targetLanguage);
		}

		if (true === empty($translation))
		{
			// Try Google
			$translation = $this->getGoogleTranslation($description, $targetLanguage);
		}

		return $translation ?? $description;
	}

	/**
	 * @param string $description
	 * @return string
	 */
	public function detectLanguage(string $description): string
	{
		$apiKey = 'RbHPoMOKlzsCgZ-Q_VAnVA5gO8x4FNYEN_FnSwPTAeDb';
		$url = 'https://api.eu-de.language-translator.watson.cloud.ibm.com/instances/8f56a089-1d16-4e75-a2d2-4eae67dbedf6';

//		{
//			"apikey": "RbHPoMOKlzsCgZ-Q_VAnVA5gO8x4FNYEN_FnSwPTAeDb",
//			"iam_apikey_description": "Auto-generated for key 88af66bc-8b01-4fbd-96a7-ff60a98bad31",
//			"iam_apikey_name": "Auto-generated service credentials",
//			"iam_role_crn": "crn:v1:bluemix:public:iam::::serviceRole:Manager",
//			"iam_serviceid_crn": "crn:v1:bluemix:public:iam-identity::a/bcc12c635a0c4587aee7fb280029ead8::serviceid:ServiceId-b53afb61-71ae-40d6-b0ad-d35a434cbe2f",
//			"url": "https://api.eu-de.language-translator.watson.cloud.ibm.com/instances/8f56a089-1d16-4e75-a2d2-4eae67dbedf6"
//		}

		$translator = Factory::getTranslator($apiKey, $url);
		return $translator->identifyLanguage($description);
	}

	/**
	 * @param string $description
	 * @param string $targetLanguage
	 * @return string|null
	 */
	protected function getDeepLTranslation(string $description, string $targetLanguage = 'nl') : ?string
	{
//		https://api-free.deepl.com/v2/translate?auth_key=edf228a5-3cc7-b9a2-2262-d90d6cf8fa24%3Afx&text=This%20is%20a%20text%20to%20test%20the%20translation%20tools%20that%20will%20be%20needed%20for%20the%20website.%0AFor%20every%20line%20there%20should%20be%20a%20separate%20translation%2C%20which%20is%20not%20needed.&target_lang=de&split_sentences=0&formality=less

		$apiKey = 'edf228a5-3cc7-b9a2-2262-d90d6cf8fa24:fx';
		$url = 'https://api-free.deepl.com/v2/translate?auth_key=' . $apiKey
			. '&text=' . urlencode(strip_tags(html_entity_decode($description)))
			. '&target_lang=' . strtoupper($targetLanguage)
//			. '&split_sentences=0'
			. '&formality=less';

		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);

		$response = curl_exec($handle);
		$info = curl_getinfo($handle);
		$responseDecoded = json_decode($response, true);
		curl_close($handle);

		if ($info['http_code'] !== 200)
		{
//			$this->info('DeepL - ' . $info['http_code'] . ' - ' . $response);
			return null;
		}

		$translation = null;
		if (false === empty($responseDecoded['translations']))
		{
			foreach ($responseDecoded['translations'] as $translation)
			{
				if (false === empty($translation['text']))
				{
					$translation = (string)$translation['text'];
				}
			}
			// Avoid API limit
			usleep(rand(500000, 1000000));
			return $translation;
		}
		else
		{
//			$this->info('DeepL - ' . var_export($response, true));
			return null;
		}
	}


	/**
	 * Fetch translation from IBM Watson Translate
	 *
	 * @param string $description
	 * @param string $targetLanguage
	 * @return string|null
	 */
	public function getIbmTranslation(string $description, string $targetLanguage = 'nl') : ?string
	{
		if (true === empty($description))
		{
			return $description;
		}

		$apiKey = 'RbHPoMOKlzsCgZ-Q_VAnVA5gO8x4FNYEN_FnSwPTAeDb';
		$url = 'https://api.eu-de.language-translator.watson.cloud.ibm.com/instances/8f56a089-1d16-4e75-a2d2-4eae67dbedf6';

		$translator = Factory::getTranslator($apiKey, $url);
		try
		{
			$orgLanguage = $translator->identifyLanguage($description);
			if ($orgLanguage === $targetLanguage)
			{
				return $description;
			}
			if ('en' !== $orgLanguage)
			{
				$description = $translator->simpleTranslate($description, 'en');
			}
			$translation = $translator->simpleTranslate($description, $targetLanguage);
		}
		catch (Exception $exception)
		{
			// echo 'ERROR getIbmTranslation: ' . $exception->getCode() . PHP_EOL;
			// echo $exception->getMessage() . PHP_EOL;
			// if (false === empty($exception->getPrevious())) {
			// 	echo '- PREVIOUS getTranslation: ' . $exception->getPrevious()->getMessage() . PHP_EOL;
			// }
			return null;
		}
		// Avoid API limit
		usleep(1500000);

		return $translation;
	}

	/**
	 * Fetch translation from Google Translate
	 *
	 * @param string $description
	 * @param string $targetLanguage
	 * @return string|null
	 */
	protected function getGoogleTranslation(string $description, string $targetLanguage = 'nl') : ?string
	{
		// Avoid API limit
//		usleep(1500000);

		$useragents = [
			'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
			'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0',
			'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:53.0) Gecko/20100101 Firefox/53.0',
			'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)',
			'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36',
			'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.79 Safari/537.36 Edge/14.14393',
			'Mozilla/5.0 (Linux; Android 7.0; HTC 10 Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.83 Mobile Safari/537.36',
			'Mozilla/5.0 (iPad; CPU OS 8_4_1 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12H321 Safari/600.1.4',
			'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.141 Safari/537.36',
			'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36 Edge/12.246',
			'Mozilla/5.0 (X11; CrOS x86_64 8172.45.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.64 Safari/537.36',
			'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/601.3.9 (KHTML, like Gecko) Version/9.0.2 Safari/601.3.9',
			'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.111 Safari/537.36',
			'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:15.0) Gecko/20100101 Firefox/15.0.1',
			'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36',
			'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
			'Opera/9.80 (Windows NT 6.2; Win64; x64) Presto/2.12 Version/12.16',
			'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko',
			'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.48 Safari/537.36',
		];
		$randomKey = rand(0, count($useragents)-1);

		$url = 'https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=' . $targetLanguage . '&dt=t&q=' . urlencode($description);

		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
//		curl_setopt($handle, CURLOPT_SSL_VERIFYSTATUS, false);
		curl_setopt($handle, CURLOPT_USERAGENT, $useragents[$randomKey]);
		curl_setopt($handle, CURLOPT_REFERER, 'https://google.nl');
		touch('/downloads/cookies/translate_cookie.txt');
		curl_setopt($handle, CURLOPT_COOKIEFILE, '/downloads/cookies/translate_cookie.txt');
		curl_setopt($handle, CURLOPT_COOKIEJAR, '/downloads/cookies/translate_cookie.txt');
		$response = curl_exec($handle);
//		$this->info('GT - ' . var_export($response, true));
		$responseDecoded = json_decode($response, true);
		curl_close($handle);

		$translation = null;
		if (false === empty($responseDecoded) && null !== $responseDecoded[0])
		{
			foreach ($responseDecoded[0] as $translationLine)
			{
				if (false === empty($translationLine))
				{
					$translation .= $translationLine[0];
				}
			}
		}
		else
		{
//			$this->info('GT - ' . var_export($response, true));
			return null;
		}
		// Avoid API limit
		usleep(rand(1500000, 2000000));

		return $translation ?? $description;
	}

	/**
	 * @param mixed $externalId
	 * @param int $sourceId
	 * @return ModelInterface|null
	 */
	protected function getBrand($externalId, int $sourceId): ?ModelInterface
	{
		return Post::findFirst([
			'conditions' => 'context = :context: AND parentId = :parentId: AND externalId = :externalId: AND sourceId = :sourceId:',
			'bind'       => [
				'context'    => 'post',
				'parentId'   => 117, // Merken
				'externalId' => $externalId,
				'sourceId'   => $sourceId,
			],
		]);
	}

	/**
	 * @param $prefUrl
	 * @param $externalId
	 * @param int $sourceId
	 * @return bool
	 */
	protected function checkPrefUrlChanged($prefUrl, $externalId, int $sourceId): bool
	{
		/* @var $brand Post */
		$brand = Post::find([
			'conditions' => "context = :context: 
				AND parentId = :parentId: 
				AND externalId = :externalId: 
				AND sourceId = :sourceId: 
				AND prefUrl LIKE '" . $prefUrl . "%'
				",
			'bind'       => [
				'context'    => 'post',
				'parentId'   => 117, // Merken
				'externalId' => $externalId,
				'sourceId'   => $sourceId,
			],
		]);

		if (false === empty($brand))
		{
			return false;
		}
		return true;
	}

	/**
	 * @param mixed $externalId
	 * @param int $sourceId
	 * @return bool
	 */
	protected function checkBrandExists($externalId, int $sourceId): bool
	{
		$localBrand = Post::findFirst([
			'conditions' => 'context = :context: AND parentId = :parentId: AND externalId = :externalId: AND sourceId = :sourceId:',
			'bind'       => [
				'context'    => 'post',
				'parentId'   => 117, // Merken
				'externalId' => $externalId,
				'sourceId'   => $sourceId,
			],
		]);
		if (null === $localBrand)
		{
			return false;
		}
		return true;
	}

	/**
	 * @param mixed $externalId
	 * @param int $sourceId
	 * @return bool
	 */
	protected function checkBrandRecentlyUpdated($externalId, int $sourceId): bool
	{
		$localBrand = Post::findFirst([
			'conditions' => 'context = :context: 
							AND parentId = :parentId: 
							AND externalId = :externalId: 
							AND sourceId = :sourceId: 
							AND (modifiedAt IS NULL OR modifiedAt < :modifiedAt:)',
			'bind'       => [
				'context'    => 'post',
				'parentId'   => 117, // Merken
				'externalId' => $externalId,
				'sourceId'   => $sourceId,
				'modifiedAt' => date('Y-m-d H:i:s', strtotime('-1 weeks'))
			],
		]);
		if (null === $localBrand)
		{
			return true;
		}
		return false;
	}

	/**
	 * @param mixed $externalId
	 * @param int $sourceId
	 * @return bool
	 */
	protected function checkPostExists($externalId, int $sourceId): bool
	{
		$localPost = Post::findFirst([
			'conditions' => 'context = :context: AND parentId = :parentId: AND externalId = :externalId: AND sourceId = :sourceId:',
			'bind'       => [
				'context'    => 'post',
				'parentId'   => 118, // Kortingscodes
				'externalId' => $externalId,
				'sourceId'   => $sourceId,
			],
		]);
		if (null === $localPost)
		{
			return false;
		}
		return true;
	}

	/**
	 * @param mixed $externalId
	 * @param int $sourceId
	 * @return bool
	 */
	protected function checkPostRecentlyUpdated($externalId, int $sourceId): bool
	{
		$localBrand = Post::findFirst([
			'conditions' => 'context = :context: 
							AND parentId = :parentId: 
							AND externalId = :externalId: 
							AND sourceId = :sourceId: 
							AND (modifiedAt IS NULL OR modifiedAt < :modifiedAt:)',
			'bind'       => [
				'context'    => 'post',
				'parentId'   => 118, // Kortingscodes
				'externalId' => $externalId,
				'sourceId'   => $sourceId,
				'modifiedAt' => date('Y-m-d H:i:s', strtotime('-2 weeks'))
			],
		]);
		if (null === $localBrand)
		{
			return true;
		}
		return false;
	}

	/**
	 * @param array $data
	 * @return void
	 */
	protected function processPost(array $data): void
	{
		// Check for existing record
		/**
		 * @var Post $post
		 */
		$post = Post::findFirst([
			'conditions' => "context = :context: AND externalId = :external_id: AND sourceId = :source_id:",
			'bind'       => [
				'context'     => 'post',
				'external_id' => $data['externalId'],
				'source_id'   => $data['sourceId'],
			],
		]);

		if (null !== $post)
		{
//			$this->info('Found EXISTING item: ' . $post->title . ' (e_id ' . $data['externalId'] . ' -> id ' . $post->id . ')');
			// Update
			$data['title'] = $post->title;
			$data['summary'] = $post->summary;
			$data['description'] = $post->description;
			$data['image'] = $post->image;
			$data['modifiedAt'] = date('Y-m-d H:i:s');
			$data['modifiedBy'] = 3; // = Admin | 4 = Jeroen G
			unset($data['createdAt']);
			unset($data['createdBy']);
			$data['metaTitle'] = $post->metaTitle;
			$data['metaDesc'] = $post->metaDesc;
			$data['metaKeys'] = $post->metaKeys;
			unset($data['image']);
			unset($data['hits']);
			unset($data['tags']);
			unset($data['fields']);

			if (strtotime($post->createdAt) < strtotime('-1 week')
				&& (true === empty($post->modifiedAt)
					|| strtotime($post->modifiedAt) < strtotime('-1 week')
				)
			)
			{
//				$this->info('UPDATE item: e_id = ' . $data['externalId'] . ' - id = ' . $post->id);
				$post = Post::findFirst([
					'conditions' => 'id = :post_id:',
					'bind'       => [
						'post_id' => $post->id,
					],
				]);
				$post->assign($data)->save();
				++$this->updatedCount;
			}
			else
			{
				++$this->skippedCount;
			}
		}
		else
		{
			// Insert
			$this->info('INSERT item: ' . $data['title'] . ' (e_id ' . $data['externalId'] . ')');
			$post = new Post();
			$post->assign($data)->save();
			$post->id = $post->getDI()->get('db')->lastInsertId();
			++$this->insertedCount;
		}

		// Process tags (if any)
		if (false === empty($post->id) && false === empty($data['tags']))
		{
			$firstTags = explode(', ', $data['tags']);
			$secondTags = [];
			foreach ($firstTags as $firstTag)
			{
				$exploded = explode(',', $firstTag);
				$secondTags = array_unique(array_merge($exploded, $secondTags));
			}
			$thirdTags = [];
			foreach ($secondTags as $secondTag)
			{
				$exploded = explode(' ', $secondTag);
				$thirdTags = array_unique(array_merge($exploded, $thirdTags));
			}

			$tags = array_unique(array_merge($firstTags, $secondTags, $thirdTags));
			foreach ($tags as $tag)
			{
				$tag = str_replace(',', '-', $tag);
				self::processPostTag($post->id, $tag);
			}
		}

		// Process fields (if any)
		if (false === empty($post->id) && false === empty($data['fields']))
		{
			foreach ($data['fields'] as $field => $value)
			{
				self::processPostField($post->id, $field, $value);
			}
		}
	}

	/**
	 * @param int $postId
	 * @param string $tagTitle
	 * @return void
	 */
	protected function processPostTag(int $postId , string $tagTitle) : void
	{
		if (true === empty($postId) || true === empty($tagTitle))
		{
			return;
		}
		// Sanitize tag
		$tagTitle = str_replace(['\'', '"'], '', $tagTitle);

		// Skip numerical tags
		if (true === is_numeric($tagTitle))
		{
			return;
		}

		// Check if tag exists
		$tag = Tag::findFirst([
			'conditions' => "title = '" . $tagTitle . "'",
		]);
		if (false === empty($tag->id))
		{
			$tagId = $tag->id;
		}
		else
		{
			$tag = new Tag();
			$data = [
				'title'     => substr($tagTitle, 0, 254),
				'slug'      => 'tag-' . substr(Filter::toSlug($tagTitle), 0, 185),
				'createdAt' => date('Y-m-d H:i:s'),
				'createdBy' => 3,
			];
			$tag->assign($data)->create();

			$newTag = Tag::find(
				"title = '" . $tagTitle . "'"
			)->toArray();
			if (true === empty($newTag))
			{
				return;
			}
			$newTag = reset($newTag);
			$tagId = $newTag['id'];
		}

		if ($postId && $tagId)
		{
			// Check if mapping exists
			$map = UcmItemMap::find(
				"context = 'tag' AND itemId1 = " . $postId . " AND itemId2 = " . $tagId
			)->toArray();
			if (true === empty($map))
			{
				$map = new UcmItemMap();
				$data = [
					'context' => 'tag',
					'itemId1' => $postId,
					'itemId2' => $tagId,
				];
				$map->assign($data)->save();
			}
		}
	}

	/**
	 * @param int $postId
	 * @param string $fieldName
	 * @param mixed $value
	 */
	protected function processPostField(int $postId, string $fieldName, $value) : void
	{
		if (true === empty($postId) || true === empty($fieldName))
		{
			return;
		}
		if (true === is_array($value))
		{
			$value = implode(' ', $value);
		}
		// Find the field
		$field = UcmField::find(
			"name = '" . $fieldName . "' AND context = 'post'"
		)->toArray();
		if (false === empty($field))
		{
			$field = reset($field);
		}
		else
		{
			return;
		}

		// Check for existing record
		$record = UcmFieldValue::findFirst([
			'conditions' => "fieldId = '" . $field['id'] . "' AND itemId = '" . $postId . "'",
		]);
		if (true === empty($record))
		{
			// Insert
			$record = new UcmFieldValue();
		}
		$data = [
			'fieldId' => $field['id'],
			'itemId'  => $postId,
			'value'   => $value,
		];
		$record->assign($data)->save();
	}


	/**
	 * @param array $file
	 * @param string $subFolder
	 * @return array
	 */
	protected function saveImage(array $file = [], string $subFolder = 'brands'): array
	{
		$fileName = $file['name'];
		$mime = $file['type'];
		$data = [
			'file' => $fileName,
		];

		if (strpos($mime, 'image/') !== 0)
		{
			$this->info('file-not-image-message ' . $fileName);
		}
		else
		{
			try
			{
//                $file->moveTo($this->uploadPath . '/brands/' . $fileName);
				if (false === is_dir($this->uploadPath . '/' . $subFolder . '/' . $fileName[0]))
				{
					mkdir($this->uploadPath . '/' . $subFolder . '/' . $fileName[0], 0777, true);
					mkdir($this->uploadPath . '/' . $subFolder . '/' . $fileName[0] . '/thumbs', 0777, true);
				}
				touch($this->uploadPath . '/' . $subFolder . '/' . $fileName[0] . '/' . $fileName);
				copy($file['tmp_name'], $this->uploadPath . '/' . $subFolder . '/' . $fileName[0] . '/' . $fileName);
				unlink($file['tmp_name']);
				$file = $subFolder . '/' . $fileName[0] . '/' . $fileName;
			}
			catch (Exception $exception)
			{
				$this->renderError('ERROR saveImage', $exception);
				$file = 'image_not_found.png';
			}

			$data = [
				'file'      => $file,
				'type'      => 'image',
				'mime'      => $mime,
				'createdAt' => (new Date())->toSql(),
				'createdBy' => 3, // = Admin | 4 = Jeroen G
			];

			if ($media = Media::findFirst([
				'conditions' => "file = '" . $data['file'] . "'",
			])
			)
			{
				$media->assign($data)->save();
			}
			else
			{
				(new Media())->assign($data)->save();
			}
		}
		return $data;
	}

	// Function to check string starting
	// with given substring
	protected function startsWith ($string, $startString) : bool
	{
		$len = strlen($startString);
		return (substr($string, 0, $len) === $startString);
	}

	/**
	 * @param $output
	 * @param Exception $exception
	 * @param Exception|null $previous
	 */
	protected function renderError($output, Exception $exception, Exception $previous = null)
	{
		echo $output . PHP_EOL;
		echo $exception->getMessage() . PHP_EOL;
		echo PHP_EOL;
		echo $exception->getTraceAsString();
		if (null === $previous)
		{
			$previous = $exception->getPrevious();
		}
		if (false === empty($previous))
		{
			echo PHP_EOL;
			echo '====== previous ======' . PHP_EOL;
			echo $previous->getMessage() . PHP_EOL;
			echo PHP_EOL;
			echo $previous->getTraceAsString();
		}
	}
}
