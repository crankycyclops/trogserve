prod:
	npm --section=admin run prod
	npm --section=app run prod

dev:
	npm --section=admin run dev
	npm --section=app run dev

clean:
	-rm -f public/js/*.js public/js/*.map public/js/*.txt 2> /dev/null
	-rm -f public/js/admin/*.js public/js/admin/*.map public/js/admin/*.txt 2> /dev/null
	-rm -f public/css/*.css 2> /dev/null
	-rm -f public/css/admin/*.css 2> /dev/null
	-rm -f public/mix-manifest.json 2> /dev/null
