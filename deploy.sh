#!/bin/sh
set -e

#vendor/bin/phpunit

(git push) || true

git checkout production
git merge master -m "deploy"

git push origin production

git checkout master
