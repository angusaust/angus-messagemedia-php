<?php
/**
 * File for WsdlClass to communicate with SOAP service
 *
 * Copyright 2014 MessageMedia
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License.
 * You may obtain a copy of the License at http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require('MMSoapClient.php');

class WsdlClass extends stdClass implements ArrayAccess, Iterator, Countable {
    /**
     * Option key to define WSDL url
     *
     * @var string
     */
    const WSDL_URL = 'wsdl_url';
    /**
     * Constant to define the default WSDL URI
     *
     * @var string
     */
    const VALUE_WSDL_URL = 'https://soap.m4u.com.au/?wsdl';
    /**
     * Option key to define WSDL login
     *
     * @var string
     */
    const WSDL_LOGIN = 'wsdl_login';
    /**
     * Option key to define WSDL password
     *
     * @deprecated use WSDL_PASSWORD instead
     * @var string
     */
    const WSDL_PASSWD = 'wsdl_password';
    /**
     * Option key to define WSDL password
     *
     * @var string
     */
    const WSDL_PASSWORD = 'wsdl_password';
    /**
     * Option key to define WSDL trace option
     *
     * @var string
     */
    const WSDL_TRACE = 'wsdl_trace';
    /**
     * Option key to define WSDL exceptions
     *
     * @deprecated use WSDL_EXCEPTIONS instead
     * @var string
     */
    const WSDL_EXCPTS = 'wsdl_exceptions';
    /**
     * Option key to define WSDL exceptions
     *
     * @var string
     */
    const WSDL_EXCEPTIONS = 'wsdl_exceptions';
    /**
     * Option key to define WSDL cache_wsdl
     *
     * @var string
     */
    const WSDL_CACHE_WSDL = 'wsdl_cache_wsdl';
    /**
     * Option key to define WSDL stream_context
     *
     * @var string
     */
    const WSDL_STREAM_CONTEXT = 'wsdl_stream_context';
    /**
     * Option key to define WSDL soap_version
     *
     * @var string
     */
    const WSDL_SOAP_VERSION = 'wsdl_soap_version';
    /**
     * Option key to define WSDL compression
     *
     * @var string
     */
    const WSDL_COMPRESSION = 'wsdl_compression';
    /**
     * Option key to define WSDL encoding
     *
     * @var string
     */
    const WSDL_ENCODING = 'wsdl_encoding';
    /**
     * Option key to define WSDL connection_timeout
     *
     * @var string
     */
    const WSDL_CONNECTION_TIMEOUT = 'wsdl_connection_timeout';
    /**
     * Option key to define WSDL typemap
     *
     * @var string
     */
    const WSDL_TYPEMAP = 'wsdl_typemap';
    /**
     * Option key to define WSDL user_agent
     *
     * @var string
     */
    const WSDL_USER_AGENT = 'wsdl_user_agent';
    /**
     * Option key to define WSDL features
     *
     * @var string
     */
    const WSDL_FEATURES = 'wsdl_features';
    /**
     * Option key to define WSDL keep_alive
     *
     * @var string
     */
    const WSDL_KEEP_ALIVE = 'wsdl_keep_alive';
    /**
     * Option key to define WSDL proxy_host
     *
     * @var string
     */
    const WSDL_PROXY_HOST = 'wsdl_proxy_host';
    /**
     * Option key to define WSDL proxy_port
     *
     * @var string
     */
    const WSDL_PROXY_PORT = 'wsdl_proxy_port';
    /**
     * Option key to define WSDL proxy_login
     *
     * @var string
     */
    const WSDL_PROXY_LOGIN = 'wsdl_proxy_login';
    /**
     * Option key to define WSDL proxy_password
     *
     * @var string
     */
    const WSDL_PROXY_PASSWORD = 'wsdl_proxy_password';
    /**
     * Option key to define WSDL local_cert
     *
     * @var string
     */
    const WSDL_LOCAL_CERT = 'wsdl_local_cert';
    /**
     * Option key to define WSDL passphrase
     *
     * @var string
     */
    const WSDL_PASSPHRASE = 'wsdl_passphrase';
    /**
     * Option key to define WSDL authentication
     *
     * @var string
     */
    const WSDL_AUTHENTICATION = 'wsdl_authentication';
    /**
     * Option key to define WSDL ssl_method
     *
     * @var string
     */
    const WSDL_SSL_METHOD = 'wsdl_ssl_method';
    /**
     * Soapclient called to communicate with the actual SOAP Service
     *
     * @var SoapClient
     */
    private static $soapClient;
    /**
     * Contains Soap call result
     *
     * @var mixed
     */
    private $result;
    /**
     * Contains last errors
     *
     * @var array
     */
    private $lastError;
    /**
     * Array that contains values when only one parameter is set when calling __construct method
     *
     * @var array
     */
    private $internArrayToIterate;
    /**
     * Bool that tells if array is set or not
     *
     * @var bool
     */
    private $internArrayToIterateIsArray;
    /**
     * Items index browser
     *
     * @var int
     */
    private $internArrayToIterateOffset;

    /**
     * Constructor
     *
     * @uses WsdlClass::setLastError()
     * @uses WsdlClass::initSoapClient()
     * @uses WsdlClass::initInternArrayToIterate()
     * @uses WsdlClass::_set()
     * @param array $_arrayOfValues   SoapClient options or object attribute values
     * @param bool  $_resetSoapClient allows to disable the SoapClient redefinition
     * @return WsdlClass
     */
    public function __construct($_arrayOfValues = array(), $_resetSoapClient = true) {
        $this->setLastError(array());
        /**
         * Init soap Client
         * Set default values
         */
        if ($_resetSoapClient)
            $this->initSoapClient($_arrayOfValues);
        /**
         * Init array of values if set
         */
        $this->initInternArrayToIterate($_arrayOfValues);
        /**
         * Generic set methods
         */
        if (is_array($_arrayOfValues) && count($_arrayOfValues)) {
            foreach ($_arrayOfValues as $name => $value)
                $this->_set($name, $value);
        }
    }

    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     *
     * @param array  $_array     the exported values
     * @return WsdlClass|null
     */
    public static function __set_state(array $_array): object {
        return self::create($_array, __CLASS__);
    }

    /**
     * Generic method that returns an object instantiated with the values.
     *
     * @uses WsdlClass::_set()
     * @param array  $_array     the values
     * @param string $_className (used by derived classes in order to always call this method)
     * @return $_className|null
     */
    protected static function create(array $_array, $_className): object {
        if (class_exists($_className)) {
            $object = @new $_className();
            if (is_object($object) && is_subclass_of($object, 'WsdlClass')) {
                foreach ($_array as $name => $value)
                    $object->_set($name, $value);
            }
            return $object;
        }
        else
            throw new Exception("Attempted to create a class that does not exist.");
    }

    /**
     * Static method getting current SoapClient
     *
     * @return SoapClient
     */
    public static function getSoapClient() {
        return self::$soapClient;
    }

    /**
     * Static method setting current SoapClient
     *
     * @param SoapClient $_soapClient
     * @return SoapClient
     */
    protected static function setSoapClient(SoapClient $_soapClient) {
        return (self::$soapClient = $_soapClient);
    }

    /**
     * Method initiating SoapClient
     *
     * @uses MMClassMap::classMap()
     * @uses WsdlClass::getDefaultWsdlOptions()
     * @uses WsdlClass::getSoapClientClassName()
     * @uses WsdlClass::setSoapClient()
     * @param array $_wsdlOptions WSDL options
     * @return void
     */
    public function initSoapClient($_wsdlOptions) {
        if (class_exists('MMClassMap', true)) {
            $wsdlOptions             = array();
            $wsdlOptions['classmap'] = MMClassMap::classMap();
            $defaultWsdlOptions      = self::getDefaultWsdlOptions();
            foreach ($defaultWsdlOptions as $optioName => $optionValue) {
                if (array_key_exists($optioName, $_wsdlOptions) && !empty($_wsdlOptions[$optioName]))
                    $wsdlOptions[str_replace('wsdl_', '', $optioName)] = $_wsdlOptions[$optioName];
                elseif (!empty($optionValue))
                    $wsdlOptions[str_replace('wsdl_', '', $optioName)] = $optionValue;
            }
            if (array_key_exists(str_replace('wsdl_', '', self::WSDL_URL), $wsdlOptions)) {
                $wsdlUrl = $wsdlOptions[str_replace('wsdl_', '', self::WSDL_URL)];
                unset($wsdlOptions[str_replace('wsdl_', '', self::WSDL_URL)]);
                $soapClientClassName = self::getSoapClientClassName();
                self::setSoapClient(new $soapClientClassName($wsdlUrl, $wsdlOptions));
            }
        }
    }

    /**
     * Returns the SoapClient class name to use to create the instance of the SoapClient.
     * The SoapClient class is determined based on the package name.
     * If a class is named as {}SoapClient, then this is the class that will be used.
     * Be sure that this class inherits from the native PHP SoapClient class and this class has been loaded or can be loaded.
     * The goal is to allow the override of the SoapClient without having to modify this generated class.
     * Then the overridding SoapClient class can override for example the SoapClient::__doRequest() method if it is needed.
     *
     * @return string
     */
    public static function getSoapClientClassName() {
        if (class_exists('MMSoapClient') && is_subclass_of('MMSoapClient', 'SoapClient'))
            return 'MMSoapClient';
        else
            return 'SoapClient';
    }

    /**
     * Method returning all default options values
     *
     * @uses WsdlClass::WSDL_CACHE_WSDL
     * @uses WsdlClass::WSDL_COMPRESSION
     * @uses WsdlClass::WSDL_CONNECTION_TIMEOUT
     * @uses WsdlClass::WSDL_ENCODING
     * @uses WsdlClass::WSDL_EXCEPTIONS
     * @uses WsdlClass::WSDL_FEATURES
     * @uses WsdlClass::WSDL_LOGIN
     * @uses WsdlClass::WSDL_PASSWORD
     * @uses WsdlClass::WSDL_SOAP_VERSION
     * @uses WsdlClass::WSDL_STREAM_CONTEXT
     * @uses WsdlClass::WSDL_TRACE
     * @uses WsdlClass::WSDL_TYPEMAP
     * @uses WsdlClass::WSDL_URL
     * @uses WsdlClass::VALUE_WSDL_URL
     * @uses WsdlClass::WSDL_USER_AGENT
     * @uses WsdlClass::WSDL_PROXY_HOST
     * @uses WsdlClass::WSDL_PROXY_PORT
     * @uses WsdlClass::WSDL_PROXY_LOGIN
     * @uses WsdlClass::WSDL_PROXY_PASSWORD
     * @uses WsdlClass::WSDL_LOCAL_CERT
     * @uses WsdlClass::WSDL_PASSPHRASE
     * @uses WsdlClass::WSDL_AUTHENTICATION
     * @uses WsdlClass::WSDL_SSL_METHOD
     * @uses SOAP_SINGLE_ELEMENT_ARRAYS
     * @uses SOAP_USE_XSI_ARRAY_TYPE
     * @return array
     */
    public static function getDefaultWsdlOptions() {
        return array(
            self::WSDL_CACHE_WSDL         => WSDL_CACHE_DISK,
            self::WSDL_COMPRESSION        => null,
            self::WSDL_CONNECTION_TIMEOUT => null,
            self::WSDL_ENCODING           => null,
            self::WSDL_EXCEPTIONS         => true,
            self::WSDL_FEATURES           => SOAP_SINGLE_ELEMENT_ARRAYS | SOAP_USE_XSI_ARRAY_TYPE,
            self::WSDL_LOGIN              => null,
            self::WSDL_PASSWORD           => null,
            self::WSDL_SOAP_VERSION       => SOAP_1_1,
            self::WSDL_STREAM_CONTEXT     => null,
            self::WSDL_TRACE              => true,
            self::WSDL_TYPEMAP            => null,
            self::WSDL_URL                => self::VALUE_WSDL_URL,
            self::WSDL_USER_AGENT         => null,
            self::WSDL_PROXY_HOST         => null,
            self::WSDL_PROXY_PORT         => null,
            self::WSDL_PROXY_LOGIN        => null,
            self::WSDL_PROXY_PASSWORD     => null,
            self::WSDL_LOCAL_CERT         => null,
            self::WSDL_PASSPHRASE         => null,
            self::WSDL_AUTHENTICATION     => null,
            self::WSDL_SSL_METHOD         => null);
    }

    /**
     * Allows to set the SoapClient location to call
     *
     * @uses WsdlClass::getSoapClient()
     * @uses SoapClient::__setLocation()
     * @param string $_location
     */
    public function setLocation($_location) {
        return self::getSoapClient() ? self::getSoapClient()->__setLocation($_location) : false;
    }

    /**
     * Returns the last request content as a DOMDocument or as a formated XML String
     *
     * @see  SoapClient::__getLastRequest()
     * @uses WsdlClass::getSoapClient()
     * @uses WsdlClass::getFormatedXml()
     * @uses SoapClient::__getLastRequest()
     * @param bool $_asDomDocument
     * @return DOMDocument|string
     */
    public function getLastRequest($_asDomDocument = false) {
        if (self::getSoapClient())
            return self::getFormatedXml(self::getSoapClient()->__getLastRequest(), $_asDomDocument);
        return null;
    }

    /**
     * Returns the last response content as a DOMDocument or as a formated XML String
     *
     * @see  SoapClient::__getLastResponse()
     * @uses WsdlClass::getSoapClient()
     * @uses WsdlClass::getFormatedXml()
     * @uses SoapClient::__getLastResponse()
     * @param bool $_asDomDocument
     * @return DOMDocument|string
     */
    public function getLastResponse($_asDomDocument = false) {
        if (self::getSoapClient())
            return self::getFormatedXml(self::getSoapClient()->__getLastResponse(), $_asDomDocument);
        return null;
    }

    /**
     * Returns the last request headers used by the SoapClient object as the original value or an array
     *
     * @see  SoapClient::__getLastRequestHeaders()
     * @uses WsdlClass::getSoapClient()
     * @uses WsdlClass::convertStringHeadersToArray()
     * @uses SoapClient::__getLastRequestHeaders()
     * @param bool $_asArray allows to get the headers in an associative array
     * @return null|string|array
     */
    public function getLastRequestHeaders($_asArray = false) {
        $headers = self::getSoapClient() ? self::getSoapClient()->__getLastRequestHeaders() : null;
        if (is_string($headers) && $_asArray)
            return self::convertStringHeadersToArray($headers);
        return $headers;
    }

    /**
     * Returns the last response headers used by the SoapClient object as the original value or an array
     *
     * @see  SoapClient::__getLastResponseHeaders()
     * @uses WsdlClass::getSoapClient()
     * @uses WsdlClass::convertStringHeadersToArray()
     * @uses SoapClient::__getLastRequestHeaders()
     * @param bool $_asArray allows to get the headers in an associative array
     * @return null|string|array
     */
    public function getLastResponseHeaders($_asArray = false) {
        $headers = self::getSoapClient() ? self::getSoapClient()->__getLastResponseHeaders() : null;
        if (is_string($headers) && $_asArray)
            return self::convertStringHeadersToArray($headers);
        return $headers;
    }

    /**
     * Returns a XML string content as a DOMDocument or as a formated XML string
     *
     * @uses DOMDocument::loadXML()
     * @uses DOMDocument::saveXML()
     * @param string $_string
     * @param bool   $_asDomDocument
     * @return DOMDocument|string|null
     */
    public static function getFormatedXml($_string, $_asDomDocument = false) {
        if (!empty($_string) && class_exists('DOMDocument')) {
            $dom                     = new DOMDocument('1.0', 'UTF-8');
            $dom->formatOutput       = true;
            $dom->preserveWhiteSpace = false;
            $dom->resolveExternals   = false;
            $dom->substituteEntities = false;
            $dom->validateOnParse    = false;
            if ($dom->loadXML($_string))
                return $_asDomDocument ? $dom : $dom->saveXML();
        }
        return $_asDomDocument ? null : $_string;
    }

    /**
     * Returns an associative array between the headers name and their respective values
     *
     * @param string $_headers
     * @return array
     */
    public static function convertStringHeadersToArray($_headers) {
        $lines   = explode("\r\n", $_headers);
        $headers = array();
        foreach ($lines as $line) {
            if (strpos($line, ':')) {
                $headerParts              = explode(':', $line);
                $headers[$headerParts[0]] = trim(implode(':', array_slice($headerParts, 1)));
            }
        }
        return $headers;
    }

    /**
     * Sets a SoapHeader to send
     * For more information, please read the online documentation on {@link http://www.php.net/manual/en/class.soapheader.php}
     *
     * @uses WsdlClass::getSoapClient()
     * @uses SoapClient::__setSoapheaders()
     * @param string $_nameSpace SoapHeader namespace
     * @param string $_name      SoapHeader name
     * @param mixed  $_data      SoapHeader data
     * @param bool   $_mustUnderstand
     * @param string $_actor
     * @return bool true|false
     */
    public function setSoapHeader($_nameSpace, $_name, $_data, $_mustUnderstand = false, $_actor = null) {
        if (self::getSoapClient()) {
            $defaultHeaders = (isset(self::getSoapClient()->__default_headers) && is_array(self::getSoapClient()->__default_headers)) ? self::getSoapClient()->__default_headers : array();
            foreach ($defaultHeaders as $index => $soapheader) {
                if ($soapheader->name == $_name) {
                    unset($defaultHeaders[$index]);
                    break;
                }
            }
            self::getSoapClient()->__setSoapheaders(null);
            if (!empty($_actor))
                array_push($defaultHeaders, new SoapHeader($_nameSpace, $_name, $_data, $_mustUnderstand, $_actor));
            else
                array_push($defaultHeaders, new SoapHeader($_nameSpace, $_name, $_data, $_mustUnderstand));
            return self::getSoapClient()->__setSoapheaders($defaultHeaders);
        } else
            return false;
    }

    /**
     * Sets the SoapClient Stream context HTTP Header name according to its value
     * If a context already exists, it tries to modify it
     * It the context does not exist, it then creates it with the header name and its value
     *
     * @uses WsdlClass::getSoapClient()
     * @param string $_headerName
     * @param mixed  $_headerValue
     * @return bool true|false
     */
    public function setHttpHeader($_headerName, $_headerValue) {
        if (self::getSoapClient() && !empty($_headerName)) {
            $streamContext = (isset(self::getSoapClient()->_stream_context) && is_resource(self::getSoapClient()->_stream_context)) ? self::getSoapClient()->_stream_context : null;
            if (!is_resource($streamContext)) {
                $options                   = array();
                $options['http']           = array();
                $options['http']['header'] = '';
            } else {
                $options = stream_context_get_options($streamContext);
                if (is_array($options)) {
                    if (!array_key_exists('http', $options) || !is_array($options['http'])) {
                        $options['http']           = array();
                        $options['http']['header'] = '';
                    } elseif (!array_key_exists('header', $options['http']))
                        $options['http']['header'] = '';
                } else {
                    $options                   = array();
                    $options['http']           = array();
                    $options['http']['header'] = '';
                }
            }
            if (count($options) && array_key_exists('http', $options) && is_array($options['http']) && array_key_exists('header', $options['http']) && is_string($options['http']['header'])) {
                $lines = explode("\r\n", $options['http']['header']);
                /**
                 * Ensure there is only one header entry for this header name
                 */
                $newLines = array();
                foreach ($lines as $line) {
                    if (!empty($line) && strpos($line, $_headerName) === false)
                        array_push($newLines, $line);
                }
                /**
                 * Add new header entry
                 */
                array_push($newLines, "$_headerName: $_headerValue");
                /**
                 * Set the context http header option
                 */
                $options['http']['header'] = implode("\r\n", $newLines);
                /**
                 * Create context if it does not exist
                 */
                if (!is_resource($streamContext))
                    return (self::getSoapClient()->_stream_context = stream_context_create($options)) ? true : false;
                /**
                 * Set the new context http header option
                 */
                else
                    return stream_context_set_option(self::getSoapClient()->_stream_context, 'http', 'header', $options['http']['header']);
            } else
                return false;
        } else
            return false;
    }

    /**
     * Method alias to count
     *
     * @uses WsdlClass::count()
     * @return int
     */
    public function length() {
        return $this->count();
    }

    /**
     * Method returning item length, alias to length
     *
     * @uses WsdlClass::getInternArrayToIterate()
     * @uses WsdlClass::getInternArrayToIterateIsArray()
     * @return int
     */
    public function count(): int {
        return $this->getInternArrayToIterateIsArray() ? count($this->getInternArrayToIterate()) : -1;
    }

    /**
     * Method returning the current element
     *
     * @uses WsdlClass::offsetGet()
     * @return mixed
     */
    public function current(): mixed {
        return $this->offsetGet($this->internArrayToIterateOffset);
    }

    /**
     * Method moving the current position to the next element
     *
     * @uses WsdlClass::getInternArrayToIterateOffset()
     * @uses WsdlClass::setInternArrayToIterateOffset()
     * @return int
     */
    public function next(): void {
        $this->setInternArrayToIterateOffset($this->getInternArrayToIterateOffset() + 1);
        return;
    }

    /**
     * Method resetting itemOffset
     *
     * @uses WsdlClass::setInternArrayToIterateOffset()
     * @return int
     */
    public function rewind(): void {
        $this->setInternArrayToIterateOffset(0);
        return;
    }

    /**
     * Method checking if current itemOffset points to an existing item
     *
     * @uses WsdlClass::getInternArrayToIterateOffset()
     * @uses WsdlClass::offsetExists()
     * @return bool true|false
     */
    public function valid(): bool {
        return $this->offsetExists($this->getInternArrayToIterateOffset());
    }

    /**
     * Method returning current itemOffset value, alias to getInternArrayToIterateOffset
     *
     * @uses WsdlClass::getInternArrayToIterateOffset()
     * @return int
     */
    public function key(): mixed {
        return $this->getInternArrayToIterateOffset();
    }

    /**
     * Method alias to offsetGet
     *
     * @see  WsdlClass::offsetGet()
     * @uses WsdlClass::offsetGet()
     * @param int $_index
     * @return mixed
     */
    public function item($_index) {
        return $this->offsetGet($_index);
    }

    /**
     * Default method adding item to array
     *
     * @uses WsdlClass::getAttributeName()
     * @uses WsdlClass::__toString()
     * @uses WsdlClass::_set()
     * @uses WsdlClass::_get()
     * @uses WsdlClass::setInternArrayToIterate()
     * @uses WsdlClass::setInternArrayToIterateIsArray()
     * @uses WsdlClass::setInternArrayToIterateOffset()
     * @param mixed $_item value
     * @return bool true|false
     */
    public function add($_item) {
        if ($this->getAttributeName() != '' && stripos($this->__toString(), 'array') !== false) {
            /**
             * init array
             */
            if (!is_array($this->_get($this->getAttributeName())))
                $this->_set($this->getAttributeName(), array());
            /**
             * current array
             */
            $currentArray = $this->_get($this->getAttributeName());
            array_push($currentArray, $_item);
            $this->_set($this->getAttributeName(), $currentArray);
            $this->setInternArrayToIterate($currentArray);
            $this->setInternArrayToIterateIsArray(true);
            $this->setInternArrayToIterateOffset(0);
            return true;
        }
        return false;
    }

    /**
     * Method to call when sending data to request for *array* type class
     *
     * @uses WsdlClass::getAttributeName()
     * @uses WsdlClass::__toString()
     * @uses WsdlClass::_get()
     * @return mixed
     */
    public function toSend() {
        if ($this->getAttributeName() != '' && stripos($this->__toString(), 'array') !== false)
            return $this->_get($this->getAttributeName());
        else
            return null;
    }

    /**
     * Method returning the first item
     *
     * @uses WsdlClass::item()
     * @return mixed
     */
    public function first() {
        return $this->item(0);
    }

    /**
     * Method returning the last item
     *
     * @uses WsdlClass::item()
     * @uses WsdlClass::length()
     * @return mixed
     */
    public function last() {
        return $this->item($this->length() - 1);
    }

    /**
     * Method testing index in item
     *
     * @uses WsdlClass::getInternArrayToIterateIsArray()
     * @uses WsdlClass::getInternArrayToIterate()
     * @param int $_offset
     * @return bool true|false
     */
    public function offsetExists(mixed $_offset): bool {
        return ($this->getInternArrayToIterateIsArray() && array_key_exists($_offset, $this->getInternArrayToIterate()));
    }

    /**
     * Method returning the item at "index" value
     *
     * @uses WsdlClass::offsetExists()
     * @param int $_offset
     * @return mixed
     */
    public function offsetGet(mixed $_offset): mixed {
        return $this->offsetExists($_offset) ? $this->internArrayToIterate[$_offset] : null;
    }

    /**
     * Method useless but necessarly overridden, can't set
     *
     * @param mixed $_offset
     * @param mixed $_value
     * @return null
     */
    public function offsetSet(mixed $_offset, mixed $_value): void {
        return;
    }

    /**
     * Method useless but necessarly overridden, can't unset
     *
     * @param mixed $_offset
     * @return null
     */
    public function offsetUnset(mixed $_offset): void {
        return;
    }

    /**
     * Method returning current result from Soap call
     *
     * @return mixed
     */
    public function getResult() {
        return $this->result;
    }

    /**
     * Method setting current result from Soap call
     *
     * @param mixed $_result
     * @return mixed
     */
    protected function setResult($_result) {
        return ($this->result = $_result);
    }

    /**
     * Method returning last errors occured during the calls
     *
     * @return array
     */
    public function getLastError() {
        return $this->lastError;
    }

    /**
     * Method setting last errors occured during the calls
     *
     * @param array $_lastError
     * @return array
     */
    private function setLastError($_lastError) {
        return ($this->lastError = $_lastError);
    }

    /**
     * Method saving the last error returned by the SoapClient
     *
     * @param string    $_methoName the method called when the error occurred
     * @param SoapFault $_soapFault l'objet de l'erreur
     * @return SoapFault
     */
    protected function saveLastError($_methoName, SoapFault $_soapFault) {
        return ($this->lastError[$_methoName] = $_soapFault);
    }

    /**
     * Method getting the last error for a certain method
     *
     * @param string $_methoName method name to get error from
     * @return SoapFault|null
     */
    public function getLastErrorForMethod($_methoName) {
        return (is_array($this->lastError) && array_key_exists($_methoName, $this->lastError)) ? $this->lastError[$_methoName] : null;
    }

    /**
     * Method returning intern array to iterate trough
     *
     * @return array
     */
    public function getInternArrayToIterate() {
        return $this->internArrayToIterate;
    }

    /**
     * Method setting intern array to iterate trough
     *
     * @param array $_internArrayToIterate
     * @return array
     */
    public function setInternArrayToIterate($_internArrayToIterate) {
        return ($this->internArrayToIterate = $_internArrayToIterate);
    }

    /**
     * Method returnint intern array index when iterating trough
     *
     * @return int
     */
    public function getInternArrayToIterateOffset() {
        return $this->internArrayToIterateOffset;
    }

    /**
     * Method initiating internArrayToIterate
     *
     * @uses WsdlClass::setInternArrayToIterate()
     * @uses WsdlClass::setInternArrayToIterateOffset()
     * @uses WsdlClass::setInternArrayToIterateIsArray()
     * @uses WsdlClass::getAttributeName()
     * @uses WsdlClass::initInternArrayToIterate()
     * @uses WsdlClass::__toString()
     * @param array $_array      the array to iterate trough
     * @param bool  $_internCall indicates that methods is calling itself
     * @return void
     */
    public function initInternArrayToIterate($_array = array(), $_internCall = false) {
        if (stripos($this->__toString(), 'array') !== false) {
            if (is_array($_array) && count($_array)) {
                $this->setInternArrayToIterate($_array);
                $this->setInternArrayToIterateOffset(0);
                $this->setInternArrayToIterateIsArray(true);
            } elseif (!$_internCall && $this->getAttributeName() != '' && property_exists($this->__toString(), $this->getAttributeName()))
                $this->initInternArrayToIterate($this->_get($this->getAttributeName()), true);
        }
    }

    /**
     * Method setting intern array offset when iterating trough
     *
     * @param int $_internArrayToIterateOffset
     * @return int
     */
    public function setInternArrayToIterateOffset($_internArrayToIterateOffset) {
        return ($this->internArrayToIterateOffset = $_internArrayToIterateOffset);
    }

    /**
     * Method returning true if intern array is an actual array
     *
     * @return bool true|false
     */
    public function getInternArrayToIterateIsArray() {
        return $this->internArrayToIterateIsArray;
    }

    /**
     * Method setting if intern array is an actual array
     *
     * @param bool $_internArrayToIterateIsArray
     * @return bool true|false
     */
    public function setInternArrayToIterateIsArray($_internArrayToIterateIsArray = false) {
        return ($this->internArrayToIterateIsArray = $_internArrayToIterateIsArray);
    }

    /**
     * Generic method setting value
     *
     * @param string $_name  property name to set
     * @param mixed  $_value property value to use
     * @return bool
     */
    public function _set($_name, $_value) {
        $setMethod = 'set' . ucfirst($_name);
        if (method_exists($this, $setMethod)) {
            $this->$setMethod($_value);
            return true;
        } else
            return false;
    }

    /**
     * Generic method getting value
     *
     * @param string $_name property name to get
     * @return mixed
     */
    public function _get($_name) {
        $getMethod = 'get' . ucfirst($_name);
        if (method_exists($this, $getMethod))
            return $this->$getMethod();
        else
            return false;
    }

    /**
     * Method returning alone attribute name when class is *array* type
     *
     * @return string
     */
    public function getAttributeName() {
        return '';
    }

    /**
     * Generic method telling if current value is valid according to the attribute setted with the current value
     *
     * @param mixed $_value the value to test
     * @return bool true|false
     */
    public static function valueIsValid($_value) {
        return true;
    }

    /**
     * Method returning actual class name
     *
     * @return string __CLASS__
     */
    public function __toString(): string {
        return __CLASS__;
    }
}
