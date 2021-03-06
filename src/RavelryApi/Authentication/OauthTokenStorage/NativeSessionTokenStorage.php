<?php

namespace RavelryApi\Authentication\OauthTokenStorage;

/**
 * A wrapper for persisting tokens directly to the PHP $_SESSION superglobal.
 *
 * This uses four keys (`atoken`, `asecret`, `rtoken`, `rsecret`) which are
 * stored, by default, under the prefix `_ravelryapi/` in `$_SESSION`. So, if
 * you're inspecting it you would notice (not all keys may be present)...
 *
 *     $_SESSION['_ravelryapi/atoken']; #= '...snip...'
 *     $_SESSION['_ravelryapi/asecret']; #= '...snip...'
 *     $_SESSION['_ravelryapi/rtoken']; #= '...snip...'
 *     $_SESSION['_ravelryapi/rsecret']; #= '...snip...'
 **/
class NativeSessionTokenStorage implements TokenStorageInterface
{
    protected $prefix;

    public function __construct($prefix = '_ravelryapi/')
    {
        $this->session = $session;
        $this->prefix = $prefix;
    }

    public function getAccessToken()
    {
        return isset($_SESSION[$this->prefix . 'atoken']) ? $_SESSION[$this->prefix . 'atoken'] : null;
    }

    public function getAccessTokenSecret()
    {
        return isset($_SESSION[$this->prefix . 'asecret']) ? $_SESSION[$this->prefix . 'asecret'] : null;
    }

    public function getRequestToken()
    {
        return isset($_SESSION[$this->prefix . 'rtoken']) ? $_SESSION[$this->prefix . 'rtoken'] : null;
    }

    public function getRequestTokenSecret()
    {
        return isset($_SESSION[$this->prefix . 'rsecret']) ? $_SESSION[$this->prefix . 'rsecret'] : null;
    }

    public function setAccessToken($token)
    {
        if (null === $token) {
            unset($_SESSION[$this->prefix . 'atoken']);
        } else {
            $_SESSION[$this->prefix . 'atoken'] = $token;
        }
    }

    public function setAccessTokenSecret($secret)
    {
        if (null === $secret) {
            unset($_SESSION[$this->prefix . 'asecret']);
        } else {
            $_SESSION[$this->prefix . 'asecret'] = $secret;
        }
    }

    public function setRequestToken($token)
    {
        if (null === $token) {
            unset($_SESSION[$this->prefix . 'rtoken']);
        } else {
            $_SESSION[$this->prefix . 'rtoken'] = $token;
        }
    }

    public function setRequestTokenSecret($secret)
    {
        if (null === $secret) {
            unset($_SESSION[$this->prefix . 'rsecret']);
        } else {
            $_SESSION[$this->prefix . 'rsecret'] = $secret;
        }
    }
}
