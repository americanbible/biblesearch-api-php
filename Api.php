<?php
/**
 * @version $Id$
 * @author  Brian Smith <wisecounselor@gmail.com>
 * @package ABS
 */
    /*
    * Include the ABS exceptions, core classes.
    */
    //require_once 'Exception.php';
    
    /**
    * Include the ABS Request, core classes.
    */
    //require_once 'Request.php';
    
    /**
    * Include the ABS Request, core classes.
    */
    //require_once 'Response.php';
    
class ABS_Api {
    
    /**
     * Should an exception be thrown when an API call fails?
     *
     * @var boolean
     */
    private $_throwOnFail = true;
    
    /**
     * The default American Bible Society API endpoint URL for REST requests.
     *
     * @var string
     * @see setEndpointUrl()
     */
    const REST_ENDPOINT_URL = 'http://biblesearch.americanbible.org/';
    //const REST_ENDPOINT_URL = 'http://v4.biblesearch.americanbible.org/';
    /*
    * An American Bible Society API key.
    *
    * To obtain one see ...
    *
    * @var string
    * @see getKey()
    */
    private $_key = null;
    
    /**
     * The American Bible Society REST endpoint URL.
     *
     * @var     string
     * @see     getEndpointUrl(), setEndpointUrl()
     * @uses    REST_ENDPOINT_URL Used as a default.
     */
    private $_endpointUrl = ABS_Api::REST_ENDPOINT_URL;
    
    /**
     * Constructor.
     *
     * @param   string $key ABS API key.
     */
    public function __construct($key) {
        // key (required)
        if (isset($key)) {
            $this->_key = (string) $key;
        } else {
            throw new ABS_Exception('Must provide a valid API key.');
        }
    }
    
    /**
     * Get the API key.
     *
     * @return  string
     * @see     __construct()
     */
    public function getKey() {
        return $this->_key;
    }
    
    /**
     * Create a ABS_Request associated with this API object.
     *
     * @param   string $method Name of the ABS API method
     * @param   array $params Associative array of parameter name/value pairs
     * @return  object ABS_Request
     * @uses    ABS_Request
     */
    public function createRequest($url, $params = array()) {
        return new ABS_Request($this, $url, $params);
    }
    
    /**
     * Return the URL of the ABS endpoint.
     *
     * @return  string
     * @see     setEndpointUrl()
     */
    public function getEndpointUrl() {
        return $this->_endpointUrl;
    }
    
    /**
     * Return true if an exception will be thrown if the API returns a fail
     * for the request.
     *
     * @return  boolean
     * @see     setExceptionThrownOnFailure()
     */
    public function isExceptionThrownOnFailure()
    {
        return $this->_throwOnFail;
    }
    /**
     * Set an exception will be thrown if the API returns a fail for the
     * request.
     *
     * @param   boolean $throwOnFail
     * @return  void
     * @see     isExceptionThrownOnFailure()
     */
    public function setExceptionThrownOnFailure($throwOnFail)
    {
        $this->_throwOnFail = (boolean) $throwOnFail;
    }
}