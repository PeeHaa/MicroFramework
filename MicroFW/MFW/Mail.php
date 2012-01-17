<?php
/**
 * Send mails
 *
 * PHP version 5.3
 *
 * @category   MicroFramework
 * @package    Mail
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 Pieter Hordijk
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    1.0.0
 */

/**
 * Sends mails
 *
 * @todo       Implement the option to add attachments (including MIME types)
 * @todo       headers -> MIME class?
 * @todo       Implement mail transport methods (SMTP, sendmail etc)
 * @todo       Create unit test
 * @todo       Check rumours about Windows Mail server not stripping BCC addresses (which would be bad m'kay)
 * @todo       Validate debug info
 * @category   MicroFramework
 * @package    Mail
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_Mail
{
    /**
     * @var MFW_Mail_Address The emailaddress of the sender
     */
    protected $emailFrom = null;

    /**
     * @var MFW_Mail_Address The reply to emailaddress
     */
    protected $replyTo = null;

    /**
     * @var MFW_Mail_Address The return-path address
     */
    protected $returnPath = null;

    /**
     * @var array The emailaddress(es) of the recipient(s)
     */
    protected $emailTo = array();

    /**
     * @var array The emailaddress(es) of the carbon copy
     */
    protected $emailCc = array();

    /**
     * @var array The emailaddress(es) of the blind carbon copy
     */
    protected $emailBcc = array();

    /**
     * @var string The subject of the email
     */
    protected $subject = null;

    /**
     * @var string The plaintext content of the email
     */
    protected $bodyText = null;

    /**
     * @var string The HTML content of the email
     */
    protected $bodyHtml = null;

    /**
     * @var string The boundary used in the content and the headers
     */
    protected $boundary;

    /**
     * @var string The character set used
     */
    protected $charSet = 'iso-8859-1';

    /**
     * @var string The headers of the mail
     */
    protected $headers;

    /**
     * Create instance
     */
    public function __construct()
    {
        $this->setBoundary($this->generateBoundary);
    }

    /**
     * Generate a random hash used as boundary
     *
     * @return string The generated hash
     */
    protected function generateBoundary()
    {
        return md5(date('r', time()));
    }

    /**
     * Set the random hash used as boundary
     *
     * @param string $hash The hash
     */
    protected function setBoundary($hash)
    {
        $this->boundary = $hash;
    }

    /**
     * Get the random hash used as boundary
     *
     * @return string The hash
     */
    protected getBoundary()
    {
        return $this->boundary;
    }

    /**
     * Set the sender emailaddress and name
     *
     * @param string $address The emailaddress of the sender
     * @param string $name The name of the sender if any
     */
    public function setSender($address, $name = null)
    {
        $this->emailFrom = new MFW_Mail_Address($address, $name);
    }

    /**
     * Get the sender address
     *
     * @return MFW_Mail_Address The emailaddress of the sender
     */
    protected function getSender($address, $name = null)
    {
        return $this->emailFrom;
    }

    /**
     * Set the reply-to address
     *
     * @param string $address The reply-to address
     * @param string $name The name of the reply-to address if any
     */
    public function setReplyTo($address, $name = null)
    {
        $this->replyTo = new MFW_Mail_Address($address, $name);
    }

    /**
     * Get the reply-to address
     *
     * @return null|MFW_Mail_Address Null if the reply-to address hasn't been set
     */
    protected function getReplyTo()
    {
        return $this->replyTo;
    }

    /**
     * Set the return path (which is used for NDR mails)
     *
     * @param string $address The return path address
     * @param string $name The name of the return path address if any
     */
    public function setReturnPath($address, $name = null)
    {
        $this->returnPath = new MFW_Mail_Address($address, $name);
    }

    /**
     * Get the return path (which is used for NDR mails)
     *
     * @return null|MFW_Mail_Address Null if the return path hasn't been set
     */
    protected function getReturnPath()
    {
        return $this->returnPath;
    }

    /**
     * Add a recipient
     *
     * @param string $address The emailaddress of the recipient
     * @param string $name The name of the recipient if any
     */
    public function addRecipient($address, $name = null)
    {
        $this->emailTo[] = new MFW_Mail_Address($address, $name);
    }

    /**
     * Get all the recipients
     *
     * @return array The recipients (To: addresses)
     */
    protected function getRecipients()
    {
        return $this->emailTo;
    }

    /**
     * Add a CC emailaddress
     *
     * @param string $address The emailaddress of the recipient
     * @param string $name The name of the recipient if any
     */
    public function addCc($address, $name = null)
    {
        $this->emailCc[] = new MFW_Mail_Address($address, $name);
    }

    /**
     * Get all the CC addresses
     *
     * @return array The CC addresses
     */
    protected function getCcs()
    {
        return $this->emailCc;
    }

    /**
     * Add a BCC emailaddress
     *
     * @param string $address The emailaddress of the recipient
     * @param string $name The name of the recipient if any
     */
    public function addBcc($address, $name = null)
    {
        $this->emailBcc[] = new MFW_Mail_Address($address, $name);
    }

    /**
     * Get all the BCC addresses
     *
     * @return array The BCC addresses
     */
    protected function getBccs()
    {
        return $this->emailBcc;
    }

    /**
     * Set the character set used in the email
     *
     * @todo check what characters sets are supported
     * @todo validate character set
     *
     * @param string The character set
     */
    public function setCharSet($charSet)
    {
        $this->charSet = $charSet;
    }

    /**
     * Get the character set used in the email
     *
     * @return string The character set
     */
    protected function getCharSet()
    {
        return $this->charSet;
    }

    /**
     * Set the email subject
     *
     * @todo Validate against RFC 2047
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Get the email subject
     *
     * @return string The subject of the mail
     */
    public function setSubject($subject)
    {
        return $this->subject;
    }

    /**
     * Add the plain text version of the body of the email
     *
     * @param string $text The text of the body
     */
    public function setBodyText($text)
    {
        $this->bodyText = $text;
    }

    /**
     * Get the plain text version of the body of the email
     *
     * @return string The text of the body
     */
    public function getBodyText($text)
    {
        return $this->bodyText;
    }

    /**
     * Add the HTML version of the body of the email
     *
     * @param string $html The HTML of the body
     */
    public function setBodyHtml($html)
    {
        $this->bodyHtml = $html;
    }

    /**
     * Get the HTML version of the body of the email
     *
     * @return string The HTML of the body
     */
    public function getBodyHtml($html)
    {
        return $this->bodyHtml;
    }

    /**
     * Set the headers
     *
     * @param string $headers The headers of the mail
     */
    protected function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * Get the headers
     *
     * @return string The headers of the mail
     */
    protected function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Check whether sending of mail is enabled at all
     * Sending of mail can be disabled when used in a project by using: define('MFW_MAIL_USE', false);
     *
     * @return boolean
     */
    protected function isMailEnabled()
    {
        if (defined('MFW_MAIL_USE') && MFW_MAIL_USE === false) {
            return false;
        }

        return true;
    }

    /**
     * Check whether the mail should be send to the debug address
     * Enabling debug mode can be done in a project by using: define('MFW_RUN_MODE', MFW_DEBUG_MODE);
     *
     * @return boolean
     */
    protected function isDebugModeEnabled()
    {
        if (defined('MFW_RUN_MODE') && MFW_RUN_MODE === MFW_DEBUG_MODE) {
            return true;
        }

        return false;
    }

    /**
     * Check all required parameters and prepare the message to be send
     */
    protected function prepareMessage()
    {
        $this->checkRequiredParams();

        $this->addDebugMessage();

        if ($this->isDebugModeEnabled()) {
            $this->buildDebugHeaders();
        } else {
            $this->buildHeaders();
        }

        $this->buildMessage();
    }

    /**
     * Check whether all the required parameters are given.
     * To be able to send a mail at least these have to been set:
     * emailFrom, at least one emailTo, a subject either a text body or an HTML body or both
     *
     * @throws BadMethodCallException if a requirement isn't met
     */
    protected function checkRequiredParams()
    {
        if ($this->getSender() === null) {
            throw new BadMethodCallException('Missing `from` address.');
        }

        if (empty($this->getRecipients())) {
            throw new BadMethodCallException('At least one recipient is required.');
        }

        if ($this->getSubject()) === null) {
            throw new BadMethodCallException('Missing mail `subject`.');
        }

        if ($this->getBodyText() === null && $this->getBodyHtml() === null) {
            throw new BadMethodCallException('Missing mail body.');
        }
    }

    /**
     * Add the debug header to the mail body if debug mode is enabled
     */
    protected function addDebugMessage()
    {
        if (!$this->isDebugModeEnabled()) {
            return;
        }

        $addressees = '';
        $separator = '';

        foreach($this->getRecipients() as $recipient) {
            $addressees .= $separator . $recipient->getRfcString();
            $separator = ', ';
        }

        foreach($this->getCcs() as $cc) {
            $addressees .= $separator . $cc->getRfcString() . ' (CC)';
        }

        foreach($this->getBccs() as $bcc) {
            $addressees .= $separator . $bcc->getRfcString() . ' (BCC)';
        }

        if ($this->getBodyText() !== null) {
            $this->setBodyText('DEBUG MODE ENABLED: original addressee(s) ' . $addressees . "\r\n" . $this->getBodyText());
        }

        if ($this->getBodyHtml() !== null) {
            $this->setBodyHtml('<p>DEBUG MODE ENABLED: original addressee(s) ' . $addressees . "</p>\r\n" . $this->getBodyHtml());
        }
    }

    /**
     * Build the debug headers using all the options specified
     *
     * @todo Implement X-Mailer setters and getters?
     * @todo Add more header options (returnpath?, date etc)
     * @todo Check for rfc line limit (70 chars?)
     */
    protected function buildDebugHeaders()
    {
        $headers = '';

        $sender = $this->getSender();
        $replyTo = $this->getReplyTo();
        $ccs = $this->getCcs();
        $bccs = $this->getBccs();
        $debugAddress = new MFW_Mail_Address(MFW_DEBUG_ADDRESS);

        $headers.= 'From: ' . $debugAddress->getRfcString() . "\r\n";

        if ($replyTo !== null) {
            $headers.= 'Reply-To: ' . $replyTo->getRfcString() . "\r\n";
        } else {
            $headers.= 'Reply-To: ' . $debugAddress->getRfcString() . "\r\n";
        }

        if (!empty($ccs)) {
            $ccHeader = 'Cc: ';
            $separator = '';
            foreach($ccs as $cc) {
                $ccHeader.= $separator . $debugAddress->getRfcString();
                $separator = ', ';
            }
            $headers.= $ccHeader . "\r\n"
        }

        if (!empty($bccs)) {
            $bccHeader = 'Bcc: ';
            $separator = '';
            foreach($bccs as $bcc) {
                $bccHeader.= $separator . $debugAddress->getRfcString();
                $separator = ', ';
            }
            $headers.= $bccHeader . "\r\n"
        }

        $headers.= 'X-Mailer: MicroFramework Mailer' . "\r\n";
        $headers.= 'Content-Type: multipart/alternative; boundary="PHP-alt-' . $this->getBoundary() . '"';

        $this->setHeaders($headers);
    }

    /**
     * Build the headers using all the options specified
     *
     * @todo Implement X-Mailer setters and getters?
     * @todo Add more header options (returnpath?, date etc)
     * @todo Check for rfc line limit (70 chars?)
     */
    protected function buildHeaders()
    {
        $headers = '';

        $sender = $this->getSender();
        $replyTo = $this->getReplyTo();
        $ccs = $this->getCcs();
        $bccs = $this->getBccs();

        $headers.= 'From: ' . $sender->getRfcString() . "\r\n";

        if ($replyTo !== null) {
            $headers.= 'Reply-To: ' . $replyTo->getRfcString() . "\r\n";
        } else {
            $headers.= 'Reply-To: ' . $sender->getRfcString() . "\r\n";
        }

        if (!empty($ccs)) {
            $ccHeader = 'Cc: ';
            $separator = '';
            foreach($ccs as $cc) {
                $ccHeader.= $separator . $cc->getRfcString();
                $separator = ', ';
            }
            $headers.= $ccHeader . "\r\n"
        }

        if (!empty($bccs)) {
            $bccHeader = 'Bcc: ';
            $separator = '';
            foreach($bccs as $bcc) {
                $bccHeader.= $separator . $bcc->getRfcString();
                $separator = ', ';
            }
            $headers.= $bccHeader . "\r\n"
        }

        $headers.= 'X-Mailer: MicroFramework Mailer' . "\r\n";
        $headers.= 'Content-Type: multipart/alternative; boundary="PHP-alt-' . $this->getBoundary() . '"';

        $this->setHeaders($headers);
    }

    /**
     * Build the mail body (the actual message to be send)
     */
    protected function buildMessage()
    {
        $message = '';

        if ($this->getBodyText() !== null) {
            $message.= '--PHP-alt-' . $this->getBoundary() . "\r\n";
            $message.= 'Content-Type: text/plain; charset="' . $this->getCharSet() . '"' . "\r\n";
            $message.= 'Content-Transfer-Encoding: 7bit' . "\r\n\r\n";
            $message.= $this->getBodyText ."\r\n\r\n";
        }

        if ($this->getBodyHtml() !== null) {
            $message.= '--PHP-alt-' . $this->getBoundary() . "\r\n";
            $message.= 'Content-Type: text/html; charset="' . $this->getCharSet() . '"' . "\r\n";
            $message.= 'Content-Transfer-Encoding: 7bit' . "\r\n\r\n";
            $message.= $this->getBodyHtml() . "\r\n\r\n";
        }

        $message.= '--PHP-alt-' . $this->getBoundary() . '--' . "\r\n";

        $this->setMessage($message);
    }

    /**
     * Send the mail (if enabled)
     *
     * @throws BadFunctionCallException If sending of the mail failed
     */
    public function send()
    {
        $this->prepareMessage();

        if (!$this->isMailEnabled()) return;

        if ($this->isDebugModeEnabled()) {
            $debugAddress = new MFW_Mail_Address(MFW_DEBUG_ADDRESS);
            $mailSent = @mail($debugAddress->getRfcString(), $this->getSubject(), $this->getMessage(), $this->getHeaders());
        } else {
            $to = '';
            $separator = '';
            foreach($this->getRecipients() as $recipient) {
                $to.= $separator . $recipient->getRfcString();
                $separator = ', ';
            }

            $mailSent = @mail($to, $this->getSubject(), $this->getMessage(), $this->getHeaders());
        }

        if (!$mailSent) {
            throw new BadFunctionCallException('Sending mail failed.');
        }
    }
}