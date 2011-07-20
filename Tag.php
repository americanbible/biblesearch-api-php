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

    
    
class ABS_Tag extends ABS_Base {
    
    /**
     * The name of the XML element in the response that defines the object.
     *
     * @var string
     */
    const XML_RESPONSE_ELEMENT = 'tag';
    
    /**
     * The  id for a specific tag
     *
     * @var integer
     */
    protected $_tag_id;
    
    /**
     * The  name for a specific tag
     *
     * @var string
     */
    protected $_tag_name;
    
    function __construct(ABS_Api $api) {
        parent::__construct($api, self::XML_RESPONSE_ELEMENT);
        $this->__setXpath('tag/references/reference');
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
            case 'list':
                return 'tags.xml';
                break;
            case 'usertags':
                return 'user/tags.xml';
                break;
            case 'show':
                return 'tags/' . $this->_tag_id . '.xml';
                break;
            case 'showbyname':
                return 'tags/' . urlencode($this->_tag_name) . '.xml';
                break;
            default:
                throw new ABS_Exception('Invalid method ' . $method);
        }
    }
    
    /*
    * Method to list all tags site-wide
    * 
    * @return  xml SimpleXML object
    */
    public function listTags() {
        return $this->requestXml('list');
    }
    
    /*
    * Method to list all tags for this user
    * 
    * @return  xml SimpleXML object
    */
    public function listUserTags() {
        return $this->requestXml('usertags');
    }
    
    /*
    * Show specific tag by id
    * 
    * @param   integer $tag_id
    * @return  xml SimpleXML object
    */
    public function show($tag_id) {
        $this->_tag_id = $tag_id;
        return $this->requestXml('show');
    }
    /*
    * Show specific tag by name
    * 
    * @param   string $tag_name
    * @return  xml SimpleXML object
    */
    public function showByName($tag_name) {
        $this->_tag_name = $tag_name;
        return $this->requestXml('showbyname');
    }
}