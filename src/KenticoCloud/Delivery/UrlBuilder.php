<?php

namespace KenticoCloud\Delivery;

class UrlBuilder
{
    public $projectID = null;
    public $usePreviewApi = false;
	const PREVIEW_ENDPOINT  = 'https://preview-deliver.kenticocloud.com/';
	const PRODUCTION_ENDPOINT  = 'https://deliver.kenticocloud.com/';
    
    const URL_TEMPLATE_ITEM = '/items/%s';
    const URL_TEMPLATE_ITEMS = '/items';
    const URL_TEMPLATE_TYPE = '/types/%s';
    const URL_TEMPLATE_TYPES = '/types';
    const URL_TEMPLATE_ELEMENT = '/types/%s/elements/%s';
    const URL_TEMPLATE_TAXONOMIES = '/taxonomies';
    const URL_TEMPLATE_TAXONOMY = '/taxonomies/%s';

    public function __construct($projectID, $usePreviewApi = null)
    {
        $this->projectID = $projectID;
        $this->usePreviewApi = $usePreviewApi;
    }

    public function getItemUrl($codename, $query)
    {
        return $this->buildUrl(sprintf(self::URL_TEMPLATE_ITEM, urlencode($codename)), $query);
    }

    public function getItemsUrl($query = null)
    {
        return $this->buildUrl(self::URL_TEMPLATE_ITEMS, $query);
    }

    
    /**
     * Returns URL to specified Content Type endpoint.
     *
     * @param string $codename Content Type code name.
     *
     * @return string URL pointing to specific Content Type endpoint.
     */
    public function getTypeUrl($codename)
    {
        return $this->buildUrl(sprintf(self::URL_TEMPLATE_TYPE, urlencode($codename)));
    }


    /**
     * Returns URL to all Content Types endpoint.
     *
     * @param QueryParams Specification of parameters for Content Types request.
     *
     * @return string URL pointing to Content Types endpoint.
     */
    public function getTypesUrl($query = null)
    {
        return $this->buildUrl(self::URL_TEMPLATE_TYPES, $query);
    }


    /**
     * Returns URL to Taxonomy endopoint.
     *
     * @param string $codename Codename of specific taxonomy to be retrieved.
     *
     * @return string URL pointing to Taxonomy endpoint.
     */
    public function getTaxonomyUrl($codename)
    {
        return $this->buildUrl(sprintf(self::URL_TEMPLATE_TAXONOMY, urlencode($codename)));
    }


    /**
     * Returns URL to all taxonomies endopoint.
     *
     * @param QueryParams Specification of parameters for Taxonomies request.
     *
     * @return string URL pointing to all taxonomies endpoint.
     */
    public function getTaxonomiesUrl($query)
    {
        return $this->buildUrl(self::URL_TEMPLATE_TAXONOMIES, $query);
    }

    public function getContentElementUrl($contentTypeCodename, $contentElementCodename)
    {
        return $this->buildUrl(sprintf(self::URL_TEMPLATE_ELEMENT, urlencode($contentTypeCodename), urlencode($contentElementCodename)));
    }
    
    private function buildUrl($endpoint, $query = null)
    {
        $segments = array(
            trim($this->usePreviewApi ? self::PREVIEW_ENDPOINT : self::PRODUCTION_ENDPOINT, '/'),
            trim($this->projectID, '/'),
            trim($endpoint, '/')
        );
        $url = implode('/', $segments);
        
        if (is_a($query, \KenticoCloud\Delivery\QueryParams::class)) {
            $query = $query->data;
        }
        if (is_array($query)) {
            $query = http_build_query($query);
        }
        if (is_string($query)) {
            $url = rtrim($url, '?') . '?' . ltrim($query, '?');
        }

        return $url;
    }
}
