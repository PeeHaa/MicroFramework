<?php
/**
 * Pretty-print any variable (in <pre>), for debugging/documentation purposes
 * Dumps anythings you pass in, prettily formatted
 *
 * void   dump($object, $flags=DUMP_DEFAULT, $name='o');
 * string sdump($object, $flags=DUMP_DEFAULT, $name='o');
 * string sdumpval($value);
 * void   dumpargs(*varargs);
 * string sdumpargs(*varargs);
 * void   dumptrace();
 * string sdumptrace();
 * void   dumpdbx($dbxresults, $overridesettings=NULL); // uses a table for output
 * string sdumpdbx($dbxresults, $overridesettings=NULL);
 * void   dumptable();
 * string sdumptable();
 * void dumptime(&$starttime, $title=NULL);
 * string sdumptime(&$starttime=NULL, $title=NULL);
 *
 * all the sdump functions return the string instead of printing directly
 * to output like the dump versions do
 *
 * Usage example:
 *
 * dump($anything, DUMP_ALL);
 */

define('DUMP_SIMPLE', 0);
define('DUMP_PRIVATE', 1);
define('DUMP_METHODS', 2);
define('DUMP_CHILD_OBJECTS', 4);
define('DUMP_CLASSNAMES', 8);
define('DUMP_LEADING_NAME', 16);

// some often-used combinations of DUMP_flags
define('DUMP_DATA', DUMP_CHILD_OBJECTS | DUMP_LEADING_NAME);
define('DUMP_PUBLIC', DUMP_METHODS | DUMP_CHILD_OBJECTS | DUMP_LEADING_NAME);
define('DUMP_ALL', DUMP_PUBLIC | DUMP_PRIVATE | DUMP_CLASSNAMES | DUMP_LEADING_NAME);
define('DUMP_DEFAULT', DUMP_CHILD_OBJECTS | DUMP_CLASSNAMES | DUMP_LEADING_NAME);

function sdumpphp($o)
{
    ob_start();
    dumpphp($o);
    $s = ob_get_clean();
    return $s;
}

function dumpphp($o)
{
    var_dump($o);
}

function txtdump($o, $flags=DUMP_DEFAULT, $name='o')
{
    return str_replace('&quot;', '"', trim(strip_tags(sdump($o, $flags, $name))));
}

function sdump($o, $flags=DUMP_DEFAULT, $name='o')
{
    ob_start();
    dump($o, $flags, $name);
    $s = ob_get_clean();
    return $s;
}

function dump($o, $flags=DUMP_DEFAULT, $name='o')
{
    print('<div class="fdump">'.N);
    print('<pre style="text-align: left;">'.N);
    fdump($o, $flags, $name);
    print('</pre>'.N);
    print('</div>'.N);
}

function sfdump($o, $flags=DUMP_DEFAULT, $name='o')
{
    ob_start();
    fdump($o, $flags, $name);
    $s = ob_get_clean();
    return $s;
}

function fdump($o, $flags=DUMP_DEFAULT, $name='o')
{
    $objectcache = array();
    _fdump($name, $o, $flags, '<b>%1s</b>', '  ', '', 0, $objectcache);
}

