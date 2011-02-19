all: test

test: unit functional

unit: clean
	@echo "Running unit tests ..."
	@find $$PWD/tests/unit -iname 'test*.php' -exec phpunit --colors --include-path `pwd` --bootstrap `pwd`/Little/Joy.php {} \;

functional: clean
	@echo "Running functional tests ..."
	@find $$PWD/tests/functional -iname 'test*.php' -exec phpunit --colors --include-path `pwd` --bootstrap `pwd`/Little/Joy.php {} \;

clean:
	@printf "Cleaning up files that are already in .gitignore... "
	@for pattern in `cat .gitignore`; do rm -rf $$pattern; done
	@echo "OK!"