<?php

/**
 * @version $Id$
 * @author  Brian Smith <wisecounselor@gmail.com>
 * @package ABS
 */

/**
 * ABS_Api includes the core classes.
 */
//require_once 'Api.php';

/**
 *
 * This class is responsible for:
 * - Converting the XML string returned by a Request object into a
 *   SimpleXML object.
 * - Determining the success or failure of the request.
 *
 * @package ABS
 * @author  Brian Smith <wisecounselorh@gmail.com>
 * @since   0.1.0
 */
class ABS_Response {
    
    /**
     * XML payload of the Response.
     *
     * @var object SimpleXMLElement
     */
    var $xml = null;
    
    /**
     * Constructor takes output from the http request
     *
     * @param string $restResult XML string .
     * @param boolean $throwOnFailed Should an exception be thrown when the
     *      response indicates failure?
     * @throws aBS_XmlParseException, ABS_MethodFailureException
     */
    function __construct($restResult, $throwOnFailed = false) {
        $restResult = trim($restResult);
        if ($restResult != '<?xml version="1.0" encoding="utf-8" ?>') {
            $xml = simplexml_load_string($restResult);
            if (false === $xml ) {
                if ($throwOnFailed) {
                    throw new ABS_XmlParseException('Could not parse XML.', $restResult);
                }
            } else {
                $this->xml = $xml;
            }
        }
    }

    public function __toString() {
        return $this->xml->asXML();
    }

    /**
     * Get the XML Object.
     *
     * @return  object SimpleXML
     * @see     SimpleXML::asXML()
     * @since   0.2.3
     */
    public function getXml() {
        return $this->xml;
    }
}
