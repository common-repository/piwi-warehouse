<?php
/**
 * @file    lib/logger/logger.php
 * @brief   Logger utility.
 *
 * @ingroup PWWH_LIB_LOGGER
 * @{
 */

/**
 * @brief     Style related definitions.
 * @details   Used to escape file during logger var dump.
 * @{
 */
define('PWWH_LIB_LOGGER_TITLE_OPENER', '/*');
define('PWWH_LIB_LOGGER_TITLE_CLOSER', '*/');
define('PWWH_LIB_LOGGER_TITLE_SEPARATOR', '=');
define('PWWH_LIB_LOGGER_TITLE_LENGHT', 80);
define('PWWH_LIB_LOGGER_NEWLINE', "\r\n");
define('PWWH_LIB_LOGGER_TYPE_ESCAPE', "(%s)");
define('PWWH_LIB_LOGGER_ARRAY_SEPARATOR', ' => ');
/** @} */

/**
 * @brief     Logger available error flags.
 * @note      Flags have been voluntarily spaced for future implementation
 *            purposes.
 */
define('PWWH_LIB_LOGGER_EFLAG_ALL',      0xFFFFFFFF);
define('PWWH_LIB_LOGGER_EFLAG_CRITICAL', 0x00000001);
define('PWWH_LIB_LOGGER_EFLAG_WARNING',  0x00000100);
define('PWWH_LIB_LOGGER_EFLAG_DEBUG',    0x00010000);
define('PWWH_LIB_LOGGER_EFLAG_GENERIC',  0x00020000);
/** @} */

/**
 * @brief     Logger error flag.
 */
define('PWWH_LIB_LOGGER_EFLAG_DEFAULT', PWWH_LIB_LOGGER_EFLAG_ALL);

/**
 * @brief     Logger errors output file.
 */
define('PWWH_LIB_LOGGER_ERROR_FILE', 'log-errors.txt');

/**
 * @brief     Logger warnings output file.
 */
define('PWWH_LIB_LOGGER_WARNING_FILE', 'log-warnings.txt');

/**
 * @brief     Logger debug messages output file.
 */
define('PWWH_LIB_LOGGER_DEBUG_FILE', 'log-debug-msg.txt');

class pwwh_lib_logger {

  private $out_dir;
  private $error_file;
  private $warning_file;
  private $debug_file;
  private $eflag;

  function __construct($out_dir, $eflag = PWWH_LIB_LOGGER_EFLAG_DEFAULT,
                       $error_file = PWWH_LIB_LOGGER_ERROR_FILE,
                       $warning_file = PWWH_LIB_LOGGER_WARNING_FILE,
                       $debug_file = PWWH_LIB_LOGGER_DEBUG_FILE) {
    $this->out_dir = $out_dir;
    $this->eflag = $eflag;
    $this->error_file = $error_file;
    $this->warning_file = $warning_file;
    $this->debug_file = $debug_file;
  }