// _fdump is NEVER called by anyone, it is internally called from fdump
// TODO: object-recursion check
function _fdump($name, &$o, $flags, $fname, $indent, $actualindent, $recursion, &$objectcache)
{
    if (!$recursion) {
        if ($flags & DUMP_LEADING_NAME) $indent = sprintf($fname, str_repeat(' ', strlen($name)));
        else $indent = '';
    }
    if ($recursion || ($flags & DUMP_LEADING_NAME)) printf($actualindent.$fname, html_encode($name));
    $first = true;
    if (is_object($o)) {
        $object_id = spl_object_hash($o);
        if (array_key_exists($object_id, $objectcache)) {
            print('; [[[recursion]]]'.N);
        }
        else {
            $objectcache[$object_id] = true;
            if (method_exists($o, '_dump_')) {
                ob_start();
                $o->_dump_();
                $s = ob_get_clean();
                if ($flags & DUMP_CLASSNAMES) {
                    $classname = get_class($o);
                    print(': ('.$classname.')'.N.$indent);
                }
                print('; '); $indent.='  ';
                $lines = explode(N, $s);
                foreach($lines as $index=>$line) if (strlen($line)) {
                    if ($index) print($indent);
                    print($line.N);
                }
            }
            elseif (!$recursion || $flags & DUMP_CHILD_OBJECTS) {

                if ($flags & DUMP_CLASSNAMES) {
                    $classname = get_class($o);
                    print(': ('.$classname.')'.N.$indent);
                }
                print('->'); $indent.='  ';

                if ($flags & DUMP_METHODS) {
                    $list = get_class_methods(get_class($o));
                    if (count($list)) {
                        $indent_size = 0;
                        foreach ($list as $name) {
                            if (substr($name, 0, 1)!=='_' || ($flags & DUMP_PRIVATE)) {
                                if ($indent_size<strlen($name)) $indent_size = strlen($name);
                            }
                        }
                        $format = sprintf('<i>%%-%ds</i>', $indent_size);
                        $next_indent = $indent.str_repeat(' ', $indent_size);
                        foreach ($list as $name) {
                            if (substr($name, 0, 1)!=='_' || ($flags & DUMP_PRIVATE)) {
                                printf(($first?'':$indent).$format.N, $name.'(...)');
                                $first = false;
                            }
                        }
                    }
                }

                $list = get_object_vars($o);
                $printable_vars = 0;
                $indent_size = 0;
                foreach ($list as $key => $value) {
                    if (substr($key, 0, 1)!=='_' || ($flags & DUMP_PRIVATE)) {
                        if ($indent_size<strlen($key)) $indent_size = strlen($key);
                        ++$printable_vars;
                    }
                }
                $format = sprintf('<b>%%-%ds</b>', $indent_size);
                $next_indent = $indent.sprintf(sprintf('<b>%%-%ds</b>', $indent_size), '');
                if (!$printable_vars) print(N);
                foreach(array_keys($list) as $key) {
                    $value =& $o->$key;
                    if (substr($key, 0, 1)!=='_' || ($flags & DUMP_PRIVATE)) {
                        _fdump($key, $value, $flags, $format, $next_indent, ($first?'':$indent), $recursion+1, $objectcache);
                        $first = false;
                    }
                }
            }
            else {
                print('-> [object]'.N);
            }
            unset($objectcache[$object_id]);
        }
    }
    else if (is_array($o)) {
        $indent_size = strlen('[]');
        foreach ($o as $key => $value) {
            if ($indent_size<strlen($key.'[]')) $indent_size = strlen($key.'[]');
        }
        $format = sprintf('%%-%ds', $indent_size);
        $next_indent = $indent.str_repeat(' ', $indent_size);
        if (!count($o)) print('[]'.N);
        foreach ($o as $key => $value) {
            _fdump('['.$key.']', $value, $flags, $format, $next_indent, ($first?'':$indent), $recursion+1, $objectcache);
            $first = false;
        }
    }
    else if (is_resource($o)) {
        print(': '.$o.N);
    }
    else if (is_bool($o)) {
        print('; '.($o? 'True': 'False').N);
    }
    else if (is_null($o)) {
        print('; NULL'.N);
    }
    else if (is_string($o) && !strlen(trim($o))) {
        print('; \''.html_encode($o).'\''.N);
    }
    else if (is_string($o)) {
        print(': '.html_encode($o).N);
    }
    else {
        print(': '.$o.N);
    }
}

function sdumpval($o)
{
    if (is_resource($o)) {
        $s = '('.$o.')';
        return $s;
    }
    else if (is_bool($o)) {
        $s = ($o? 'True': 'False');
        return $s;
    }
    else if (is_null($o)) {
        $s = 'NULL';
        return $s;
    }
    else if (is_string($o) && !strlen(trim($o))) {
        $s = '\''.html_encode($o).'\'';
        return $s;
    }
    else if (is_string($o)) {
        $s = html_encode($o, ENT_COMPAT, 'UTF-8');
        return $s;
    }
    else if (is_array($o)) {
        $comma = '';
        $s = '{';
        foreach($o as $k=>$v) if ($k[0]!='_') { $s.= $comma.$k.':'.sdumpval($v); $comma = ', '; }
        $s.= '}';
        return $s;
    }
    else if (is_object($o)) {
        $comma = '';
        $s = '{{';
        foreach($o as $k=>$v) if ($k[0]!='_') { $s.= $comma.$k.':'.sdumpval($v); $comma = ', '; }
        $s.= '}}';
        return $s;
    }
    else {
        $s = $o;
        return $s;
    }
}

function dumpargs()
{
    $args = func_get_args();
    dump($args);
}

function sdumpargs()
{
    $args = func_get_args();
    return sdump($args);
}


function dumptrace()
{
    print(sdumptrace());
}

