<?php

namespace CLSystems\PhalCMS\Library\Mvc\Controller;

use PHPMailer\PHPMailer\Exception as PHPMailerException;
use CLSystems\PhalCMS\Library\Helper\Asset;
use CLSystems\PhalCMS\Library\Helper\Text;
use CLSystems\PhalCMS\Library\Helper\Toolbar;
use CLSystems\PhalCMS\Library\Helper\Mail as MailHelper;
use CLSystems\PhalCMS\Library\Helper\Config as CMSConfig;
use CLSystems\PhalCMS\Library\Mvc\Model\Config as ConfigModel;

class AdminConfigController extends AdminControllerBase
{
	/** @var ConfigModel $model */
	public $model = 'Config';

	public function getEntity()
	{
		$entity = $this->model->findFirst(
			[
				'conditions' => 'context = :context:',
				'bind'       => [
					'context' => 'cms.config',
				],
			]
		);

		if (!$entity)
		{
			$entity          = new ConfigModel;
			$entity->id      = 0;
			$entity->context = 'cms.config';
		}

		return $entity;
	}

	public function indexAction()
	{
		Asset::addFile('config.js');
		$this->tag->setTitle(Text::_('configuration'));
		$this->view->setVar('formsManager', $this->model->getFormsManager());
		$this->indexToolBar();
	}

	public function saveAction()
	{
		if ($this->request->isPost())
		{
			$formsManager = $this->model->getFormsManager(false);
			$formData     = $this->request->getPost('FormData');
			$validData    = $formsManager->bind($formData);

			if (false === $validData)
			{
				$this->flashSession->warning(implode('<br/>', $formsManager->getMessages()));

				return $this->response->redirect($this->uri->routeTo('index'), true);
			}

			if ($this->getEntity()->assign(['data' => $validData])->save())
			{
				$this->flashSession->success(Text::_('config-saved'));
				$this->uri->setVar('adminPrefix', $validData['adminPrefix']);
			}
			else
			{
				$this->flashSession->error('Error, cannot save config data.');
			}
		}

		$this->response->redirect($this->uri->routeTo('index'), true);
	}

	protected function indexToolBar($activeState = null, $excludes = [])
	{
		Toolbar::add('save', $this->uri->routeTo('save'), 'cloud-check');
	}

	public function testMailAction()
	{
		if ($this->request->isPost()
			&& $this->request->isAjax()
		)
		{
			$data   = $this->request->getPost('FormData', null, []);
			$mailer = MailHelper::getInstance(
				[
					'host'     => empty($data['sysSmtpHost']) ? CMSConfig::get('sysSmtpHost') : trim($data['sysSmtpHost']),
					'port'     => empty($data['sysSmtpPort']) ? CMSConfig::get('sysSmtpPort') : trim($data['sysSmtpPort']),
					'username' => empty($data['sysSmtpUsername']) ? CMSConfig::get('sysSmtpUsername') : trim($data['sysSmtpUsername']),
					'password' => empty($data['sysSmtpPassword']) ? CMSConfig::get('sysSmtpPassword') : trim($data['sysSmtpPassword']),
					'security' => empty($data['sysSmtpSecurity']) ? CMSConfig::get('sysSmtpSecurity') : trim($data['sysSmtpSecurity']),
				]
			);

			$site     = empty($data['siteName']) ? CMSConfig::get('siteName') : trim($data['siteName']);
			$fromMail = empty($data['sysSendFromMail']) ? CMSConfig::get('sysSendFromMail') : trim($data['sysSendFromMail']);
			$fromName = empty($data['sysSendFromName']) ? CMSConfig::get('sysSendFromName') : trim($data['sysSendFromName']);

			try
			{
				$mailer->setFrom($fromMail, $fromName);
				$mailer->addAddress($fromMail, $fromName);
				$mailer->Subject = Text::_('send-test-mail');
				$mailer->Body    = Text::_('test-mail-body', ['site' => $site]);

				if (true === $mailer->send())
				{
					$this->response->setJsonContent(
						[
							'status'  => 'success',
							'message' => Text::_('send-test-mail-success'),
						]
					);
				}
				else
				{

					$this->response->setJsonContent(
						[
							'status'  => 'danger',
							'message' => Text::_('send-test-mail-fail') . '<br>' . $mailer->ErrorInfo,
						]
					);
				}
			}
			catch (PHPMailerException $e)
			{
				$this->response->setJsonContent(
					[
						'status'  => 'danger',
						'message' => $e->getMessage(),
					]
				);
			}

			return $this->response;
		}

		$this->response->setJsonContent(
			[
				'status'  => 'danger',
				'message' => Text::_('send-test-mail-fail'),
			]
		);
		return $this->response;
	}

}
