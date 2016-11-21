#!/bin/bash

soft='physicalinv'
version='09.1'
email=david@durieux.family
copyright=''

xgettext *.php */*.php -o locales/glpi.pot -L PHP --add-comments=TRANS --from-code=UTF-8 --force-po -default-domain=$soft \
    --keyword=_n:1,2,4c --keyword=__s:1,2c --keyword=__:1,2c --keyword=_e:1,2c --keyword=_x:1c,2 --keyword=_ex:1c,2 --keyword=_sx:1c,2 --keyword=_nx:1c,2,3


### for using tx :
##tx set --execute --auto-local -r GLPI.glpipot 'locales/<lang>.po' --source-lang en_GB --source-file locales/glpi.pot
## tx push -s
## tx pull -a


