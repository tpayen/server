<?php

namespace OC\Share20;

use OC\AppFramework\Bootstrap\Coordinator;
use OCP\Server;
use OCP\Share\IShare;
use OCP\Share\IPublicShareTemplateFactory;
use OCP\Share\IPublicShareTemplateProvider;

class PublicShareTemplateFactory implements IPublicShareTemplateFactory {
	public function __construct(
		private Coordinator $coordinator
	) {
	}

	public function getProvider(IShare $share): IPublicShareTemplateProvider {
		$context = $this->coordinator->getRegistrationContext();
		$providerRegistrations = $context->getPublicShareTemplateProviders();

		/**
		 * @var IPublicShareTemplateProvider[]
		 */
		$providers = array_map(
			fn ($registration) => Server::get($registration->getService()),
			$providerRegistrations
		);

		usort($providers, fn (IPublicShareTemplateProvider $a, IPublicShareTemplateProvider $b) => $b->getPriority() - $a->getPriority());
		$filteredProviders = array_filter($providers, fn (IPublicShareTemplateProvider $provider) => $provider->shouldRespond($share));

		return array_shift($filteredProviders);
	}
}