  /**
   * @brief     Prints on predefined log files.
   * @details   According to the error flag messages are printed on different
   *            files:
   *            - PWWH_LIB_LOGGER_EFLAG_CRITICAL prints on PWWH_LIB_LOGGER_ERROR_FILE.
   *            - PWWH_LIB_LOGGER_EFLAG_WARNING prints on PWWH_LIB_LOGGER_WARNING_FILE.
   *            - PWWH_LIB_LOGGER_EFLAG_DEBUG and PWWH_LIB_LOGGER_EFLAG_GENERIC prints
   *              on PWWH_LIB_LOGGER_DEBUG_FILE.
   *
   * @param[in] mixed $msg          A string or an array of string to log.
   * @param[in] string $flag        A flag associated to the message
   *
   * @return    void
   */
  function logger_append($msg, $flag = PWWH_LIB_LOGGER_EFLAG_DEBUG) {

    /* Only activated flags are printed. */
    if($flag & $this->eflag) {
      $filepath = $this->out_dir;

      /* Computing filename. */
      if($flag & (PWWH_LIB_LOGGER_EFLAG_CRITICAL))
        $filename = PWWH_LIB_LOGGER_ERROR_FILE;
      else if($flag & (PWWH_LIB_LOGGER_EFLAG_WARNING))
        $filename = PWWH_LIB_LOGGER_WARNING_FILE;
      else if($flag & (PWWH_LIB_LOGGER_EFLAG_DEBUG | PWWH_LIB_LOGGER_EFLAG_GENERIC))
        $filename = PWWH_LIB_LOGGER_DEBUG_FILE;
      else {
        $msg = sprintf('Wrong flag 0x%x in logger_append() when msg is ' .
                       '"%s"', $flag, $msg);
        $this->logger_append($msg, PWWH_LIB_LOGGER_EFLAG_WARNING);
        return;
      }

      /* Changing date timezone and saving old one. */
      $oldtz = date_default_timezone_get();
      date_default_timezone_set('Europe/Rome');

      $output = '';
      if(is_array($msg)) {
        foreach($msg as $row) {
          if(is_string($row)) {
            $date = date('Y F d G:i:s');
            $output .= $date . ' - ' . $row . " \r\n";
          }
        }
      }
      else if(is_string($msg)) {
          $date = date('Y F d G:i:s');
          $output .= $date . ' - ' . $msg . " \r\n";
      }
      else{
        /* Restoring default timezone. */
        date_default_timezone_set($oldtz);
        return;
      }
      date_default_timezone_set($oldtz);
      $file = fopen($filepath . '/'. $filename, "a");
      if($file) {
        fwrite($file, $output);
        fclose($file);
      }
    }
  }

