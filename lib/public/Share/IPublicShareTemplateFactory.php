<?php

namespace OCP\Share;

interface IPublicShareTemplateFactory {
	public function getProvider(IShare $share): IPublicShareTemplateProvider;
}
