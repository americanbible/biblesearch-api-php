<?php

/**
 * @version $Id$
 * @author  Brian Smith <wisecounselor@gmail.com>
 * @package ABS
 */

/**
 * A base class for the ABS objects that wrap XML returned by API calls.
 *
 * This class is responsible for:
 * - Creating the object
 * - Requesting and caching the requested XML.
 *
 * @author  Brian Smith <wisecounselor@gmail.com>
 * @package ABS
 */
abstract class ABS_Base {
    /**
     * Reference to the API.
     *
     * Technically we could pull this from $_request but it makes sense to
     * cache it.
     *
     * @var object ABS_Api
     * @see __construct(), getApi()
     */
    protected $_api = null;
    /**
     * @var object ABS_Request
     * @see __construct(), createRequest(), getRequest()
     */
    protected $_request = null;
    /**
     * The name of the XML element in the response that defines the object.
     *
     * If the response looks like <resp><objname /></resp> this should be
     * "objname".
     *
     * @var string
     * @see getResponseElement()
     */
    protected $_respElement;
    /**
     * The name of the XML element in the response that defines an
     * array of objects returned as part of a response object
     *
     * @var string
     * @see getDataElement()
     */
    protected $_dataElement;
    /**
     * XML from ABS (or the cache) used to define an array of objects
     * that is part of the response object.
     *
     * @var object SimpleXMLElement
     */
    protected $_cachedXml = null;
    
    /**
     * Parameters to build URL for submission to ABS
     *
     * @var array
     */
    protected $_params;
    /**
     * Holds parse xml
     *
     * @var array
     */
    public $_xml;
     /**
     * Holds an array of data returned by a request, such as list of chapters
     * or verses
     *
     * @var array
     */
    public $_data;
     /**
     * An xpath path used to pull out data from xml returns for use
     * in an iterator object
     *
     * @var array
     */
    protected $_xpath;
     /**
     * REST method being employed by an action
     *
     * @var array
     */
    protected $_restMethod = 'GET';
    /**
     * whether or not to allow response to be cached
     *
     * @var boolean
     */
    protected $_allowCached = true;
     /**
     * request data used for REST methods like POST, PUT, DELETE
     *
     * @var array
     */
    protected $_requestData;
     /**
     * whether or not to throw exception on failure
     *
     * @var boolean
     */
    protected $_throwOnFail = true;
	
     /**
     * A version ID defining a specific Bible version
     *
     * @var string
     */ 
    protected $_version_id;
	
	/**
     * A testament ID defining a specific testament (OT,NT,DEUT)
     *
     * @var string
     */ 
    protected $_testament_id;
     
	 /**
     * A version ID defining a specific Bible version
     *
     * @var string
     */
    protected $_book_id;
    
     /**
     * A numeric chapter of a book
     *
     * @var integer
     */
    protected $_chapter;
    
    
     /**
     * A numeric chapter of a book
     *
     * @var integer
     */
    protected $_lang;
    
    
    /**
     * Constructor.
     *
     * Construct a base object for getting XML from the ABS api
     *
     * @param   object ABS_API $api
     * @param   string $responseElement Name of the XML element in the response
     *          that defines this object.
     */
    function __construct(ABS_Api $api, $responseElement) {
        $this->_api =& $api;
        $this->_respElement = $responseElement;
    }

    /**
     * Create a ABS_Request object that will request the XML for this object.
     *
     * @param   string method - method being executed for the request
     * @return  object ABS_Request
     */
    protected function &createRequest($method) {
        $request = $this->_api->createRequest(
            $this->getUrl($method),
            $this->getParams()
        );
        $request->setRestMethod($this->_restMethod);
        $request->setExceptionThrownOnFailure($this->_api->isExceptionThrownOnFailure());
        if (!empty($this->_requestData)) {
            $request->setRequestData($this->_requestData);
        }
        if (is_null($request)) {
            throw new ABS_Exception('Could not create a Request.');
        } else {
            $this->_request = $request;
            return $request;
        }
    }
    
    /**
     * Connect to ABS get XML
     *
     * @param   boolean $allowCached If a cached result exists, should it be
     *          returned?
     * @return  object SimpleXMLElement
     * @throws  ABS_ConnectionException, ABS_XmlParseException
     */
    protected function requestXml($method) {
        $response = $this->createRequest($method)->execute($this->_allowCached, $this->_throwOnFail);
        $xml = $response->xml;
        if (! empty($xml)) {
            if (is_null($xml) && $this->_throwOnFail) {
                throw new Exception("Could not load object with id: '{$this->getId()}'.");
            }
            $this->_xml = $xml;
        }
        
        return $xml;
    }
   