function sdumptrace()
{
    $trace = debug_backtrace();
    return sdump($trace);
}

function dumpdbx($dbxresult)
{
    print(sdumpdbx($dbxresult));
}

function sdumpdbx($dbxresult)
{
    $settings->show_fields = array();
    $settings->link_id = True;
    $settings->link_parentid = True;
    $settings->show_all_fields = True;
    $settings->hide_fields = array();

    $s = '';
    // header
    $s.= '<table class="dbx">'.N;
    // sql statement
    if (isset($dbxresult->sql)) {
        $s.= '<tr class="dbx-header">'.N;
        $s.= '<td colspan="'.($dbxresult->cols+1).'"><b>'.$dbxresult->sql.'</b></td>'.N;
        $s.= '</tr>'.N;
    }
    if (isset($dbxresult->cols) && isset($dbxresult->rows) ) {
        $s.= '<tr class="dbx-header">'.N;
        $s.= '<td colspan="'.($dbxresult->cols+1).'">size: '.$dbxresult->rows.' rows, '.$dbxresult->cols.' columns</td>'.N;
        $s.= '</tr>'.N;
    }
    // info
    if (isset($dbxresult->info) && count($dbxresult->info)) {
        $s.= '<tr class="dbx-info">'.N;
        $s.= '<td>#<br/></td>'.N;
        $handled_columns = array();
        for ($col=0; $col<$dbxresult->cols; ++$col) {
            $colname = $dbxresult->info["name"][$col];
            if (!in_array($colname, $handled_columns)) {
                if (($settings->show_all_fields && !in_array($colname, $settings->hide_fields))
                || in_array($colname, $settings->show_fields)) {
                    $s.= '<td><b>'.$colname.'</b><br/>('.$dbxresult->info["type"][$col].')</td>'.N;
                    $handled_columns[] = $colname;
                }
            }
        }
        $s.= '</tr>'.N;
    }
    // data
    if (isset($dbxresult->data) && count($dbxresult->data)) {
        $firstrow = '-firstrow';
        foreach ($dbxresult->data as $rowindex=>$row) {
            $s.= '<tr class="dbx-data'.$firstrow.'">'.N;
            $s.= '<td>'.$rowindex.'</td>'.N;
            $handled_columns = array();
            for ($col=0; $col<$dbxresult->cols; ++$col) {
                $colname = $dbxresult->info["name"][$col];
                if (!in_array($colname, $handled_columns)) {
                    if (($settings->show_all_fields && !in_array($colname, $settings->hide_fields))
                    || in_array($colname, $settings->show_fields)) {
                        $link = array('', '');
                        if ($settings->link_id && $colname=='id') {
                            $link = array('<a href="'.url('', prm('id', $row[$col])).'">', '</a>');
                        }
                        if ($settings->link_parentid && $colname=='parentid') {
                            $link = array('<a href="'.url('', prm('id', $row[$col])).'">', '</a>');
                        }
                        $s.= '<td>'.$link[0].$row[$col].$link[1].'&nbsp;</td>'.N;
                        $handled_columns[] = $colname;
                    }
                }
            }
            $s.= '</tr>'.N;
            $firstrow = '';
        }
    }
    // error?
    if (isset($dbxresult->error)) {
        $s.= '<tr class="dbx-footer">'.N;
        $s.= '<td colspan="'.($dbxresult->cols+1).'">'.$dbxresult->error.'</td>'.N;
        $s.= '</tr>'.N;
    }
    if ($dbxresult && $dbxresult->resulttime) {
        $s.= '<tr class="dbx-footer">'.N;
        $s.= '<td colspan="'.($dbxresult->cols+1).'">'.N;
        $s.= sprintf("<b>%0.3f</b> sec.", $dbxresult->resulttime).N;
        $s.= '</td>'.N;
        $s.= '</tr>'.N;
    }
    $s.= '</table>'.N;
    return $s;
}

function dumptable($table)
{
    print(sdumptable($table));
}

function sdumptable($table)
{
    $s = '';
    $s.= '<table>'.N;
    if (count($table)) {
        $s.= '<tr>'.N;
        $s.= '<th>'.N;
        $s.= '&nbsp;'.N;
        $s.= '</th>'.N;
        foreach($table[0] as $cellkey=>$cell) if (!is_numeric($cellkey)) {
            $s.= '<th>'.N;
            $s.= sdumpval($cellkey).N;
            $s.= '</th>'.N;
        }
        $s.= '</tr>'.N;
    }
    foreach($table as $rowindex=>$row) {
        $s.= '<tr>'.N;
        $s.= '<th>'.N;
        $s.= $rowindex.N;
        $s.= '</th>'.N;
        foreach($row as $cellkey=>$cell) if (!is_numeric($cellkey)) {
            $s.= '<td>'.N;
            $s.= sdumpval($cell).N;
            $s.= '</td>'.N;
        }
        $s.= '</tr>'.N;
    }
    $s.= '</table>'.N;
    $s.= '</table>'.N;
    return $s;
}

