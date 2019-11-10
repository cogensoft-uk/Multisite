<?php

namespace Cogensoft\Multisite\Block;

/** @codingStandardsIgnoreFile */

class CountryCookie extends \Magento\Framework\View\Element\Template
{
    public function canRedirect()
    {
        if ($this->getRequest()->getParam('source') == 'shoppingads' || $this->getRequest()->getParam('langSwitch')) {
            return false;
        }

        return true;
    }

    public function getCurrency()
    {
        return $this->_scopeConfig->getValue('currency/options/default', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
}
