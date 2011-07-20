<?php
/**
 * @version $Id$
 * @author  Brian Smith <wisecounselor@gmail.com>, Mark Bradshaw <mbradshaw@americanbible.org>
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

class ABS_Verse extends ABS_Base {
    
    /**
     * The name of the XML element in the response that defines the object.
     *
     * @var string
     */
    const XML_RESPONSE_ELEMENT = 'verse';
    
     /**
     * A numeric verse of a chapter of a book
     *
     * @var integer
     */
    private $_verse;
    
    public $verse_id;
    
    function __construct(ABS_Api $api) {
        parent::__construct($api, self::XML_RESPONSE_ELEMENT);
        $this->__setXpath('verse');
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
            case 'passages':
                return 'passages.xml?q[]=';
            case 'list':
                return implode('',array('chapters/',
                                        $this->__buildChapterID(),
                                        '/verses.xml',
                                       )
                              );
                break;
            case 'verses':
                return 'verses.xml';
                break;
            case 'show':
                
                return implode('',array('verses/',
                                    $this->__buildChapterID(),
                                    '.',
                                    $this->_verse,
                                    '.xml'
                                   )
                          );
                
                break;
            case 'showbyid':
                return 'verses/'. $this->verse_id . '.xml';
                break;
            default:
                throw new ABS_Exception('Invalid method ' . $method);
        }
    }
    
    private function __buildChapterID() {
        return implode('',array(
                                $this->_version_id,
                                ':',
                                $this->_book_id,
                                '.',
                                $this->_chapter
                               )
                              );
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
     * Method to list all verses beloning to the currently set chapter.
     * 
     * @param   string $method
     * @return  xml SimpleXML object
     */
    public function listVerses($start='',$end='') {
        $this->__validateAll();
        $this->__setXpath('verses');
        if (!empty($start) && ! empty($end)) {
            $this->addParam('start',$start);
            $this->addParam('end',$end);
        }
        return $this->requestXml('list');
    }
    /*
     * Method to search all books of the Bible by keyword.  For a passage search use listVerses.
	 * If you are unsure, use the Search object to find out where to go next.
     * 
     * @param   array $params
     * 
     *  keyword: the words(s) you are searching for. REQUIRED
     *  precision: may be “all” to return search results with all keywords or
     *      “any” to return search results where any keywords appear
     *  exclude: any keywords that should not appear in the search results
     *  spelling: may be “yes” to search for keywords like the terms you
     *      submitted if your keywords return no results
     *  version: may be one or several of the version “version” values
     *  language: may be one or several of version “language” values
     *  testament: may be one or several of the book “testament” values
     *  book: may be one or several of the book “abbreviation” values
     *  sort_order: may be either “canonical” or “relevance”
     *  offset: may be an integer to request records returned after this number
     *      of records. That is, if the offset is 1000, the records returned
     *      will start with the one thousand first record.
     *  limit: may be an integer to request a maximum number of records be
     *  returned. If provided, limit must be less than or equal to 500.
     *
     *  @return  xml SimpleXML object
     */
    public function search($params = array()) {
        $this->__setXpath('result/verses/verse');
        if (!isset($params['keyword']) || empty($params['keyword'])) {
            throw new ABS_Exception("The parameter 'keyword' is required");
        }
        foreach ($this->__allowedSearchParams() as $key) {
            if (isset($params[$key])) {
                $this->addParam($key,$params[$key]);
            }
        }
        return $this->requestXml('verses');
    }
    private function __allowedSearchParams() {
        return array('keyword',
                     'precision',
                     'exclude',
                     'spelling',
                     'version',
                     'language',
                     'testament',
                     'book',
                     'sort_order',
                     'offset',
                     'limit'
                     );
    }
    /*
    * Method to list show the details of a specific Bookof the Bible
    * 
    * @param   string $book
    * @return  xml SimpleXML object
    */
    public function show($verse) {
        $this->setVerse($verse);
        $this->__setXpath('verses/verse');
        $this->__validateAll();
        $this->__validateVerse();
        $xml =  $this->requestXml('show');
        return $xml;
    }
     /*
    * Method to list show the details of a specific Bookof the Bible
    * 
    * @param   string $book
    * @return  xml SimpleXML object
    */
    public function showByID($verse_id) {
        $this->verse_id = $verse_id;
        $this->__setXpath('verse');
        $xml =  $this->requestXml('showbyid');
        return $xml;
    }
    /**
    * Method to list show the details of a specific Passage / Book using passage string
    * Version ID must be set (ex: NASB)
    * 
    * Ex: john 3:1
    *
    * @param   string $passage
    * @return  xml SimpleXML object
    */
    public function passages($passage) {
        // set xpath path for use with iterator object
        $this->__setXpath('result/passages/passage');
        if (empty($this->_version_id) || is_null($this->_version_id)) {
            throw new ABS_Exception('The Version cannot be null or empty');
        }
        $this->addParam('passage',$passage);
        $this->addParam('version',$this->_version_id);
        return $this->requestXml('passages');
    }
    /**
     * Method to set the verse of a chapter of a book of the Bible
     * 
     * @param   integer chapter
    */
    public function setVerse($verse) {
        if (empty($verse) || is_null($verse)) {
            throw new ABS_Exception('The Verse cannot be null or empty');
        }
        if (! is_int($verse)) {
            throw new Exception('The Verse must be an integer value');
        }
        $this->_verse = $verse;
    }
    private function __validateVerse() {
        if (empty($this->_verse)  || is_null($this->_verse)) {
            throw new ABS_Exception('The Verse cannot be null or empty');
        }
    }
}