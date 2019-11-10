<?php

namespace Cogensoft\Multisite\Controller\Check;

use Cogensoft\Multisite\Model\Config;

class Redirect extends \Magento\Framework\App\Action\Action
{
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $jsonFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
	    \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
        $this->jsonFactory = $jsonFactory;
	    $this->_storeManager = $storeManager;

	    return parent::__construct($context);
    }

    public function execute()
    {
        $cookieSiteCode = (isset($_COOKIE['country'])) ? $_COOKIE['country'] : null;
        $currentSiteCode = $this->getIsoCodeFromStoreCode($this->getCurrentStoreCode());

        if(!$cookieSiteCode || $cookieSiteCode !== $currentSiteCode) {
	        $redirectToCountry = $cookieSiteCode;
	        if(!$redirectToCountry) {
		        $redirectToCountry = (isset($_SERVER['HTTP_CF_IPCOUNTRY'])) ? $_SERVER['HTTP_CF_IPCOUNTRY'] : Config::DEFAULT_CURRENCY;
	        }
	        $redirectoToStore = $this->getStoreCodeFromIsoCode($redirectToCountry);
	        $redirectToUrl = $this->buildRedirectUrl($redirectoToStore);

	        return $this->jsonFactory->create()->setData([
		        'redirect' => ($redirectToCountry !== $currentSiteCode),
		        'country' => $redirectToCountry,
		        'url' => $redirectToUrl,
	        ]);
        }

        return $this->jsonFactory->create()->setData([
        	'redirect' => false,
	        'country' => $currentSiteCode
        ]);
    }

    protected function getCurrentStoreCode() {
    	return $this->_storeManager->getStore()->getCode();
	}

    protected function getStoreCodeFromIsoCode($countryCode) {
    	$codes = Config::ISO_CODE_TO_STORE_CODE;
    	return (isset($codes[$countryCode]))
		    ? $codes[$countryCode]
		    : $codes[Config::DEFAULT_CURRENCY];
    }

	protected function getIsoCodeFromStoreCode($store) {
		$codes = Config::STORE_CODE_TO_ISO_CODE;
		return (isset($codes[$store]))
			? $codes[$store]
			: Config::DEFAULT_CURRENCY;
	}

    protected function buildRedirectUrl($redirectoToStore) {
    	$domainOverrides = Config::DOMAIN_OVERRIDES;
	    $baseUrl = (isset($domainOverrides[$redirectoToStore]))
		    ? $domainOverrides[$redirectoToStore]
	        : $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB) . $redirectoToStore;
	    $path = parse_url($this->getRequest()->getParam('url'), PHP_URL_PATH);
	    $url_params = parse_url($this->getRequest()->getParam('url'), PHP_URL_QUERY);

	    if($path) {
		    foreach(Config::ISO_CODE_TO_STORE_CODE AS $code) {
			    $count = 0;
			    $path = preg_replace('/^\/('.$code.')/', '', $path, 1, $count);
			    if($count) break;
		    }
		    $path = '/' . ltrim($path, '/');
	    }

	    $full = ($baseUrl . $path) . (($url_params) ? '?' . $url_params : '');

	    return $full;
    }
}
