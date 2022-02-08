<?php

namespace Fastly\Cdn\Model;

use Magento\Framework\App\RequestInterface;
use Magento\GraphQl\Controller\GraphQl;

class GraphQlPlugin
{
    private $config;

    /**
     * GraphQlPlugin constructor.
     *
     * @param Config $config
     */
    public function __construct(
        Config $config
    )
    {
        $this->config = $config;
    }

    /**
     * Set Query from Fastly Header
     *
     * @param GraphQl $subject
     * @param RequestInterface $request
     */
    public function beforeDispatch(GraphQl $subject, RequestInterface $request): void
    {
        // Is Fastly cache enabled?
        if ($this->config->getType() !== Config::FASTLY) {
            return;
        }

        $fastlyHeader = (string)$request->getHeader('X-GraphQL-Query');
        if ($fastlyHeader) {
            $fastlyHeaderQuery = $this->decodeQuery($fastlyHeader);

            if ($fastlyHeaderQuery) {
                if ($request->isGet()) {
                    $request->setParam('query', $fastlyHeaderQuery);
                } elseif ($request->isPost()) {
                    $request->setContent(
                        json_encode(['query' => $fastlyHeaderQuery])
                    );
                }
            }
        }
    }

    /**
     * Decode fastly header query
     *
     * @param $query
     * @return string
     */
    private function decodeQuery($query): string
    {
        $result = "";
        if (\str_starts_with($query, 'gzip ') !== false) {
            $result = \substr_replace($query, '', '0', '5');
            $result = \base64_decode($result);
            if($result){
                $result = @\gzuncompress($result);
            }
        } elseif (\str_starts_with($query, 'plain ') !== false) {
            $result = \substr_replace($query, '', '0', '6');
            $result = \base64_decode($result);
        }
        return (string)$result;
    }
}