    /**
     * Method can be used to set a new response element
     */
    protected function setResponseElement($respElement) {
        $this->_respElement = $respElement;
    }
    /**
     * Return the current response element
     */
    
    protected function getResponseElement() {
        return $this->_respElement;
    }
    /**
     * Return the current data element
     */
    
    protected function getDataElement() {
        return $this->_dataElement;
    }
    protected function setDataElement($el) {
        $this->_dataElement = $el;
    }
    /**
     * Returns the name of this object's getInfo API method.
     *
     * @return  string
     */
    abstract protected function getUrl($method);
    
    /**
     * Return a reference to this object's ABS_Api.
     *
     * @return  object ABS_Api
     * @see     __construct()
     */
    public function &getApi() {
        return $this->_api;
    }
    /**
     * Return the ABS_Request the object is based on.
     *
     * @return  object ABS_Request
     * @see     __construct()
     */
    public function getRequest() {
        return $this->_request;
    }
    /**
     * Return the cached XML as a SimpleXMLElement.
     *
     * View it as text call $x->getXml()->asXml().
     *
     * @return  object SimpleXMLElement
     * @see     __construct(), SimpleXMLElement->asXml()
     * @since   0.2.4
     */
    public function getXml() {
        return $this->_xml;
    }
    /**
     * Return params to build URL
     */
    
    protected function getParams() {
        return $this->_params;
    }
    
    public function addParam($key, $val) {
        $this->_params[$key] = $val;
    }
    protected function __setXpath($p) {
        $this->_xpath = $p;
    }
    protected function __getXpath() {
        return $this->_xpath;
    }
    /**
     * Return an array of objects from the core xml object using xpath query
     * uses the response element parameter of an inherited class for query
     * 
     * @see getResponseElement
     */
    public function getData($xpath = '') {
        if (empty($xpath)) {
            $xpath = $this->__getXpath();
        }
        
        if (empty($xpath)) {
            $xpath = $this->getResponseElement();
        }
        if (empty($xpath)) {
            throw new Exception('Missing xpath query to retrieve data');
        }
        $xml = $this->getXml();
        
        if (! empty($xml)) {
            $r = $xml->xpath($xpath);
            if (false !== $r) {
                return $r;
            }
        }
        return array();
    }
	
	/*
     * Method to set the Bible version
     * 
     * @param   string $version
      */
    public function setVersion($version) {
        $this->_version_id = $version;
    }
	public function getVersion() {
		return $this->_version_id;
	}
	
	/*
     * Method to set the testament
     * 
     * @param   string $testament
      */
    public function setTestament($testament) {
        $this->_testament_id = $testament;
    }
	public function getTestament() {
		return $this->_testament_id;
	}
	
    /*
     * Method to set the version of the Bible being processed
     * 
     * @param   string $version_id
      */
    public function setBook($book) {
        $this->_book_id = $book;
    }
	public function getBook() {
		return $this->_book_id;
	}
	
    /*
     * Method to set the chapter of a Bible book
     * 
     * @param   integer chapter
      */
    public function setChapter($chapter) {
        $this->_chapter = 0+$chapter;
    }
	public function getChapter() {
		return $this->_chapter;
	}
	
    /*
     * Method to set the  Bible language
     * 
     * @param   string $lang
      */
    public function setLanguage($lang) {
        $this->_lang = $lang;
    }
    protected function __validateVersion() {
        if (empty($this->_version_id) || is_null($this->_version_id)) {
            throw new ABS_Exception('The Version cannot be null or empty');
        }
    }
    protected function __validateChapter() {
        if (empty($this->_chapter) || is_null($this->_chapter)) {
            throw new ABS_Exception('The Chapter cannot be null or empty');
        }
        if (! is_int($this->_chapter)) {
            throw new Exception('The chapter must be an integer value');
        }
    }
    protected function __validateBook() {
        if (empty($this->_book_id) || is_null($this->_book_id)) {
            throw new ABS_Exception('The Book ID cannot be null or empty');
        }
    }
    protected function __validateAll() {
        $this->__validateVersion();
        $this->__validateChapter();
        $this->__validateBook();
    }
    protected function setRestMethod($method) {
        $this->_restMethod = $method;
    }
    protected function setRequestData($data) {
        $this->_requestData = $data;
    }
    public function setThrowOnFail($setting) {
        $this->_throwOnFail = (boolean) $setting;
    }
    public function loadXml($xml) {
        $this->_xml = simplexml_load_string($xml);
        return $this->_xml;
    }
}