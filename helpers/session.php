<?php
namespace tradersoft\helpers;

/**
 * Session provides.
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Session
{
    public $flashParam = '__flash';

    /**
     * Starts the session.
     */
    public function open()
    {
        if ($this->getIsActive()) {
            return;
        }

        $this->_setSessionCookieParams();

        @session_start();

        if ($this->getIsActive()) {
            $this->updateFlashCounters();
        }
    }

    /**
     * Ends the current session.
     */
    public function close()
    {
        if ($this->getIsActive()) {
            @session_write_close();
        }
    }

    /**
     * @return bool whether the session has started
     */
    public function getIsActive()
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     * Returns the session variable value with the session variable name.
     * @param string $key the session variable name
     * @param mixed $defaultValue.
     * @return mixed.
     */
    public function get($key, $defaultValue = null)
    {
        $this->open();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $defaultValue;
    }

    /**
     * Adds a session variable.
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->open();
        $_SESSION[$key] = $value;
    }

    /**
     * Gets the session ID.
     * @return string
     */
    public function getId()
    {
        return session_id();
    }

    /**
     * Sets the session ID.
     * This is a wrapper for [PHP session_id()](http://php.net/manual/en/function.session-id.php).
     * @param string $value the session ID for the current session
     */
    public function setId($value)
    {
        session_id($value);
    }

    /**
     * Gets the name of the current session.
     * @return string
     */
    public function getName()
    {
        return session_name();
    }

    /**
     * Sets the name for the current session.
     * @param string $value the session name
     */
    public function setName($value)
    {
        session_name($value);
    }

    /**
     * Removes a session variable.
     * @param string $key
     * @return mixed
     */
    public function remove($key)
    {
        $this->open();
        if (isset($_SESSION[$key])) {
            $value = $_SESSION[$key];
            unset($_SESSION[$key]);

            return $value;
        } else {
            return null;
        }
    }

    /**
     * Removes all session variables
     */
    public function removeAll()
    {
        $this->open();
        foreach (array_keys($_SESSION) as $key) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function has($key)
    {
        $this->open();
        return isset($_SESSION[$key]);
    }

    /**
     * Returns a flash message.
     * @param string $key
     * @param mixed $defaultValue
     * @param bool $delete
     * @return mixed
     */
    public function getFlash($key, $defaultValue = null, $delete = false)
    {
        $counters = $this->get($this->flashParam, []);
        if (isset($counters[$key])) {
            $value = $this->get($key, $defaultValue);
            if ($delete) {
                $this->removeFlash($key);
            } elseif ($counters[$key] < 0) {
                $counters[$key] = 1;
                $_SESSION[$this->flashParam] = $counters;
            }

            return $value;
        } else {
            return $defaultValue;
        }
    }

    /**
     * Sets a flash message.
     * @param string $key
     * @param mixed $value flash message
     * @param bool $removeAfterAccess
     */
    public function setFlash($key, $value = true, $removeAfterAccess = true)
    {
        $counters = $this->get($this->flashParam, []);
        $counters[$key] = $removeAfterAccess ? -1 : 0;
        $_SESSION[$key] = $value;
        $_SESSION[$this->flashParam] = $counters;
    }

    /**
     * Removes a flash message.
     * @param string $key
     * @return mixed
     */
    public function removeFlash($key)
    {
        $counters = $this->get($this->flashParam, []);
        $value = isset($_SESSION[$key], $counters[$key]) ? $_SESSION[$key] : null;
        unset($counters[$key], $_SESSION[$key]);
        $_SESSION[$this->flashParam] = $counters;

        return $value;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasFlash($key)
    {
        return $this->getFlash($key) !== null;
    }

    /**
     * Updates the counters for flash messages and removes outdated flash messages.
     * This method should only be called once in [[init()]].
     */
    protected function updateFlashCounters()
    {
        $counters = $this->get($this->flashParam, []);
        if (is_array($counters)) {
            foreach ($counters as $key => $count) {
                if ($count > 0) {
                    unset($counters[$key], $_SESSION[$key]);
                } elseif ($count == 0) {
                    $counters[$key]++;
                }
            }
            $_SESSION[$this->flashParam] = $counters;
        } else {
            unset($_SESSION[$this->flashParam]);
        }
    }

    /**
     * Set session cookie params for secure protocol
     */
    protected function _setSessionCookieParams()
    {
        if (!Request::getIsSecureConnection()) {
            return;
        }

        $cookieParams = session_get_cookie_params();

        if (PHP_VERSION_ID < 70300) {
            session_set_cookie_params(
                $cookieParams['lifetime'],
                $cookieParams['path'] . '; SameSite=None',
                $cookieParams['domain'],
                true,
                $cookieParams['httponly']
            );
        } else {
            $cookieParams['samesite'] = 'None';
            $cookieParams['secure'] = true;

            session_set_cookie_params($cookieParams);
        }
    }
}