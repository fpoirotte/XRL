# -*- coding: utf-8 -*-

import os
import shutil
import logging
from datetime import datetime
from subprocess import call, Popen, PIPE

log = logging.getLogger(__name__)

try:
    import simplejson as json
except ImportError:
    import json

def fake_ignore(cwd, contents):
    for entry in contents:
        log.info('Copying %s/%s to its final destination...', cwd, entry)
    return []

def prepare(globs, locs):
    git = Popen('which git 2> %s' % os.devnull, shell=True, stdout=PIPE
                ).stdout.read().strip()
    doxygen = Popen('which doxygen 2> %s' % os.devnull, shell=True, stdout=PIPE
                ).stdout.read().strip()
    cwd = os.getcwd()
    root = os.path.abspath(os.path.join(cwd, '..', '..'))
    print "Running from %s..." % (root, )
    os.chdir(root)

    buildenv = os.path.join(root, 'vendor', 'erebot', 'buildenv')
    generic_doc = os.path.join(root, 'docs', 'src', 'generic')

    origin = Popen([git, 'config', '--local', 'remote.origin.url'],
                   stdout=PIPE).stdout.read().strip()
    project = origin.rpartition('/')[2]
    if project.endswith('.git'):
        project = project[:-4]
    locs['project'] = project

    git_tag = Popen(['git', 'describe', '--tags', '--exact', '--first-parent'],
                    stdout=PIPE).communicate()[0].strip()
    if git_tag:
        locs['version'] = locs['release'] = git_tag
    else:
        locs['version'] = locs['release'] = 'latest'

    for repository, path in (
        ('git://github.com/Erebot/Erebot_Buildenv.git', buildenv),
        ('git://github.com/Erebot/Erebot_Module_Skeleton_Doc.git', generic_doc)
    ):
        if not os.path.isdir(path):
            os.makedirs(path)
            print "Cloning %s into %s..." % (repository, path)
            call([git, 'clone', repository, path])
        else:
            os.chdir(path)
            print "Updating clone of %s in %s..." % (repository, path)
            call([git, 'checkout', 'master'])
            call([git, 'pull'])
            os.chdir(root)

    composer = json.load(open(os.path.join(root, 'composer.json'), 'r'))

    # Run doxygen
    call([doxygen, os.path.join(root, 'Doxyfile')], env={
        'COMPONENT_NAME': locs['project'],
        'COMPONENT_VERSION': locs['version'],
        'COMPONENT_BRIEF': composer.get('description', ''),
    })

    # Copy doxygen output to Sphinx's output folder
    try:
        shutil.copytree(
            os.path.join(root, 'docs', 'api', 'html'),
            os.path.join(root, 'docs', 'enduser', 'html', 'api'),
            ignore=fake_ignore,
        )
    except OSError:
        pass

    os.chdir(cwd)
    real_conf = os.path.join(buildenv, 'sphinx', 'conf.py')
    print "Including real configuration file (%s)..." % (real_conf, )
    execfile(real_conf, globs, locs)

    locs['copyright'] = u'2012-%d, XRL Team. All rights reserved' % \
            datetime.now().year


prepare(globals(), locals())
