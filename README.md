# LittleJoy

Really tiny php framework focused on easy-to-write and easy-to-run tests through [phpunit](http://phpunit.de)

## installing

The very first thing you must install is PHPUnit, but since it's
documentation is quite a bit out-of-date, here is a small walk-through:

**ATTENTION**: this walkthrough consider that you have [pear](http://pear.php.net) installed on your computer.


1. Add a few channels on your pear

    sudo pear channel-discover pear.phpunit.de
    sudo pear channel-discover pear.symfony-project.com
    sudo pear channel-discover components.ez.no

2. Update your official channel

    sudo pear channel-update pear.php.net

3. Update your pear version

    sudo pear upgrade pear

4. Finally install PHPUnit

    sudo pear install --alldeps pear.phpunit.de/PHPUnit

## hands on

Within your code, require LittleJoy's bootstrap file:

    require_once "Little/Joy.php";

### Active Record

It's inspired on
[django's model declaration](http://docs.djangoproject.com/en/dev/topics/db/models/),
so if you already worked with Django, it will not be a problem.

#### Declare your models sweetly

    require_once "Little/Joy.php";

    class School extends ModelJoy {
        var $name = array(type => CharField, max_length => 100);
        var $address = array(type => TextField);
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


#### Create all declared Models as tables in the database

    Joy::syncdb();

## running tests

    cd LittleJoy
    make test

