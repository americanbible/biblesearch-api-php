<?php
class ABS_Exception extends Exception {
}
/**
 * Exception thrown when there is a problem connecting to the service.
 *
 * @package ABS
 * @author  Brian Smith <wisecounselor@gmail.com>
 */
class ABS_ConnectionException extends ABS_Exception {
    /**
     * The URL that was being requested when the problem occured.
     *
     * @var string
     */
    protected $_url;

    /**
     * Constructor
     *
     * @param string $message Error message
     * @param integer $code Error code
     * @param string $url URL accessed during failure
     */
    public function __construct($message = null, $code = null, $url = null) {
        parent::__construct($message, $code);
        $this->_url = (string) $url;
    }

    public function __toString() {
        $s = "exception '" . __CLASS__ . "' [{$this->code}]: {$this->message}\n";
        if (isset($this->_url)) {
            $s .= "URL: {$this->_url}\n";
        }
        $s .= "Stack trace:\n" . $this->getTraceAsString();
        return $s;
    }

    /**
     * Return the URL associated with the connection failure.
     *
     * @return string
     */
    public function getUrl() {
        return $this->_url;
    }
}
/**
 * Exception (optionally) thrown when an API method call fails.
 *
 * You can determine if this exception should be thrown by calling
 * ABS_Request's setExceptionThrownOnFailure() method.
 *
 * @package ABS
 * @author  Brian Smith <wisecounselor@gmail.com>
 */
class ABS_MethodFailureException extends ABS_Exception {
}

/**
 * Exception thrown when XML cannot be parsed.
 *
 * @package ABS
 * @author  Brian Smith <wisecounselor@gmail.com>
 */
class ABS_XmlParseException extends ABS_Exception {
    /**
     *
     * @var string
     */
    protected $_xml;

    /**
     * Constructor
     *
     * @param string $message
     * @param string $xml
     */
    public function __construct($message = null, $xml = null) {
        parent::__construct($message);
        $this->_xml = (string) $xml;
    }

    public function __toString() {
        $s = "exception '" . __CLASS__ . "' {$this->message}\n";
        if (isset($this->_xml)) {
            $s .= "XML: '{$this->_xml}'\n";
        }
        $s .= "Stack trace:\n" . $this->getTraceAsString();
        return $s;
    }

    /**
     * Return the un-parseable XML.
     *
     * @return string
     */
    public function getXml() {
        return $this->_xml;
    }
}
