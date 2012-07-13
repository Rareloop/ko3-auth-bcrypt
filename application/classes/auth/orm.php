<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Custom Auth module that uses Bcrypt for hashing
 *
 * @author Joe Lambert (Rareloop Ltd)
 */
class Auth_ORM extends Kohana_Auth_ORM
{
	/**
	 * CUSTOM FUNCTION TO ENABLE BCRYPT AUTH
	 * Attempt to log in a user by using an ORM object and plain-text password.
	 *
	 * @param   string   username to log in
	 * @param   string   password to check against
	 * @param   boolean  enable autologin
	 * @return  boolean
	 */
	public function login($username, $password, $remember = FALSE)
	{
		if (empty($password))
			return FALSE;

		return $this->_login($username, $password, $remember);
	}
	
	/**
	 * Creates a hashed password from a plaintext password, inserting salt
	 * based on the configured salt pattern.
	 *
	 * @param   string  plaintext password
	 * @return  string  hashed password string
	 */
	public function hash_password($password, $salt = FALSE)
	{
		return Bcrypt::hash($password);
	}
	
	/**
	 * CUSTOM FUNCTION TO ENABLE BCRYPT AUTH
	 * Logs a user in.
	 *
	 * @param   string   username
	 * @param   string   password
	 * @param   boolean  enable autologin
	 * @return  boolean
	 */
	protected function _login($user, $password, $remember)
	{
		if ( ! is_object($user))
		{
			$username = $user;

			// Load the user
			$user = ORM::factory('user');
			$user->where($user->unique_key($username), '=', $username)->find();
		}

		// If the passwords match, perform a login
		if ($user->has('roles', ORM::factory('role', array('name' => 'login'))) AND Bcrypt::check($password, $user->password))
		{
			if ($remember === TRUE)
			{
				// Create a new autologin token
				$token = ORM::factory('user_token');

				// Set token data
				$token->user_id = $user->id;
				$token->expires = time() + $this->_config['lifetime'];
				$token->save();

				// Set the autologin cookie
				Cookie::set('authautologin', $token->token, $this->_config['lifetime']);
			}

			// Finish the login
			$this->complete_login($user);

			return TRUE;
		}

		// Login failed
		return FALSE;
	}
	
	/**
	 * Compare password with original (hashed). Works for current (logged in) user
	 *
	 * @param   string  $password
	 * @return  boolean
	 */
	public function check_password($password)
	{
		$user = $this->get_user();

		if ($user === FALSE)
		{
			// nothing to compare
			return FALSE;
		}

		return Bcrypt::check_password($password, $user->password);
	}
}