<?php
/**
 * @version $Id$
 * @author  Brian Smith <wisecounselor@gmail.com>
 * @package ABS
 */

/**
 * A simple iterator class to be used to iterate over an array of objects
 * return as part of an overal SimpleXML object.  The iterator takes an iterable
 * object in its constructor.  What makes the object iterable is the presence of
 * a method, getData() which will set the data over which the class can iterate.
 *
 * This class is built to iterate over data returned in the various ABS classes
 * used to query the ABS api and get data
 *
 * @author  Brian Smith <wisecounselor@gmail.com>
 * @package ABS
 */

class ABS_Iterator implements Iterator {
    /**
     * The data over which the class will iterate
     *
     * @var array
     */
    private $_data;
    /**
     * The current position of the data pointer
     *
     * @var integer
     */
    private $_position;
    
    /**
     *  Constructor
     *
     *  @param iterable object $obj
     * 
     */
    function __construct($obj) {
        if (! method_exists($obj,'getData')) {
            throw new ABS_Exception('The object to be iterated must have a getData method');
        }
        $this->_data = $obj->getData();
        $this->rewind();
    }
    /**
     * Rewind the data array by setting position to 0
     */
    
    function rewind() {
        $this->_position = 0;
    }
    /**
     * Return current element
     */
    
    function current() {
        return $this->_data[$this->_position];
    }
    /**
     * Increment data pointer
     */
    function next() {
        $this->_position++;
    }
    /**
     * Check to make sure pointer value points to a valid object in array
     */
    
    function valid() {
        return isset($this->_data[$this->_position]);
    }
    /**
     * Return key of current value
     */
    function key() {
        return $this->_position;
    }
}
?>