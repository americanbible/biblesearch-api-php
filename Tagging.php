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

    
    
class ABS_Tagging extends ABS_Base {
    
    /**
     * The name of the XML element in the response that defines the object.
     *
     * @var string
     */
    const XML_RESPONSE_ELEMENT = 'tagging';
    
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
                return 'user/taggings.xml';
                break;
            case 'listbytag':
                return 'user/taggings/' . urlencode($this->_tag_name) . '.xml';
                break;
            case 'show':
                return 'taggings/' . $this->_tag_id . '.xml';
                break;
            case 'create':
                return 'user/taggings.xml';
                break;
            case 'update':
                return 'taggings/' . $this->_tag_id . '.xml';
                break;
            case 'delete':
                return 'taggings/' . $this->_tag_id . '.xml';
                break;
            case 'deletebytag':
                return 'user/taggings/' . urlencode($this->_tag_name) . '.xml';
                break;
            default:
                throw new ABS_Exception('Invalid method ' . $method);
        }
    }
    
    /*
    * Method to list all taggings for user
    * 
    * @return  xml SimpleXML object
    */
    public function listTaggings() {
        return $this->requestXml('list');
    }
    
    /*
    * Method to list all taggings by a specific tag
    * 
    * @param   string $tag_name
    * @return  xml SimpleXML object
    */
    public function listByTag($tag_name) {
        $this->_tag_name = $tag_name;
        return $this->requestXml('listbytag');
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
    
    /*
    * Create a new tagging
    * 
    * @param   string $xml
    * @return  xml SimpleXML object
    */
    public function addTag($xml) {
        $this->setRestMethod('POST');
        $this->setRequestData($xml);
        return $this->requestXml('create');
    }
    /*
    * Update a tagging
    *
    * @param   integer $tag_id
    * @param   string $xml
    * @return  xml SimpleXML object
    */
    public function updateTag($tag_id, $xml) {
        $this->_tag_id = $tag_id;
        $this->setRestMethod('PUT');
        $this->setRequestData($xml);
        return $this->requestXml('update');
    }
    /*
    * Delete a tagging by tag id
    * 
    * @param   string $method
    * @return  xml SimpleXML object
    */
    public function deleteTag($tag_id) {
        $this->_tag_id = $tag_id;
        $this->setRestMethod('DELETE');
        return $this->requestXml('delete');
    }
    /**
    * Delete a tagging by the tag name, such as 'peace'
    * 
    * @param   string $method
    * @return  xml SimpleXML object
    */
    public function deleteByTag($tag_name) {
        $this->_tag_name = $tag_name;
        $this->setRestMethod('DELETE');
        return $this->requestXml('deletebytag');
    }
}