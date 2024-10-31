<?php

/**
 * @brief     Movement's history table name.
 */
define('PWWH_CORE_MOVEMENT_HISTORY', PWWH_CORE_MOVEMENT . '_history');

/**
 * @file      movements/class/movement-history.php
 * @brief     This class represent a database table which contains the
 *            movements' history.
 *
 * @addtogroup PWWH_CORE_MOVEMENT
 * @{
 */
class pwwh_core_movement_history {
  /**
   * @brief     True if table exists, false otherwise.
   */
  private $__exists;

  /**
   * @brief     Constructor method.
   *
   * @return    void.
   */
  public function __construct() {
    GLOBAL $wpdb;

    $query = "SHOW TABLES LIKE '" . PWWH_CORE_MOVEMENT_HISTORY . "'";
    $res = $wpdb->get_results($query);
    if($res) {
      $this->__exists = true;
    }
    else{
      $this->__exists = $this->create();
    }
  }

  /**
   * @brief     Creates the table.
   *
   * @return    boolean the operation status.
   * @retval    true
   * @retval    false
   */
  private function create() {
    GLOBAL $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $query = 'CREATE TABLE ' . PWWH_CORE_MOVEMENT_HISTORY . ' (
              ID bigint(20) NOT null AUTO_INCREMENT,
              mov_id bigint(20) NOT null,
              item_id bigint(20) NOT null,
              loc_id bigint(20) NOT null,
              hold_id bigint(20) NOT null,
              date datetime NOT null,
              moved int(20) NOT null,
              lent int(20) NOT null,
              donated int(20) NOT null,
              returned int(20) NOT null,
              lost int(20) NOT null,
              PRIMARY KEY (ID)
              )' . $charset_collate;
    $res = $wpdb->query($query);
    if($res) {
      return true;
    }
    else{
      return false;
    }
  }

  /**
   * @brief     Insert a new entry in the history table.
   * @notes     The lent parameter is internally computed to be compliant with
   *            the equation:
   *            moved = donated + returned + lost + lent
   * @notes     If mov_id, hold_id, item_id or loc_id are invalid the
   *            insertion fails.
   *
   * @param[in] array $args         An array of value to update.
   * @paramkey{mov_id}              The movement ID.
   * @paramkey{item_id}             The item ID.
   * @paramkey{loc_id}              The location ID.
   * @paramkey{hold_id}             The holder ID.
   * @paramkey{date}                The date as sting. Should be formatted as
   *                                Y-m-d H:i:s. Leave this field untouched for
   *                                auto date. @defaut{[current date]}
   * @paramkey{moved}               The total item quantity involved in this
   *                                movement. @defaut{0}
   * @paramkey{donated}             The amount of item which have been donated
   *                                to the holder. @defaut{0}
   * @paramkey{returned}            The amount of item which have been returned
   *                                from the holder. @defaut{0}
   * @paramkey{lost}                The amount of item which have been lost
   *                                by the holder. @defaut{0}
   *
   * @return    boolean the operation status.
   * @retval    true
   * @retval    false
   */
  public function insert($args) {
    GLOBAL $wpdb;
    /* Validate array keys. */
    $mov_id = pwwh_lib_utils_validate_array_field($args, 'mov_id', null);
    $item_id = pwwh_lib_utils_validate_array_field($args, 'item_id', null);
    $loc_id = pwwh_lib_utils_validate_array_field($args, 'loc_id', null);
    $hold_id = pwwh_lib_utils_validate_array_field($args, 'hold_id', null);
    $date = pwwh_lib_utils_validate_array_field($args, 'date', current_time('Y-m-d H:i:s'));
    $moved = floatval(pwwh_lib_utils_validate_array_field($args, 'moved', 0));
    $donated = floatval(pwwh_lib_utils_validate_array_field($args ,'donated', 0));
    $returned = floatval(pwwh_lib_utils_validate_array_field($args ,'returned', 0));
    $lost = floatval(pwwh_lib_utils_validate_array_field($args ,'lost', 0));

    $lent = $moved - $donated - $returned - $lost;

    if((get_post_type($mov_id) == PWWH_CORE_MOVEMENT) &&
       (get_post_type($item_id) == PWWH_CORE_ITEM) &&
       (is_a(get_term($loc_id, PWWH_CORE_ITEM_LOCATION), 'WP_Term')) &&
       (is_a(get_term($hold_id, PWWH_CORE_MOVEMENT_HOLDER), 'WP_Term'))) {

      $keys = '(mov_id, item_id, loc_id, hold_id, date, moved, lent, donated,
                returned, lost)';
      $values = "($mov_id, $item_id, $loc_id, $hold_id, '$date', $moved," .
                " $lent, $donated, $returned, $lost)";
      $query = 'INSERT INTO ' . PWWH_CORE_MOVEMENT_HISTORY . ' ' . $keys .
               ' VALUES ' . $values . ';';

      $res = $wpdb->query($query);

      return $res;
    }
    else {
      $msg = 'Invalid arguments in pwwh_core_movement_history::insert()';
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      $res = false;
    }
  }

  /**
   * @brief     Updates an entry in the history table.
   *
   * @param[in] int $history_id     The history entry ID.
   * @param[in] array $args         An array of value to update.
   * @paramkey{mov_id}              The movement ID.
   * @paramkey{item_id}             The item ID.
   * @paramkey{loc_id}              The location ID.
   * @paramkey{hold_id}             The holder ID.
   * @paramkey{date}                The date.
   * @paramkey{moved}               The total item quantity involved in this
   *                                movement.
   * @paramkey{lent}                The amount of item which have been lent
   *                                to the holder.
   * @paramkey{donated}             The amount of item which have been donated
   *                                to the holder.
   * @paramkey{returned}            The amount of item which have been returned
   *                                from the holder.
   * @paramkey{lost}                The amount of item which have been lost
   *                                by the holder.
   *
   * @return    boolean the operation status.
   * @retval    true
   * @retval    false
   */
  public function update($history_id, $args = array()) {
    if(is_array($args) && count($args)) {
      GLOBAL $wpdb;

      /* Allowed columns for this database table. */
      $allowed = array('mov_id', 'item_id', 'loc_id', 'hold_id', 'date',
                       'moved', 'lent', 'donated', 'returned', 'lost');

      /* Composing SET argument. */
      $flag = false;
      $res = true;
      $set = '';
      foreach($args as $key => $value) {
        if(in_array($key, $allowed)) {
          if($flag)
            $set .= ',';
          $set .= sprintf("%s = '%s'", $key, $value);
          $flag = true;
        }
        else {
          /* Unallowed key: printing error and terminating this function. */
          $msg = 'Unallowed $args key in pwwh_core_movement_history::update()';
          pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
          $msg = sprintf('Key is %s', $key);
          pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
          $res = false;
          break;
        }
      }
      if($res) {
        /* So far so good. Updating database table. */
        $query = 'UPDATE ' . PWWH_CORE_MOVEMENT_HISTORY . ' SET ' . $set .
                 ' WHERE ID = ' . $history_id;

        $res = $wpdb->query($query);

        return $res;
      }
    }
    else {
      /* Unallowed args: printing error and terminating this function. */
      $msg = 'Unexpected $args in pwwh_core_movement_history::update()';
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      $msg = sprintf('$args type is %s', gettype($args));
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      $res = false;
    }

    return $res;
  }

  /**
   * @brief     Erases all the entries related to a specific.
   *
   * @brief     Updates an entry in the history table.
   *
   * @param[in] array $args         An array of value to update.
   * @paramkey{logic}               The logic of condition. Allowed 'AND' and
   *                                'OR'. @default{'AND'}
   * @paramkey{history_id}          The history ID.
   * @paramkey{mov_id}              The movement ID.
   * @paramkey{item_id}             The item ID.
   * @paramkey{loc_id}              The location ID.
   * @paramkey{hold_id}             The holder ID.
   * @paramkey{date}                The date.
   * @paramkey{moved}               The total item quantity involved in this
   *                                movement.
   * @paramkey{lent}                The amount of item which have been lent
   *                                to the holder.
   * @paramkey{donated}             The amount of item which have been donated
   *                                to the holder.
   * @paramkey{returned}            The amount of item which have been returned
   *                                from the holder.
   * @paramkey{lost}                The amount of item which have been lost
   *                                by the holder.
   *
   * @return    mixed an integer value indicating the number of rows erased or
   *            FALSE in case of error.
   */
  public function erase($args) {
    GLOBAL $wpdb;

    $logic = pwwh_lib_utils_validate_array_field($args, 'logic', 'AND',
                                             array('AND', 'OR'));

    if(($logic == 'AND') || ($logic == 'OR')) {
      if(isset($args['logic']))
        unset($args['logic']);

      /* Allowed columns for this database table. */
      $allowed = array('history_id', 'mov_id', 'item_id', 'loc_id', 'hold_id',
                       'date', 'moved', 'lent', 'donated', 'returned', 'lost');
      $res = true;
      $where = array();
      foreach($args as $key => $value) {
        if(in_array($key, $allowed)) {
          array_push($where, sprintf('%s=%s', $key, $value));
        }
        else {
          $wrong_key = $key;
          $res = false;
          break;
        }
      }
      if($res) {
        $where_clause = 'WHERE ' . implode(" $logic ", $where);
        $query = "DELETE FROM " . PWWH_CORE_MOVEMENT_HISTORY . " $where_clause;";
        $res = $wpdb->query($query);

        return $res;
      }
      else {
        $msg = 'Unexpected argument in pwwh_core_movement_history::erase()';
        pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
        $msg = sprintf('Wrong argument is %s', $wrong_key);
        pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
        return false;
      }
    }
    else {
      $msg = 'Unexpected logic in pwwh_core_movement_history::erase()';
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      $msg = sprintf('Logic is %s', $logic);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      return false;
    }
  }

  /**
   * @brief     Returns the movement history according to arguments.
   *
   * @param[in] array $args         An array of arguments to compose the query.
   * @paramkey{mov_id}              The movement ID or an array of movement IDs.
   * @paramval{item_id}             The item ID column.
   * @paramkey{loc_id}              The location ID.
   * @paramkey{orderby}             The name of the column on which orderby.
   *                                @default{'date'}.
   * @paramkey{order}               The order @default{'ASC'}.
   * @paramkey{limit}               The number of rows to return @optional.
   *
   * @return    mixed an array of results or false.
   * @retval    true
   * @retval    false
   */
  public function get($args) {
    if(is_array($args) && count($args)) {
      $where = array();
      /* Managing movement ID or IDs. */
      if(array_key_exists('mov_id', $args) && !is_array($args['mov_id'])) {
        $mov_id = $args['mov_id'];
        array_push($where, "mov_id='$mov_id'");
      }
      else if(array_key_exists('mov_id',$args) && is_array($args['mov_id'])) {
        $where_mov = array();
        foreach($args['mov_id'] as $mov_id) {
          array_push($where_mov, sprintf('"(mov_id=\'%d\')"', $mov_id));
        }
        $array_push($where, implode(' OR ', $where_mov));
      }
      else {
        /* Nothing to do here. */
      }

      /* Managing item ID or IDs. */
      if(array_key_exists('item_id', $args) && !is_array($args['item_id'])) {
        $item_id = $args['item_id'];
        array_push($where, "item_id='$item_id'");
      }
      else if(array_key_exists('item_id',$args) && is_array($args['item_id'])) {
        $where_item = array();
        foreach($args['item_id'] as $item_id) {
          array_push($where_item, sprintf('"(item_id=\'%d\')"', $item_id));
        }
        array_push($where, implode(' OR ', $where_item));
      }
      else {
        /* Nothing to do here. */
      }

      /* Managing location ID or IDs. */
      if(array_key_exists('loc_id', $args) && !is_array($args['loc_id'])) {
        $loc_id = $args['loc_id'];
        array_push($where, "loc_id='$loc_id'");
      }
      else if(array_key_exists('loc_id',$args) && is_array($args['loc_id'])) {
        $where_item = array();
        foreach($args['loc_id'] as $loc_id) {
          array_push($where_item, sprintf('"(loc_id=\'%d\')"', $loc_id));
        }
        array_push($where, implode(' OR ', $where_item));
      }
      else {
        /* Nothing to do here. */
      }

      if(count($where))
        $where = implode(' AND ', $where);
      else {
        $msg = 'Unexpected where condition in pwwh_core_movement_history::get()';
        pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
        return false;
      }

      /* Managing orderby. */
      /* Allowed columns for this database table. */
      $allowed = array('mov_id', 'item_id', 'loc_id', 'hold_id', 'date',
                       'moved', 'lent', 'donated', 'returned', 'lost');

      if(array_key_exists('orderby',$args) &&
         in_array($args['orderby'], $allowed)) {
        $orderby = $args['orderby'];
      }
      else {
        $orderby = 'date';
      }

      /* Managing order. */
      $order = pwwh_lib_utils_validate_array_field($args, 'order', 'ASC', array('ASC', 'DESC'));

      /* Managing limit. */
      if(array_key_exists('limit',$args)) {
        $limit = 'LIMIT ' . $args['limit'];
      }
      else {
        $limit = '';
      }

      /* Composing query and managing results. */
      $query = "SELECT * FROM " . PWWH_CORE_MOVEMENT_HISTORY .
               " WHERE $where ORDER BY $orderby $order $limit;";

      GLOBAL $wpdb;
      return $wpdb->get_results($query, ARRAY_A);

    }
    else {
      /* Unallowed args: printing error and terminating this function. */
      $msg = 'Unexpected $args in pwwh_core_movement_history::get()';
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      $msg = sprintf('$args type is %s', gettype($args));
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      return false;
    }
  }
}

/** @} */