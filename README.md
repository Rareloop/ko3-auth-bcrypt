# Bcrypt for Kohana 3 Auth Module

This is a small modification to the Kohana 3.0 Auth module to enable us of Bcrypt hashing instead of SHA1.

# Usage

Copy all PHP scripts into your `application` folder for Kohana. You will also need to make an amend to the size of your `password` field in your `users` table to be 60 chars.

# License

Copyright &copy; 2012 [Rareloop](http://www.rareloop.com) and is licensed under the terms of the [MIT License](http://www.opensource.org/licenses/mit-license.php).

This mod also makes use of a [Bcrypt helper class](https://gist.github.com/1053158/) by [Marco Arment](http://marco.org), which has been released to the public domain.