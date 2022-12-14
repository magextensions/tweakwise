<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Catalog\Product\Recommendation;

use Emico\Tweakwise\Model\Client;
use Emico\Tweakwise\Model\Client\Request\Recommendations\FeaturedRequest;
use Emico\Tweakwise\Model\Client\RequestFactory;
use Emico\Tweakwise\Model\Client\Response\RecommendationsResponse;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\Config as CatalogConfig;

class Context
{

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var CatalogConfig
     */
    protected $catalogConfig;

    /**
     * @var Visibility
     */
    protected $visibility;

    /**
     * @var FeaturedRequest
     */
    protected $request;

    /**
     * @var RecommendationsResponse
     */
    protected $response;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * Context constructor.
     * @param Client $client
     * @param RequestFactory $requestFactory
     * @param CollectionFactory $collectionFactory
     * @param CatalogConfig $catalogConfig
     * @param Visibility $visibility
     */
    public function __construct(
        Client $client,
        RequestFactory $requestFactory,
        CollectionFactory $collectionFactory,
        CatalogConfig $catalogConfig,
        Visibility $visibility
    ) {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->collectionFactory = $collectionFactory;
        $this->catalogConfig = $catalogConfig;
        $this->visibility = $visibility;
    }

    /**
     * @return FeaturedRequest
     */
    public function getRequest()
    {
        if (!$this->request) {
            $this->request = $this->requestFactory->create();
        }

        return $this->request;
    }

    /**
     * @return RecommendationsResponse
     */
    public function getResponse()
    {
        if (!$this->response) {
            $this->response = $this->client->request($this->getRequest());
        }
        return $this->response;
    }

    /**
     * @return Collection
     */
    public function getCollection()
    {
        if (!$this->collection) {
            $collection = $this->collectionFactory->create(['response' => $this->getResponse()]);
            $this->prepareCollection($collection);
            $this->collection = $collection;
        }

        return $this->collection;
    }

    /**
     * @param Collection $collection
     */
    protected function prepareCollection(Collection $collection)
    {
        $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addUrlRewrite()
            ->setVisibility($this->visibility->getVisibleInCatalogIds())
            ->setFlag('do_not_use_category_id', true);
    }

    /**
     * @param FeaturedRequest $request
     * @return void
     */

    public function setRequest(FeaturedRequest $request) {
        $this->collection = null;
        $this->response = null;
        $this->request = $request;
    }
}
