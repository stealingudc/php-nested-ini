# php-nested-ini
PHP class that allows reading and writing infinitely (?) nested arrays in .ini files using dot notation.

## Methods

#### `parse_string($str, $process_sections, $scanner_mode)`
Reads the string specified in `str`, and returns the settings in it in an associative array.
### Parameters
#### $str: string
&nbsp;&nbsp;&nbsp;&nbsp;The .ini string being parsed.
#### $process_sections: bool
&nbsp;&nbsp;&nbsp;&nbsp;By setting the process_sections parameter to true, you get a multidimensional array, with the section names and settings included. The default for `process_sections` is false.
#### $scanner_mode: int
&nbsp;&nbsp;&nbsp;&nbsp;Can either be INI_SCANNER_NORMAL (default) or INI_SCANNER_RAW. If INI_SCANNER_RAW is supplied, then option values will not be parsed. See the [php docs](https://www.php.net/parse_ini_file) for more info.
### Return Values
The settings are returned as an associative array on success. Empty array on failure.

##
#### `parse_file($filename, $process_sections, $scanner_mode)`
Loads in the ini file specified in `filename`, and returns the settings in it in an associative array.
#### $filename: string
&nbsp;&nbsp;&nbsp;&nbsp;The filename of the ini file being parsed. If a relative path is used, it is evaluated relative to the current working directory, then the include_path.
#### $process_sections: bool
&nbsp;&nbsp;&nbsp;&nbsp;By setting the process_sections parameter to true, you get a multidimensional array, with the section names and settings included. The default for `process_sections` is false.
#### $scanner_mode: int
&nbsp;&nbsp;&nbsp;&nbsp;Can either be INI_SCANNER_NORMAL (default) or INI_SCANNER_RAW. If INI_SCANNER_RAW is supplied, then option values will not be parsed. See the [php docs](https://www.php.net/parse_ini_file) for more info.
### Return Values
The settings are returned as an associative array on success. Empty array on failure.

##
#### `from_array($arr, $process_sections)`
Recursively iterates through a given array, creating a string based on the contents.
#### $arr: array
&nbsp;&nbsp;&nbsp;&nbsp;The array to be iterated through.
#### $process_sections: bool
&nbsp;&nbsp;&nbsp;&nbsp;By setting the process_sections parameter to true, you get a multidimensional array, with the section names and settings included. The default for `process_sections` is false.
### Return Values
The settings are returned as a string on success. Empty string on failure.

##
#### `write_file($data, $path, $process_sections, $protected)`
Iterates through a given array, writing its contents to the disk.
#### $data: array
&nbsp;&nbsp;&nbsp;&nbsp;The array to be iterated through.
#### $path: string
&nbsp;&nbsp;&nbsp;&nbsp;The path of the .ini file to be saved.
#### $process_sections: bool
&nbsp;&nbsp;&nbsp;&nbsp;By setting the process_sections parameter to true, you get a multidimensional array, with the section names and settings included. The default for `process_sections` is false.
#### $protected: bool
&nbsp;&nbsp;&nbsp;&nbsp;If true, will append `.php` to the end of the file name, as well as `;<?php exit(); ?>` to the first line of the file. The default for `protected` is false.
### Return Values
The number of bytes that were written to the file, or `false` on failure.

## Usage
```php
<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/path/to/INI.php";

$arr = array(
  "foo" => array(
    "bar" => array(
      "baz" => "value"
    )
  )
);

$str = INI::from_array($arr));
var_dump($str);

// string(22) "foo.bar.baz = "value""

$parsed = INI::parse_string($str);
var_dump($parsed);

/*
array(1) {
  ["foo"]=>
  array(1) {
    ["bar"]=>
    array(1) {
      ["baz"]=>
      string(5) "value"
    }
  }
}
*/
```

### Section Processing
You can process .ini file sections by passing `true` to the second argument to any of the methods.
```php
$arr = array(
  "foo" => array(
    "bar" => array(
      "baz" => "value"
    )
  )
);

$str = INI::from_array($arr), true);
var_dump($str);

/* string(22) "
[foo]
bar.baz = "value"
"
*/
```

### Protected Files
When writing a .ini file using `write_file()`, you can pass the `protected` flag as a function parameter. This effectively protects the .ini file from being accessed externally, since it essentially becomes a php script. This does not affect the functionality of `parse_file()` or `parse_string()`.
```php
$arr = array(
  "foo" => array(
    "bar" => array(
      "baz" => "value"
    )
  )
);

INI::write_file($arr, "/path/to/file.ini", false, true);

/*
-- file.ini.php --

;<?php exit(); ?>
foo.bar.baz = "value"
*/
```
_NOTE: When passing `$protected = true`, the extension of the written file becomes .php, instead of .ini. As such, you should take care to reference it correctly when calling functions such as `parse_file()`._ 
### Escaping Keys
If any of your keys' names contain `.`, you may escape it using single-quotes (`'`).
```php
/*
-- file.ini --
'foo.bar.baz' = "value"
*/

$arr = INI::parse_file("/path/to/file.ini", false);
var_dump($arr);

/*
array(1) {
  ["foo.bar.baz"]=>
    string(5) "value"
}
*/
```
