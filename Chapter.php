<?php
/**
 * @version $Id$
 * @author  Brian Smith <wisecounselor@gmail.com>
 * @package ABS
 */

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
     * Use this if you have received a ChapterID from a previous api call to set the version, book and chapter.
     */ 
    public function setFromReceivedChapterID($ID) {
    	if ( strpos( $ID, ':') !== false && strpos ( $ID, '.' ) !== false ) {
    		$parts = explode(':', $ID);
    		if ( count( $parts ) == 2 ) {
    			$this->_version_id = $parts[0];
    			$parts = explode('.', $parts[1]);
	    		if ( count( $parts ) == 2 ) {
	    			$this->_book_id = $parts[0];
	    			$this->_chapter = 0 + $parts[1]; // Easy way to force this to be an int
	    		}
				elseif ( count ( $parts ) == 3 ) {
					$this->_book_id = $parts[0].'.'.$parts[1];
					$this->_chapter = 0 + $parts[2];
				}
	    		else {
	    			throw new ABS_Exception( 'Invalid Chapter ID' . $ID );
	    		}
    		}
    		else {
    			throw new ABS_Exception( 'Invalid Chapter ID' . $ID );
    		}
    	}
    	else {
    		throw new ABS_Exception( 'Invalid Chapter ID' . $ID );
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