  /**
   * @brief     Prints a mixed on a generic log file.
   * @details   Variables are always logged on debug log.
   *
   * @param[in] mixed $var          A mixed to log.
   * @param[in] string $title       The title used in the text separator.
   * @param[in] int $indent         Used internally for recursive call.
   * @param[in] mixed $class_name   Used internally for recursive call.
   *
   * @return    void
   */
  function logger_var_dump($var, $title = '', $indent = 0, $class_name = false) {
    $filepath = $this->out_dir;
    $filename = $this->debug_file;
    $file = fopen($filepath . '/'. $filename, "a");

    /* Cannot open file. Returning. */
    if(!$file)
      return;

    /* First level: composing title separator.*/
    if($title) {
      $len = PWWH_LIB_LOGGER_TITLE_LENGHT - strlen(PWWH_LIB_LOGGER_TITLE_OPENER) -
             strlen(PWWH_LIB_LOGGER_TITLE_CLOSER);
      $sepline = PWWH_LIB_LOGGER_TITLE_OPENER .
                 str_repeat(PWWH_LIB_LOGGER_TITLE_SEPARATOR, $len) .
                 PWWH_LIB_LOGGER_TITLE_CLOSER . PWWH_LIB_LOGGER_NEWLINE;
      $title = stripcslashes($title);
      $len = PWWH_LIB_LOGGER_TITLE_LENGHT - strlen(PWWH_LIB_LOGGER_TITLE_OPENER) -
             strlen(PWWH_LIB_LOGGER_TITLE_CLOSER) - strlen($title);
      $len_before = $len/2;
      $len_after = $len/2;
      if($len % 2)
        $len_after++;
      $titleline = PWWH_LIB_LOGGER_TITLE_OPENER . str_repeat(' ', $len_before) . $title .
                   str_repeat(' ', $len_after) . PWWH_LIB_LOGGER_TITLE_CLOSER .
                   PWWH_LIB_LOGGER_NEWLINE;
      $output = $sepline . $titleline . $sepline;

      fwrite($file, $output);
    }

    if(is_bool($var)) {
      /* Printing type. */
      $output = sprintf(PWWH_LIB_LOGGER_TYPE_ESCAPE, ucfirst(gettype($var))) . ' ';

      /* Printing value. */
      if($var)
        $output .= 'TRUE' . PWWH_LIB_LOGGER_NEWLINE;
      else
        $output .= 'FALSE' . PWWH_LIB_LOGGER_NEWLINE;
      fwrite($file, $output);
    }
    else if(is_integer($var) || is_double($var) || is_string($var)) {
      /* Printing type. */
      $output = sprintf(PWWH_LIB_LOGGER_TYPE_ESCAPE, ucfirst(gettype($var))) . ' ';
      /* Printing value. */
      $output .= strval($var) . PWWH_LIB_LOGGER_NEWLINE;
      fwrite($file, $output);
    }
    else if(is_array($var)) {
      if(count($var)) {
        /* Retrieving which is the keys max lenght. */
        $keys = array_keys($var);
        $lengths = array_map('strlen', $keys);
        if(count($lengths))
          $keymaxlen = max($lengths);
        else
          $keymaxlen = 0;

        $flag = true;
        foreach($var as $key => $value) {
          $output = '';
          /* First element is different. */
          if($flag) {
            if($class_name) {
              $output .= sprintf(PWWH_LIB_LOGGER_TYPE_ESCAPE, $class_name) . ' ';
            }
            else {
              $output .= sprintf(PWWH_LIB_LOGGER_TYPE_ESCAPE,
                                 ucfirst(gettype($var))) .
                         ' ';
            }

            /* Computing indent for current element. */
            $curr_indent = $indent + strlen($output);
            $output .= strval($key) . str_repeat(' ', $keymaxlen - strlen($key)) .
                       PWWH_LIB_LOGGER_ARRAY_SEPARATOR;
            $indent += strlen($output);
            fwrite($file, $output);

            /* First element is already indented. */
            $this->logger_var_dump($value, null, $indent);
            $flag = false;

          }
          else {
            $output .= str_repeat(' ', $curr_indent);
            $output .= strval($key) . str_repeat(' ', $keymaxlen - strlen($key)) .
                       PWWH_LIB_LOGGER_ARRAY_SEPARATOR;

            fwrite($file, $output);
            $this->logger_var_dump($value, null, $indent);
          }
        }
      }
      else {
        if($class_name) {
          $output = sprintf(PWWH_LIB_LOGGER_TYPE_ESCAPE, $class_name) .
                    PWWH_LIB_LOGGER_NEWLINE;
        }
        else {
          $output = sprintf(PWWH_LIB_LOGGER_TYPE_ESCAPE, ucfirst(gettype($var))) .
                    PWWH_LIB_LOGGER_NEWLINE;
        }
        fwrite($file, $output);
      }
    }
    else if(is_object($var)) {
      /* Decoding to array. */
      $class_name = 'Object ' . get_class($var);
      $var = json_decode(json_encode($var), true);
      $this->logger_var_dump($var, null, $indent, $class_name);
    }
    else if(is_null($var)) {
      /* Printing type. */
      $output = sprintf(PWWH_LIB_LOGGER_TYPE_ESCAPE, ucfirst(gettype($var))) . ' ';
      /* Printing value. */
      $output .= 'null' . PWWH_LIB_LOGGER_NEWLINE;
      fwrite($file, $output);
    }
    else {
      /* Printing type. */
      $output = sprintf(PWWH_LIB_LOGGER_TYPE_ESCAPE, ucfirst(gettype($var))) . ' ';
      /* Printing value. */
      $output .= '???' . PWWH_LIB_LOGGER_NEWLINE;
      fwrite($file, $output);
    }
    fclose($file);
  }
}

/* Configuring logger. */
global $PWWH_LOG;
$PWWH_LOG = new pwwh_lib_logger(PWWH_MAIN_DIR);

/**
 * @brief     Prints on a log files located in the PWWH_MAIN_DIR.
 * @see       pwwh_lib_logger:logger_append
 *
 * @param[in] mixed $msg          A string or an array of string to log.
 * @param[in] string $flag        A flag associated to the message
 *
 * @return    void
 */
function pwwh_logger_append($msg, $flag = PWWH_LIB_LOGGER_EFLAG_DEBUG) {
  global $PWWH_LOG;
  $PWWH_LOG->logger_append($msg, $flag);
}

/**
 * @brief     Prints a mixed variable on a log files located in the
 *            PWWH_MAIN_DIR.
 * @see       pwwh_lib_logger:logger_var_dump
 *
 * @param[in] mixed $var          A mixed to log.
 * @param[in] string $title       The title used in the text separator.
 * @param[in] int $indent         Used internally for recursive call.
 * @param[in] mixed $class_name   Used internally for recursive call.
 *
 * @return    void
 */
function pwwh_logger_var_dump($var, $title = '') {
  global $PWWH_LOG;
  $PWWH_LOG->logger_var_dump($var, $title);
}
/** @} */