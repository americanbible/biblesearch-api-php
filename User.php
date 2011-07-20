<?php
/**
 * @version $Id$
 * @author  Brian Smith <wisecounselor@gmail.com>
 * @package ABS
 */
    /*
    * Include the ABS Api, core classes.
    */
    require_once 'Api.php';
    /*
    * Include the ABS Base object class, core classes.
    */
    require_once 'Base.php';

class ABS_User extends ABS_Base {
    
    /**
     * The name of the XML element in the response that defines the object.
     *
     * @var string
     */
    const XML_RESPONSE_ELEMENT = 'user';
    
    function __construct(ABS_Api $api) {
        parent::__construct($api, self::XML_RESPONSE_ELEMENT);
    }
    /*
     * Method to return the appropriate URL ending based on the method being executed
     * 
     * @param   string $method
     * @return  string
     * @throws  ABS_Exception
     */
    
    protected function getUrl($method) {
        switch($method) {
            case 'user':
                return 'user.xml';
                break;
            case 'update':
                return 'user.xml';
                break;
            default:
                throw new ABS_Exception('Invalid method ' . $method);
        }
    }
    
    /*
    * Method to get the details of current api user account
    * 
    * @return  xml SimpleXML object
    */
    public function getUser() {
        return $this->requestXml('user');
    }
    /*
    * Method to update a user account
    * 
    * @param   string $xml
    * @return  xml SimpleXML object
    */
    public function updateUser($xml) {
        $test = simplexml_load_string($xml);
        if (false === $test) {
            throw new ABS_XmlParseException('Could not parse XML.', $xml);
        }
        $this->setRestMethod('PUT');
        $this->setRequestData($xml);
        return $this->requestXml('update');
    }
}