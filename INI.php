<?php

class INI
{
  public static function parse_string(string $str, bool $process_sections = false, $scanner_mode = INI_SCANNER_NORMAL)
  {
    $explode_str = '.';
    $escape_char = "'";
    $data = parse_ini_string($str, $process_sections, $scanner_mode);
    if (!$process_sections) {
      $data = array($data);
    }
    foreach ($data as $section_key => $section) {
      foreach ($section as $key => $value) {
        if (strpos($key, $explode_str)) {
          if (substr($key, 0, 1) !== $escape_char) {
            $sub_keys = explode($explode_str, $key);
            $subs = &$data[$section_key];
            foreach ($sub_keys as $sub_key) {
              if (!isset($subs[$sub_key])) {
                $subs[$sub_key] = [];
              }
              $subs = &$subs[$sub_key];
            }
            $subs = $value;
            unset($data[$section_key][$key]);
          }
          else {
            $new_key = trim($key, $escape_char);
            $data[$section_key][$new_key] = $value;
            unset($data[$section_key][$key]);
          }
        }
      }
    }
    if (!$process_sections) {
      $data = $data[0];
    }
    return $data;
  }
  public static function parse_file($file, $process_sections = false, $scanner_mode = INI_SCANNER_NORMAL)
  {
    $explode_str = '.';
    $escape_char = "'";
    $data = parse_ini_file($file, $process_sections, $scanner_mode);
    if (!$process_sections) {
      $data = array($data);
    }
    foreach ($data as $section_key => $section) {
      foreach ($section as $key => $value) {
        if (strpos($key, $explode_str)) {
          if (substr($key, 0, 1) !== $escape_char) {
            $sub_keys = explode($explode_str, $key);
            $subs = &$data[$section_key];
            foreach ($sub_keys as $sub_key) {
              if (!isset($subs[$sub_key])) {
                $subs[$sub_key] = [];
              }
              $subs = &$subs[$sub_key];
            }
            $subs = $value;
            unset($data[$section_key][$key]);
          }
          else {
            $new_key = trim($key, $escape_char);
            $data[$section_key][$new_key] = $value;
            unset($data[$section_key][$key]);
          }
        }
      }
    }
    if (!$process_sections) {
      $data = $data[0];
    }
    return $data;
  }

  public static function from_array(array $arr, bool $process_sections = false)
  {
    $ritit = new RecursiveIteratorIterator(new RecursiveArrayIterator($arr));
    $result = array();
    foreach ($ritit as $leaf_value) {
      $keys = array();
      foreach (range(0, $ritit->getDepth()) as $depth) {
        $keys[] = $ritit->getSubIterator($depth)->key();
      }
      $result[join('.', $keys)] = $leaf_value;
    }
    $out = '';
    $section_key = '';
    foreach ($result as $k => $v) {
      if ($process_sections) {
        $keys = explode(".", $k);
        if ($section_key !== $keys[0]) {
          $out .= "[$keys[0]]" . PHP_EOL;
          $section_key = $keys[0];
        }
        unset($keys[0]);
        $key_str = join(".", $keys);
        $out .= "$key_str = \"$v\"" . PHP_EOL;
      } else {
        $key_str = join(".", $keys);
        $out .= "$key_str = \"$v\"" . PHP_EOL;
      }
    }
    return $out;
  }

  public static function write_file(array $data, string $path, bool $process_sections, bool $protected = false)
  {
    $str = $protected ? ";<?php exit(); ?>" . PHP_EOL : "";
    $str .= self::from_array($data, $process_sections);
    return file_put_contents($path . ($protected ? ".php" : ""), $str);
  }
}
