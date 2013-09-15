<?php
// © copyright XRL Team, 2012. All rights reserved.
/*
    This file is part of XRL.

    XRL is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    XRL is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with XRL.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once(
    'PHPUnit' .
    DIRECTORY_SEPARATOR . 'Framework' .
    DIRECTORY_SEPARATOR . 'TestCase.php'
);
require_once(
    dirname(dirname(__FILE__)) .
    DIRECTORY_SEPARATOR . 'src' .
    DIRECTORY_SEPARATOR . 'XRL' .
    DIRECTORY_SEPARATOR . 'Autoload.php'
);
spl_autoload_register(array("XRL_Autoload", "load"));

