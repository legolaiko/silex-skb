How to run tests
================

1.  These functional tests use 'symfony/browser-kit', 'symfony/css-selector', 'phpunit/phpunit'. 
Ensure this libs are installed
2.  Composer 'autoload.php' must be included by test runner itself (e.g. if you're using PhpStorm as test runner, 
go to 'Project settings' -> 'PHP' -> 'PHPUnit' -> 'Use custom loader' and specify correct path to 'autoload.php')
3.  Test environment requires correctly configured database:
    -  Create test database.
    -  Apply initial script (../sql/schema.sql)
    -  Copy phpunit.xml to your config dir, set up correct db options, 
    and make sure this config included by test runner