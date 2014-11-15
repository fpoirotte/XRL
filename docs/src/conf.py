# -*- coding: utf-8 -*-

import os
import shutil
from datetime import datetime
from subprocess import call, Popen, PIPE

try:
    import simplejson as json
except ImportError:
    import json

def prepare(globs, locs):
    git = Popen('which git 2> %s' % os.devnull, shell=True, stdout=PIPE
                ).stdout.read().strip()
    doxygen = Popen('which doxygen 2> %s' % os.devnull, shell=True, stdout=PIPE
                ).stdout.read().strip()
    cwd = os.getcwd()
    root = os.path.abspath(os.path.join(cwd, '..', '..'))
    os.chdir(root)
    print "Running from %s..." % (root, )

    # Figure several configuration values from git.
    origin = Popen([git, 'config', '--local', 'remote.origin.url'],
                   stdout=PIPE).stdout.read().strip()
    git_tag = Popen(['git', 'describe', '--tags', '--exact', '--first-parent'],
                    stdout=PIPE).communicate()[0].strip()
    project = origin.rpartition('/')[2]
    if project.endswith('.git'):
        project = project[:-4]
    locs['project'] = project
    if git_tag:
        locs['version'] = locs['release'] = git_tag
    else:
        locs['version'] = locs['release'] = 'latest'

    # Clone or update dependencies
    buildenv = os.path.join(root, 'vendor', 'erebot', 'buildenv')
    natives = os.path.join(root, 'vendor', 'fpoirotte', 'natives4doxygen')
    for repository, path in (
        ('git://github.com/Erebot/Erebot_Buildenv.git', buildenv),
        ('git://github.com/fpoirotte/PHPNatives4Doxygen', natives),
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

    # Remove extra files/folders.
    try:
        shutil.rmtree(os.path.join(root, 'build'))
    except OSError:
        pass
    shutil.move(
        os.path.join(root, 'docs', 'api', 'html'),
        os.path.join(root, 'build', 'apidoc'),
    )

    # Load the real Sphinx confiruation file
    os.chdir(cwd)
    real_conf = os.path.join(buildenv, 'sphinx', 'conf.py')
    print "Including real configuration file (%s)..." % (real_conf, )
    execfile(real_conf, globs, locs)

    locs['copyright'] = u'2012-%d, XRL Team. All rights reserved' % \
            datetime.now().year

    # Copy doxygen output to Sphinx's output folder.
    if 'html_extra_path' not in locs:
        locs['html_extra_path'] = []
    locs['html_extra_path'].append(os.path.join(root, 'build'))
    locs['html_theme'] = 'haiku'


prepare(globals(), locals())
