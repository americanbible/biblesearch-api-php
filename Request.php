<?php

/**
 * @version $Id$
 * @author  Brian Smith <wisecounselor@gmail.com>
 * @package ABS
 */

/**
 * ABS_Api includes the core classes.
 */
//require_once 'Api.php';

class ABS_Request {
    /**
     * Number of seconds to wait while connecting to the server.
     */
    const TIMEOUT_CONNECTION = 20;
    /**
     * Total number of seconds to wait for a request.
     */
    const TIMEOUT_TOTAL = 50;

    /**
     * Phlickr_API object
     *
     * @var object
     */
    private $_api = null;
    /**
     * Name of the method.
     *
     * @var string
     */
    private $_method = null;
    /**
     * Parameters used in the last request.
     *
     * @var array
     */
    private $_params = array();
    /**
     * Should an exception be thrown when an API call fails?
     *
     * @var boolean
     */
    private $_throwOnFail = true;
    /**
     * holds data for post, put, delete requests
     *
     * @var string
     */
    public $_requestData;
    /**
     * the REST method being called
     *
     * @var string
     */
    public $_restMethod;
    
    /**
     * Constructor.
     *
     * @param  object ABS_API $api
     * @param  string $method The name of the method.
     * @param  array $params Associative array of parameter name/value pairs.
     */
    public function __construct(ABS_Api $api, $url, $params = array())
    {
        $this->_api = $api;
        if (!is_null($params)) {
            $this->_params = $params;
        }
        $this->_url = $url;
    }

    public function __toString()
    {
        return $this->buildUrl();
    }