function dumprecordset($recordset)
{
    print(sdumprecordset($recordset));
}

function sdumprecordset($recordset)
{
    if (!count($recordset)) {
        return sdump(NULL);
    }
    $s = '';
    $s.= '<table class="recordset fdump">'.N;
    foreach($recordset as $index=>$record) {
        $fields = $record;
        if (is_object($record)) {
            $fields = $record->toArray();
        }

        if (!$index) {
            $s.= '<thead>'.N;
            $s.= '<tr>'.N;
            $s.= '<th>#</th>'.N;
            foreach($fields as $name=>$value) {
                $s.= '<th>'.$name.'</th>'.N;
            }
            $s.= '</tr>'.N;
            $s.= '</thead>'.N;
            $s.= '<tbody>'.N;
        }
        $s.= '<tr>'.N;
        $s.= '<th>'.$index.'</th>'.N;
        foreach($fields as $name=>$value) {
            $s.= '<td>'.sdumpval($value).'</td>'.N;
        }
        $s.= '</tr>'.N;
    }
    $s.= '</tbody>'.N;
    $s.= '</table>'.N;
    return $s;
}

function sdumptime(&$starttime, $title=NULL)
{
    $sdump = '';
    list($usec, $sec) = explode(" ",microtime());
    $newtime = ((float)$usec + (float)$sec);
    if ($starttime && $title) $sdump = sprintf('<div>'.$title.': <b>%0.4f</b> seconds</div>', ($newtime-$starttime));
    $starttime = $newtime;
    return $sdump;
}

if (!function_exists('dumptime')) {{{
function dumptime(&$starttime, $title=NULL)
{
    $newtime = $starttime;
    $s = sdumptime($newtime, $title);
    if ($starttime && $title) {
        print($s);
    }
    $starttime = $newtime;
}
}}}

function dumpfinaltime($title='total time for request')
{
    list($usec, $sec) = explode(" ",microtime());
    $endtime = ((float)$usec + (float)$sec);
    list($usec, $sec) = explode(" ", $GLOBALS['_total_request_time_start']);
    $starttime = ((float)$usec + (float)$sec);
    printf('<div>'.$title.': <b>%0.4f</b> seconds</div>', ($endtime-$starttime));
}

function signaldump($signal, $o, $flags=DUMP_DEFAULT, $name='o')
{
    stn_signal($signal, sdump($o, $flags, $name));
}

function signaltxtdump($signal, $o, $flags=DUMP_DEFAULT, $name='o')
{
    stn_signal($signal, txtdump($o, $flags, $name));
}

/***********************************************************************
// standalone version, if dump.inc is not loaded yet copy/paste this
// function to where you need it

function dumptime(&$starttime, $title=NULL)
{
    list($usec, $sec) = explode(" ",microtime());
    $newtime = ((float)$usec + (float)$sec);
    if ($starttime && $title) printf('<div>'.$title.': <b>%0.4f</b> seconds</div>', ($newtime-$starttime));
    $starttime = $newtime;
}
***********************************************************************/

/**
 * below are required externals, included here so dump.inc can be used
 * as a standalone file
 * - N: defined in helpers/_init_.inc
 * - spl_object_hash: for recursion-checking, needed for php-4 compat
 */

if (!defined('N')) define('N', "\n");

if (!function_exists('spl_object_hash')) {{{
/**
 * string spl_object_hash ( object $obj)
 * see: http://www.php.net/spl_object_hash
 * availability: PHP >= 5.2.0
 */
function spl_object_hash(&$object)
{
    static $id = 0;
    if (!property_exists($object, '_object_id___')) {
        $object->_object_id___ = '_'.$id.'_';
        $id+= 1;
    }
    return $object->_object_id___;
}
}}}

if (!function_exists('html_encode')) {{{
/*
// Encodes HTML safely for UTF-8. Use instead of htmlentities.
*/
function html_encode($var)
{
    return htmlentities($var, ENT_QUOTES, 'UTF-8') ;
}
}}}
