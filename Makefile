all: test

SED=`/usr/bin/which gsed || /usr/bin/which sed` -i
test: unit functional
ROOT=$(pwd)

prepare_and_run_of:
	@cp tests/.configuration.skeleton.xml tests/$(kind)/configuration.xml
	@$(SED) "s,__kind__,$(kind),g" tests/$(kind)/configuration.xml
	@$(SED) "s,__root__,$(ROOT),g" tests/$(kind)/configuration.xml
	@$(SED) "s,__files__,$(files),g" tests/$(kind)/configuration.xml
	@phpunit --colors --configuration tests/$(kind)/configuration.xml

unit: clean
	@echo "Running unit tests ..."
	@make prepare_and_run_of kind=unit files=`find tests/unit -iname 'test*.php' -exec printf '<file>'$$PWD/{}'</file>' \;`;

functional: clean
	@echo "Running functional tests ..."
	@make prepare_and_run_of kind=functional files=`find tests/functional -iname 'test*.php' -exec printf '<file>'$$PWD/{}'</file>' \;`;

clean:
	@printf "Cleaning up files that are already in .gitignore... "
	@for pattern in `cat .gitignore`; do rm -rf $$pattern; done
	@echo "OK!"