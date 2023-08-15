<?php

namespace CLSystems\PhalCMS\Lib\Mvc\Controller;

use CLSystems\PhalCMS\Lib\Helper\Config as CMSConfig;
use CLSystems\PhalCMS\Lib\Helper\Date;
use CLSystems\PhalCMS\Lib\Helper\Form;
use CLSystems\PhalCMS\Lib\Helper\Mail as MailHelper;
use CLSystems\PhalCMS\Lib\Helper\Uri;
use CLSystems\PhalCMS\Lib\Helper\User;
use CLSystems\PhalCMS\Lib\Helper\Text;
use CLSystems\PhalCMS\Lib\Helper\Comment;
use CLSystems\PhalCMS\Lib\Helper\Config;
use CLSystems\PhalCMS\Lib\Mvc\Model\Formbuilder;
use CLSystems\PhalCMS\Lib\Mvc\Model\UcmComment;
use CLSystems\PhalCMS\Lib\Mvc\Model\UcmItem;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use stdClass;

class FormbuilderController extends ControllerBase
{
	protected function notFound()
	{
		$this->dispatcher->forward(
			[
				'controller' => 'error',
				'action'     => 'show',
			]
		);
	}

	public function viewAction()
	{
		$queryBuilder = $this->modelsManager
			->createBuilder()
			->columns('*')
			->from(Formbuilder::class)
			->where('route LIKE :route:');
		$bindParams   = [
			'route' => '%' . $this->dispatcher->getParam('path'),
		];
		$result = $queryBuilder->getQuery()
			->execute($bindParams)
			->getFirst();

		if (!$result)
		{
			$this->notFound();
		}

		$this->view->setVar('item', $result);

		// Metadata
		$metadata                = new stdClass;
		$metadata->metaTitle     = $result->metaTitle;
		$metadata->metaDesc      = $result->metaDesc;
		$metadata->metaKeys      = $result->metaKeys;
		$metadata->contentRights = Config::get('siteContentRights');
		$metadata->metaRobots    = Config::get('siteRobots');

		$vars = [
			'metadata' => $metadata,
		];

		$this->view->setVars($vars);
		$this->view->pick('Formbuilder/View');
	}

	public function postAction()
	{
		if (!$this->request->isPost())
		{
			$this->dispatcher->forward(
				[
					'controller' => 'error',
					'action'     => 'show',
				]
			);

			return false;
		}

		$queryBuilder = $this->modelsManager
			->createBuilder()
			->columns('*')
			->from(Formbuilder::class)
			->where('route LIKE :route:');
		$bindParams   = [
			'route' => '%' . $this->dispatcher->getParam('path'),
		];
		$result = $queryBuilder->getQuery()
			->execute($bindParams)
			->getFirst();

		if (!$result)
		{
			$this->notFound();
		}
		$this->view->setVar('item', $result);

		if (true !== Form::checkToken())
		{
			$this->view->pick('Formbuilder/InvalidToken');
		}

		$fields = json_decode($result->description, true);
		$postValues = [];
		foreach ($fields as $field)
		{
			$postValues[$field['name']] = $this->request->getPost($field['name']);
		}

		$data = array(
			'secret' => "0x4642BD05b3c691Dc43715bDb492BEF255351aAB1",
			'response' => $this->request->getPost('h-captcha-response'),
		);
		$verify = curl_init();
		curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
		curl_setopt($verify, CURLOPT_POST, true);
		curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($verify);
		$responseData = json_decode($response, true);
//		var_dump($responseData); die;

		if(false === $responseData['success'])
		{
			$this->flashSession->error(Text::_('form-fail-message'));
			return $this->response->redirect(Uri::route('forms/' . $this->dispatcher->getParam('path'), true), true);
		}

		$this->sendForm($result->metaTitle, $postValues);

		// Metadata
		$metadata                = new stdClass;
		$metadata->metaTitle     = $result->metaTitle;
		$metadata->metaDesc      = $result->metaDesc;
		$metadata->metaKeys      = $result->metaKeys;
		$metadata->contentRights = Config::get('siteContentRights');
		$metadata->metaRobots    = Config::get('siteRobots');

		$vars = [
			'metadata' => $metadata,
		];

		$this->view->setVars($vars);
		$this->view->pick('Formbuilder/Success');

	}

	private function sendForm($title, $postValues) : bool
	{
		$mailer = MailHelper::getInstance(
			[
				'host'     => CMSConfig::get('sysSmtpHost'),
				'port'     => CMSConfig::get('sysSmtpPort'),
				'username' => CMSConfig::get('sysSmtpUsername'),
				'password' => CMSConfig::get('sysSmtpPassword'),
				'security' => CMSConfig::get('sysSmtpSecurity'),
			]
		);

		$fromMail = CMSConfig::get('sysSendFromMail');
		$fromName = CMSConfig::get('sysSendFromName');

		try
		{
			$mailer->setFrom($fromMail, $fromName);
			$mailer->addAddress('info@casservices.nl', 'Cas Services');
			$mailer->addReplyTo($postValues['email']);
			$mailer->Subject = $title;
			$html = '
			<html>
			<body>
			<h1>' . $title . '</h1>
			<table border="0" cellpadding="3" cellspacing="3">';
			foreach ($postValues as $name => $value)
			{
				$html .= '<tr><td>' . $name . '</td><td>' . (is_array($value) ? implode(', ', $value) : $value) . '</td></tr>';
			}
			$html .= '</table>
			</body>
			</html>
			';
			$mailer->Body    = $html;
			$mailer->isHTML(true);

			if (true === $mailer->send())
			{
				return true;
			}
			else
			{

				return false;
			}
		}
		catch (PHPMailerException $e)
		{
			return false;
		}
	}

}