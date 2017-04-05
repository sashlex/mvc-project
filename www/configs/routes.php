<?php
# routes file, required in App.php

function url( $url = '' )
{
    if ( !$url )
    {
        $url = $_SERVER [ 'REQUEST_URI' ];
    }
    $url = rawurldecode( $url );
    if ( ( bool ) preg_match( '#[^a-zA-Z0-9\/_\-.]+#', $url ) )
    { // Если url валидный ( любой символ кроме этих означает недопустимый URL ) - "отрицание" строгая поверка
        return null;
    }
    return $url;
}
# get url ( return value must be string )
$url = url();
# set default request
$request = new stdClass();
$request->controller = 'Default';
$request->params = array();
$request->method = 'index';
$request->args = array();

// case 4 == sscanf( $url, '/%d/%[^/]/%d/%s', $a, $b, $c, $d ):
// case - (string) or (sscanf rule)
// what about -> request - responce
// what about REST API
// validate url data (args and params) or validated on function url !!!!!!!!!!!!!
// handle request type POST, GET, PUT, DELETE and etc.
switch ( $url ) {
    # Users access
    case '/':
        $request->controller = 'Default';
        $request->method = 'index';
        break;
    case 1 == sscanf( $url, '/css/%s', $file ):
        $request->controller = 'File';
        $request->args = array( 'file' => $file );
        $request->method = 'index';
        break;
    case 1 == sscanf( $url, '/img/%s', $file ):
        $request->controller = 'File';
        $request->args = array( 'file' => $file );
        $request->method = 'index';
        break;

    # Admin access
    case 'admin':
        echo 'admin';
        break;

        #default page
    default:
        $request->controller = 'Default';
        $request->method = 'index';
}

return $request;

# Format examples:
# '/%[ab]/%[c]'   --->   '/ab/c'
# '/%d/%[^/]/%d'   --->   '/1/x/2'
# '/%[x]/%d/%[y]/%d'   --->   '/x/1/y/2/z'
# '/%[css]/%[^.].%s'   --->   '/css/style.html'
# '/css/theme/%[^.]'   --->   '/css/theme/style.html'
# '/%[^/]/%[^/]/%[^.]'   --->   '/css/theme/style.html'
# '/css/theme/%s'   --->   '/css/theme/style.html' => style.html
# '%[^xyz]' - invert
# '%[^]/' - until not found /

//case 1 == sscanf( $url, '/editpoll/%d%s', $id, $not_allowed );
// print_r( count( $data = sscanf( $url, '/editpoll/%d[^%s]' ) ) );
// print_r( ( $data = sscanf( $url, '/editpoll/%d%s' ) ) [1] );
# http://docs.roxen.com/(en)/pike/7.0/tutorial/strings/sscanf.xml
# The return value from sscanf will be 1, a will be given the value "test":
# sscanf(" \t test", "%*[ \t]%s", a) 


# Examples:
#
# only /editpoll/3 and not /editpoll/3/
# case 1 == count( $data = sscanf( $url, '/editpoll/%d%s' ) ):
# result in $data
#
# only /editpoll/1/editquestion/2 and not /editpoll/1/editquestion/2/
# case 2 == count( $data = sscanf( $url, '/editpoll/%d/editquestion/%d%s' ) ):
# result in $data
#

# Regular Expression examples:
# '/^\/store{1}\/\d+\/?$/'   --->   /store/3 or /store/3/
# '/^\/store{1}$/'   --->   /store
