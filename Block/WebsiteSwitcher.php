<?php

namespace Cogensoft\Multisite\Block;

use Magento\Framework\View\Element\Template;
use Cogensoft\Multisite\Model\Config;

/** @codingStandardsIgnoreFile */

class WebsiteSwitcher extends \Magento\Framework\View\Element\Template
{

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;

	public function __construct(
		Template\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		array $data = []
	) {
		$this->_storeManager = $storeManager;

		return parent::__construct( $context, $data );
	}

	public function getCurrentStoreId() {
		return $this->_storeManager->getStore()->getId();
	}

	public function getWebsiteCodesJson() {
		return json_encode(Config::ISO_CODE_TO_STORE_CODE);
	}

	public function getWebsites()
	{
		$domainOverrides = Config::DOMAIN_OVERRIDES;
		$_websites = $this->_storeManager->getWebsites();
		$_websiteData = array();
		foreach($_websites as $website){
			foreach($website->getStores() as $store){
				$wedsiteId = $website->getId();
				$storeObj = $this->_storeManager->getStore($store);
				$name = $storeObj->getName();
				$code = $storeObj->getCode();
				$url = $storeObj->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
				$_websiteData[] =  [
					'id' => $wedsiteId,
					'name' => $name,
					'url' => (isset($domainOverrides[$code])) ? $domainOverrides[$code] : $url.$code,
					'code' => $this->getIsoCodeFromStore($code)
				];
			}
		}

		return $_websiteData;
	}

	protected function getIsoCodeFromStore($store) {
		$codes = Config::STORE_CODE_TO_ISO_CODE;
		return (isset($codes[$store]))
			? $codes[$store]
			: Config::DEFAULT_CURRENCY;
	}
}