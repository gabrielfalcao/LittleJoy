# LittleJoy

> version 0.1 - unreleased

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
    require_once "Little/Joy.php";

    get("/", function ($response, $matches, $route){
            return '<h1>Index</h1>';
        });

    get("/hello", function ($response, $matches, $route){
            return render_view("hello-world.haml", array("name", $_GET["username"]));
        });

    Joy::and_work();
    ?>

### note on nice urls

You need [mod_rewrite](http://httpd.apache.org/docs/1.3/mod/mod_rewrite.html) working in your apache
and the following rewrite configuration:

    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . /index.php [L]

## Active Record

It's inspired on
[django's model declaration](http://docs.djangoproject.com/en/dev/topics/db/models/),
so if you already worked with Django, it will not be a problem.

### Declare your models sweetly

    require_once "Little/Joy.php";

    Entity("User", function($does, $validate, $table){
            $does->have("username", new CharField(30));
            $does->have("first_name", new CharField(30));
            $does->have("last_name", new CharField(30));
            $does->have("email", new EmailField());
            $does->have("password", new CharField(128));
            $does->have("is_staff", new BooleanField());
            $does->have("is_active", new BooleanField());
            $does->have("is_superuser", new BooleanField());
            $does->have("last_login", new DateTimeField(true));
            $does->have("date_joined", new DateTimeField());

            $does->has_many("groups")->from_entity("Group")->through("auth_user_groups");

            $does->validate_uniqueness_of("username");

            $table->name_is("auth_user");
            $does->extend("UserModelBusinessRules");
        });

    Entity("Group", function($does, $validate, $table){
            $does->have("name", new CharField(80));
            $does->has_many("users")->from_entity("User")->through("auth_user_groups");
            $table->name_is("auth_group");
        });


    // extend your entity model here
    class UserModelBusinessRules {
        public function get_full_name(){
            return $this->first_name . " " . $this->last_name;
        }
        public function hash($password) {
            $sha = sha1($this->username.$password);
            return "sha1:{$sha}";
        }
        public function set_password($new) {
            if (!is_null($this->password) && ($this->password != $this->hash($new))) {
                throw new Exception("Old password does not match");
            }
            $this->password = $this->hash($new);
        }
    }

### Create all declared Models as tables in the database

    Joy::syncdb();

### Now you can play with it

    $john = new User();
    $john->username = "johndoe";
    $john->first_name = "John";
    $john->last_name = "Doe";
    $john->email = "john@doe.com";
    $john->set_password("123456");
    $john->date_joined = time();
    $john->save(); //performs a UPDATE

    $found_by_username = User::find_one_by_username("johndoe");
    $found_by_email = User::find_one_by_email("john@doe.com");

    $found_by_website->name === $found_by_name->name === $user->name;


#### relationships

    $admins = new Group();
    $admins->name "Administrators";
    $john->groups->add($john);
    $john->save();

    $groups1 = $john->groups->all();
    $groups2 = $john->groups->by_name("Administrators");

    $single_group = $john->groups->by_name("Administrators")->first();

    $admins == $groups1[0] == $groups[1] == $single_group;

# Contributing

## install dependencies to run tests

The very first thing you must install is PHPUnit, but since it's
documentation is quite a bit out-of-date, here is a small walk-through:

**ATTENTION**: this walkthrough consider that you have [pear](http://pear.php.net) installed on your computer.

### if you are running Mac OSX

You may choose whether to use its builtin php or install a alternative [homebrew](http://github.com/mxcl/homebrew/) version found [here](brew install https://github.com/ampt/homebrew/raw/php/Library/Formula/php.rb)

to do so:

    brew install https://github.com/ampt/homebrew/raw/php/Library/Formula/php.rb --with-apache --with-mysql


### afterwards

1. Add a few channels on your pear
2. Update your official channel
3. Update your pear version
4. Finally install PHPUnit

### OK, here is the big command, just paste it in your terminal

    pear channel-discover pear.phpunit.de
    pear channel-discover pear.symfony-project.com
    pear channel-discover components.ez.no
    pear channel-update pear.php.net
    pear upgrade pear
    pear install --alldeps pear.phpunit.de/PHPUnit

## next steps

### 1. fork and clone the project
### 2. install the dependencies above
### 3. run the tests with make:

    > make unit functional

### 4. hack at will
### 5. commit, push etc
### 6. send a pull request
