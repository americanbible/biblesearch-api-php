<?php
/**
 * @version $Id$
 * @author  Brian Smith <wisecounselor@gmail.com>
 * @package ABS
 */

class ABS_Version extends ABS_Base {
    
    /**
     * The name of the XML element in the response that defines the object.
     *
     * @var string
     */
    const XML_RESPONSE_ELEMENT = 'version';
    
    function __construct(ABS_Api $api, $version_id = '') {
        parent::__construct($api, self::XML_RESPONSE_ELEMENT);
        $this->_version_id = $version_id;
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
            case 'books':
				if (! empty($this->_testament_id)) {
					return 'versions/' . $this->_version_id.'/books.xml?testament='.$this->_testament_id;
                } else {
					return 'versions/' . $this->_version_id.'/books.xml';
				}
                break;
				
            case 'list':
                if (! empty($this->_lang)) {
                    $url = 'versions.xml?language='.$this->_lang;
                } else {
                    $url = 'versions.xml';
                }
                return $url;
                break;
				
            case 'show':
                return 'versions/' . $this->_version_id . '.xml';
                break;
            default:
                throw new ABS_Exception('Invalid method ' . $this->_method);
        }
    }
    /*
     * Method to list all versions of the Bible
     * 
     * @return  xml SimpleXML object
     */
    public function listVersions($lang = '') {
        $this->setLanguage($lang);
        return $this->requestXml('list');
    }
	
    /*
    * Method to list all books in a specific version of the Bible
    * 
    * @param   string $version_id
	* @param   string $testament_id
    * @return  xml SimpleXML object
    */
    public function books($version_id = '', $testament_id = '') {
        if (! empty($version_id)) {
            $this->setVersion($version_id);
        }        
		
		if (! empty($testament_id)) {
            $this->setTestament($testament_id);
        }
		
        $this->setResponseElement('book');
        return $this->requestXml('books');
    }
	
    /*
    * Method to list show the details of a specific version of the Bible
    * 
    * @param   string $version_id
    * @return  xml SimpleXML object
    */
    public function show($version_id = '') {
        if (empty($version_id)) {
            $version_id = $this->_version_id;
        }
        $this->setVersion($version_id);
        $xml = $this->requestXml('show');
        return $xml;
    }
}