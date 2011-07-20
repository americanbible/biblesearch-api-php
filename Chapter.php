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

class ABS_Chapter extends ABS_Base {
    
    /**
     * The name of the XML element in the response that defines the object.
     *
     * @var string
     */
    const XML_RESPONSE_ELEMENT = 'chapter';
    
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
            case 'verses':
                return implode('',array('chapters/',
                                        $this->_version_id,
                                        ':',
                                        $this->_book_id,
                                        '.',
                                        $this->_chapter,
                                        '/verses.xml',
                                       )
                              );
                break;
            case 'list':
                return implode('',array('books/',
                                        $this->_version_id,
                                        ':',
                                        $this->_book_id,
                                        '/chapters.xml'
                                       )
                              );
                break;
            case 'show':
                return implode('',array('chapters/',
                                        $this->_version_id,
                                        ':',
                                        $this->_book_id,
                                        '.',
                                        $this->_chapter,
                                        '.xml'
                                       )
                              );
                break;
            default:
                throw new ABS_Exception('Invalid method ' . $method);
        }
    }
    /*
     * Method to list all verses of a chapter of the Bible
     * 
     * @return  xml SimpleXML object
     */
    public function verses($chapter = '') {
        $this->__setXpath('chapter/verses/verse');
        if (empty($this->_version_id) || is_null($this->_version_id)) {
            throw new ABS_Exception('The Version cannot be null or empty');
        }
        if (empty($chapter)) {
            if (empty($this->_chapter) || is_null($this->_chapter)) {
                throw new ABS_Exception('The Chapter cannot be null or empty');
            }
        } else {
            $this->setChapter($chapter);
        }
        
        if (empty($this->_book_id) || is_null($this->_book_id)) {
            throw new ABS_Exception('The Book cannot be null or empty');
        }
        return $this->requestXml('verses');
    }
    /*
     * Method to list a group of verses from a specific version, chapter, and book
     * 
     * @return  xml SimpleXML object
     */
    public function references($chapter = '', $start = '', $end = '') {
        $this->addParam('start',$start);
        $this->addParam('end',$end);
        return $this->verses($chapter);
    }
    /*
     * Method to list all books of the Bible
     * 
     * @return  xml SimpleXML object
     */
    public function listChapters($version = '', $book = '') {
		if ( ! empty( $version ) ) {
			$this->setVersion( $version );
			}
			
		if ( ! empty( $book ) ) {
			$this->setBook( $book );
		}
	
        return $this->requestXml('list');
    }
    
    /*
    * Method to list show the details of a specific Bookof the Bible
    * 
    * @param   string $book
    * @return  xml SimpleXML object
    */
    public function show($chapter = '') {
        $this->setChapter($chapter);
        $this->__validateChapter();
        $this->__validateBook();
        $this->__validateVersion();
        $this->setChapter($chapter);
        $xml = $this->requestXml('show');
        return $xml;
    }
}