<?php

namespace Cogensoft\Multisite\Model;

class Config {
	const DEFAULT_CURRENCY = 'GB';

	const ISO_CODE_TO_STORE_CODE = [
		'DE' => 'de',
		'FR' => 'fr',
		'GB' => 'uk',
		'PL' => 'pos'
	];

	const STORE_CODE_TO_ISO_CODE = [
		'de' => 'DE',
		'fr' => 'FR',
		'uk' => 'GB',
		'pos' => 'GB'
	];

	const DOMAIN_OVERRIDES = [
		'uk' => ' https://www.fightequipmentuk.com',
		'fr' => 'https://www.fightequipment.fr',
		'de' => 'https://www.combatequipment.de'
	];
}
