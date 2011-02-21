# LittleJoy

Really tiny php framework focused on easy-to-write and easy-to-run tests through [phpunit](http://phpunit.de)

# hands on

Installation-free, just grab the code and copy the folder `Little` to
your project root.

Within your code, let's say `index.php`, all you have to do is require LittleJoy's bootstrap file:

> require_once "Little/Joy.php";

and start hacking!

## Controllers

Simple example, just put the code below in your `index.php` file, in Apache's DocRoot:

    <?php
    require_once Little/Joy.php";

    class Main extends ControllerJoy {
        var $urls = array(
            '^' => 'index',
            '^hello' => 'hello_world'

        );
        public function index($response, $matches, $route) {
            return '<h1>Index</h1>';
        }
        public function hello_world($response, $matches, $route) {
            return '<h1>Hello World</h1>';
        }
    }

    Joy::and_work();
    ?>

## Active Record

It's inspired on
[django's model declaration](http://docs.djangoproject.com/en/dev/topics/db/models/),
so if you already worked with Django, it will not be a problem.

### Declare your models sweetly

    require_once "Little/Joy.php";

    class School extends ModelJoy {
        var $name = array(type => CharField, max_length => 100);
        var $website = array(type => URLField);
    }

    class Person extends ModelJoy {
        var $name = array(type => CharField, max_length => 100);
        var $email = array(type => EmailField);
        var $bio = array(type => TextField);
        var $website = array(type => URLField);
        var $gender = array(type => ChoiceField, nullable => true, choices => array("male", "female"));
        var $birthday = array(type => DateTimeField, nullable => true);

        var $mother = array(type => ForeignKey, related_with => "Person");
        var $father = array(type => ForeignKey, related_with => "Person");
        var $school = array(type => ForeignKey, related_with => "School");
    }


### Create all declared Models as tables in the database

    Joy::syncdb();

### Now you can play with it

    $school = School::populated_with(array("name" => "Harvard School of Engineer and Applied Sciences", "website" => "http://seas.harvard.edu/"));
    $school->save(); //performs a INSERT

    $school->website = http://new-website.url";
    $school->save(); //performs a UPDATE

    $found_by_name = School::find_by_name("Harvard School of Engineer and Applied Sciences");
    $found_by_website = School::find_by_website("http://new-website.url");

    $found_by_website->name === $found_by_name->name === $school->name;

# Contributing

## install dependencies to run tests

The very first thing you must install is PHPUnit, but since it's
documentation is quite a bit out-of-date, here is a small walk-through:

**ATTENTION**: this walkthrough consider that you have [pear](http://pear.php.net) installed on your computer.

1. Add a few channels on your pear
2. Update your official channel
3. Update your pear version
4. Finally install PHPUnit

### OK, here is the big command, just paste it in your terminal

    sudo pear channel-discover pear.phpunit.de
    sudo pear channel-discover pear.symfony-project.com
    sudo pear channel-discover components.ez.no
    sudo pear channel-update pear.php.net
    sudo pear upgrade pear
    sudo pear install --alldeps pear.phpunit.de/PHPUnit

1. fork and clone the project
2. install the dependencies above
3. run the tests with make:
    > make unit functional

4. hack at will
5. commit, push etc
6. send a pull request
