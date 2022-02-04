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
            $fastlyHeaderQuery = base64_decode($fastlyHeader);

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
}
