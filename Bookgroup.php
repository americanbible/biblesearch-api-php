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

class ABS_Bookgroup extends ABS_Base {
    
    /**
     * The name of the XML element in the response that defines the object.
     *
     * @var string
     */
    const XML_RESPONSE_ELEMENT = 'bookgroup';
    
    /**
     * A group ID defining a specific book group
     *
     * @var string
     */
    private $_group_id;
    
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
                return 'bookgroups/' . $this->_group_id . '/books.xml';
                break;
            case 'allbookgroups':
                return 'bookgroups.xml';
                break;
            case 'showbookgroup':
                return 'bookgroups/' . $this->_group_id . '.xml';
                break;
            default:
                throw new ABS_Exception('Invalid method ' . $method);
        }
    }
    
    /*
    * Method to list all books in a specific version of the Bible, specific book group
    * 
    * @param   string $group_id
    * @return  xml SimpleXML object
    */
    public function show($group_id) {
        $this->setGroup($group_id);
        return $this->requestXml('list');
    }
    /*
    * Method to list all book groups
    * 
    * @return  xml SimpleXML object
    */
    public function listBookGroups() {
        return $this->requestXml('allbookgroups');
    }
    /*
     * Method to set the group id being processed
     * 
     * @param   string $version_id
      */
    public function setGroup($group_id) {
        $this->_group_id = $group_id;
    }
}