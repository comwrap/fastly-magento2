<?php

namespace Fastly\Cdn\Plugin\Model\PageCache;

use Fastly\Cdn\Model\Config as FastlyConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\PageCache\Model\Config;

/**
 * Class ConfigPlugin
 * @package Fastly\Cdn\Plugin\Model\PageCache
 */
class ConfigPlugin
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * ConfigPlugin constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param Config $subject
     * @param int $result
     * @return int|string
     */
    public function afterGetType(Config $subject, $result)
    {
        $type = $this->scopeConfig->getValue(Config::XML_PAGECACHE_TYPE);
        if ($type != FastlyConfig::OLD_FASTLY_TYPE) {
            return $result;
        }

        return FastlyConfig::FASTLY;
    }
}
