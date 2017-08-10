<?php

namespace KenticoCloud\Delivery;

class Client
{
    public $previewMode = false;
    public $urlBuilder = null;
    public $previewApiKey = null;
    public $_debug = true;
    public $lastRequest = null;
    public $mode = null;
    protected $typeMapper = null; 
    protected $modelBinder = null;   

    public function __construct($projectId, $previewApiKey = null, TypeMapperInterface $typeMapper = null)
    {
        $this->previewApiKey = $previewApiKey;
        $this->previewMode = !is_null($previewApiKey);
        $this->urlBuilder = new UrlBuilder($projectId, $this->previewMode);
        $this->typeMapper = $typeMapper;
        $self = get_class($this);
    }

    public function getItems($params)
    {
        $uri = $this->urlBuilder->getItemsUrl($params);
        $request = $this->getRequest($uri);
        $response = $this->send($request);

        $modelBinder = $this->getModelBinder();
                
        $items = new Models\ContentItems($modelBinder, $response->body);

        return $items;
    }

    public function getItem($params)
    {
        //TODO: use the 'item' endpoint (https://deliver.kenticocloud.com/975bf280-fd91-488c-994c-2f04416e5ee3/items/home)
        $params['limit'] = 1;
        $results = $this->getItems($params);

        if (!isset($results->items) || !count($results->items)) {
            return null;
        }

        $item = reset($results->items);
        return $item;
    }

    protected function getRequest($uri)
    {
        //TODO: make use of templates http://phphttpclient.com/#templates
        $request = \Httpful\Request::get($uri);
        $request->_debug = $this->_debug;
        $request->mime('json');
        if (!is_null($this->previewApiKey)) {
            $request->addHeader('Authorization', 'Bearer ' . $this->previewApiKey);
        }
        return $request;
    }

    protected function send($request)
    {
        $response = $request->send();
        $this->lastRequest = $request;
        $this->lastResponse = $response;
        return $response;
    }

    protected function getModelBinder()
    {
        if($this->modelBinder == null)
        {
            $this->modelBinder = new ModelBinder($this->typeMapper ?? new DefaultTypeMapper());
        }
        return $this->modelBinder;
    }
}