    /**
     * Submit a GET request with to the specified URL with given parameters.
     *
     * @param   string $key - api key
     * @param   string $url - request URL
     * @param   array $params An optional array of parameter name/value
     *          pairs to include in the POST.
     * @param   integer $timeout The total number of seconds, including the
     *          wait for the initial connection, wait for a request to complete.
     * @return  string
     * @throws  ABS_ConnectionException
     */
    static function submitRequest($key, $url, $throwOnFail = true)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_USERPWD, $key . ':X');
        //curl_setopt($ch, CURLOPT_HEADER, true);
        //curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        $result = curl_exec($ch);
        $result = str_replace('&','&amp;',$result);
        
        // check for errors
        if (0 == curl_errno($ch)) {
            curl_close($ch);
            return $result;
        } else {
            if ($throwOnFail) {
                $ex = new ABS_ConnectionException('Request failed. ' . curl_error($ch), curl_errno($ch), $url);
                throw $ex;
            }
            curl_close($ch);
        }
    }
    /**
     * Submit a POST request with to the specified URL with given parameters.
     *
     * @param   string $key
     * @param   string $url
     * @param   string $data a string of xml to "stuff" in the POST.
     */
    static function doPost($key, $url, $data, $throwOnFail = true)
    {
        if (substr($data,0,4) != "<?xml") {
            $data = '<?xml version="1.0" encoding="utf-8"?>'.$data;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $key . ':X');
        curl_setopt($ch, CURLOPT_POST, true); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/xml'));
        //curl_setopt($ch, CURLOPT_HEADER, true);
        //curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        
        // check for errors
        if (0 == curl_errno($ch)) {
            curl_close($ch);
            return $result;
        } else {
            if ($throwOnFail) {
                $ex = new ABS_ConnectionException(
                'Request failed. ' . curl_error($ch), curl_errno($ch), $url);
                throw $ex;
            }
            curl_close($ch);
        }
    }
    /**
     * Submit a PUT request with to the specified URL with given parameters.
     *
     * @param   string $key - api key
     * @param   string $url - request URL
     * @param   array $params An optional array of parameter name/value
     *          pairs to include in the PUT.
     * @param   integer $timeout The total number of seconds, including the
     *          wait for the initial connection, wait for a request to complete.
     * @return  string
     * @throws  ABS_ConnectionException
     */
    static function doPut($key, $url, $data, $throwOnFail = true) {
        // make sure xml is properly formed
        if (substr($data,0,4) != "<?xml") {
            $data = '<?xml version="1.0" encoding="utf-8"?>'.$data;
        }
       
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERPWD, $key . ':X');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: '.strlen($data),                                                    'Content-type: application/xml')
                    );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $r = curl_getinfo($ch);
        
        return $response;
    }
    /**
     * Submit a DELETE request with to the specified URL with given parameters.
     *
     * @param   string $key - api key
     * @param   string $url - request URL
     * @param   array $params An optional array of parameter name/value
     *          pairs to include in the POST.
     * @param   integer $timeout The total number of seconds, including the
     *          wait for the initial connection, wait for a request to complete.
     * @return  boolean
     * @throws  ABS_ConnectionException
     */
    static function doDelete($key, $url, $data='', $throwOnFail = true) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERPWD, $key . ':X');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        if ('' != $data) {
            $length = strlen($data);
            $putData = tmpfile(); 
            fwrite($putData, $data); 
            fseek($putData, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: '.strlen($data)));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (200 == $responseCode) {
            return true;
        }
        return false;
    }
    /**
     * Return a reference to this Request's ABS_Api.
     *
     * @return  object ABS_Api
     * @see     __construct()
     */
    public function getApi()
    {
        return $this->_api;
    }

    /**
     * Return the name of the method
     *
     * @return  string
     * @see     __construct()
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Return the array of parameters.
     *
     * @return  array
     * @see     setParams()
     */
    public function &getParams()
    {
        return $this->_params;
    }

    /**
     * Assign parameters to the request.
     *
     * @param   array $params Associative array of parameter name/value pairs
     * @return  void
     * @see     __construct, getParams()
     */
    public function setParams($params)
    {
        if (is_null($params)) {
            $this->_params = array();
        } else {
            $this->_params = $params;
        }
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
    /**
     * Build a URL for this Request.
     * 
     **/
    static function encodeParams($params = array())
    {
        if (empty($params)) {
            return '';
        }
        $values = array();
        ksort($params);
        foreach($params as $key => $value) {
            $values[] = $key . '=' . urlencode($value);
        }
        
        return implode('&', $values);
    }
    /**
     * Build a signed URL for this Request.
     *
     * The Api will provide the key and secret and token values.
     *
     * @return  string
     * @see     buildUrl, ABS_Api::getKey()
     * @uses    encodeParams() to create a URL.
     */
    public function buildUrl()
    {
        $api = $this->getApi();
        $params = $this->getParams();
        $url = $api->getEndpointUrl() . $this->getUrl();
        if (strpos($url,'?') === false) {
            $url .= '?';
        }
        return $url . self::encodeParams($params);
    }

    /**
     * Execute a ABS API method.
     *
     * @return  object ABS_Response
     * @throws  ABS_XmlParseException, ABS_ConnectionException
     * @uses    submitRequest() to submit the request.
     * @uses    ABS_Cache to load and cached requests.
     * @uses    ABS_Response to return results.
     */
    public function execute($allowCache = false, $throwOnFail = true)
    {
        $api = $this->getApi();
        $url = $this->buildUrl();
        switch($this->_restMethod) {
            case 'PUT':
                $result = self::doPut($api->getKey(),$url, $this->_requestData, $throwOnFail);
                return new ABS_Response($result, $throwOnFail);
                break;
            case 'POST':
                $result = self::doPost($api->getKey(), $url, $this->_requestData, $throwOnFail);
                return new ABS_Response($result, $throwOnFail);
                break;
            case 'DELETE':
                $result = self::doDelete($api->getKey(), $url, $this->_requestData, $throwOnFail);
                break;
            default:
                $result = self::submitRequest($api->getKey(), $url, $throwOnFail);
                return new ABS_Response($result, $throwOnFail);
        }
    }
    public function setRestMethod($method) {
        $this->_restMethod = $method;
    }
    public function setRequestData($data) {
        $this->_requestData = $data;
    }
}
