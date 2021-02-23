<?php

class NetPayException extends Exception
{
    private $_netpayError = null;

    public function __construct($message = null, $netpayError = null)
    {
        parent::__construct($message);
        $this->setNetPayError($netpayError);
    }

    /**
     * Returns an instance of an exception class from the given error response.
     *
     * @param  array $array
     *
     * @return NetPayAuthenticationFailureException|NetPayNotFoundException|NetPayUsedTokenException|NetPayInvalidCardException|NetPayInvalidCardTokenException|NetPayMissingCardException|NetPayInvalidChargeException|NetPayFailedCaptureException|NetPayFailedFraudCheckException|NetPayUndefinedException
     */
    public static function getInstance($array)
    {
        switch ($array['code']) {
            case 'authentication_failure':
                return new NetPayAuthenticationFailureException($array['message'], $array);

            case 'bad_request':
                return new NetPayBadRequestException($array['message'], $array);

            case 'not_found':
                return new NetPayNotFoundException($array['message'], $array);

            case 'used_token':
                return new NetPayUsedTokenException($array['message'], $array);

            case 'invalid_card':
                return new NetPayInvalidCardException($array['message'], $array);

            case 'invalid_card_token':
                return new NetPayInvalidCardTokenException($array['message'], $array);

            case 'missing_card':
                return new NetPayMissingCardException($array['message'], $array);

            case 'invalid_charge':
                return new NetPayInvalidChargeException($array['message'], $array);

            case 'failed_capture':
                return new NetPayFailedCaptureException($array['message'], $array);

            case 'failed_fraud_check':
                return new NetPayFailedFraudCheckException($array['message'], $array);

            case 'failed_refund':
                return new NetPayFailedRefundException($array['message'], $array);

            case 'invalid_link':
                return new NetPayInvalidLinkException($array['message'], $array);

            case 'invalid_recipient':
                return new NetPayInvalidRecipientException($array['message'], $array);

            case 'invalid_bank_account':
                return new NetPayInvalidBankAccountException($array['message'], $array);

            default:
                return new NetPayUndefinedException($array['message'], $array);
        }
    }

    /**
     * Sets the error.
     *
     * @param NetPayError $netpayError
     */
    public function setNetPayError($netpayError)
    {
        $this->_netpayError = $netpayError;
    }

    /**
     * Gets the NetPayError object. This method will return null if an error happens outside of the API. (For example, due to HTTP connectivity problem.)
     * Please see https://docs.netpay.co/api/errors/ for a list of possible errors.
     *
     * @return NetPayError
     */
    public function getNetPayError()
    {
        return $this->_netpayError;
    }
}

class NetPayAuthenticationFailureException extends NetPayException { }
class NetPayBadRequestException extends NetPayException { }
class NetPayNotFoundException extends NetPayException { }
class NetPayUsedTokenException extends NetPayException { }
class NetPayInvalidCardException extends NetPayException { }
class NetPayInvalidCardTokenException extends NetPayException { }
class NetPayMissingCardException extends NetPayException { }
class NetPayInvalidChargeException extends NetPayException { }
class NetPayFailedCaptureException extends NetPayException { }
class NetPayFailedFraudCheckException extends NetPayException { }
class NetPayFailedRefundException extends NetPayException { }
class NetPayInvalidLinkException extends NetPayException { }
class NetPayInvalidRecipientException extends NetPayException { }
class NetPayInvalidBankAccountException extends NetPayException { }
class NetPayUndefinedException extends NetPayException { }
