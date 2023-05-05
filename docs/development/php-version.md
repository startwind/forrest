## Why we write PHP 8.2 code

Yes, you are allowed to use the newest PHP versions and functionalities. For as it is important to write the best code and use all the syntactic sugar out there.

## Why we support PHP 7.4

Yes, PHP 7.4 is not an officially supported version anymore. But we know that approx. 50 % of the web servers out there still run on that version. That means we are in a dilemma. 

Of course we are not. Thanks to [Rector](https://github.com/rectorphp/rector). Before we build our `PHAR` file we use this wonderful tool to "downgrade" our source code. After that it is compatible with PHP 7.4. So for the moment we have the best of both worlds. Our dev team can write PHP 8 code and all servers out there can run it. 

We will reconsider this decision from time to time and have a look at the server stats. But as long as you can read this markdown file we are compatible with PHP 7.4.
