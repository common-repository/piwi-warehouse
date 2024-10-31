<?php

/**
 * @brief     Make sure that the necessary base class is available.
 */
if(!class_exists('WP_List_Table')) {
  require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * @file      movements/class/movement-history-list.php
 * @brief     This class displaying the list of history table entries related to 
 *            one specific movement.
 * @note      This class is based on Wordpress class WP_List_Table.
 *
 * @addtogroup PWWH_CORE_MOVEMENT
 * @{
 */
class pwwh_core_movement_history_list extends WP_List_Table {
  /**
   * @brief     The movement ID.
   */
  private $__mov_id;

  /**
   * @brief     The item ID.
   */
  private $__item_id;

  /**
   * @brief     The location ID.
   */
  private $__loc_id;

  /**
   * @brief     Constructor method. Overrides parent constructor.
   *
   * @param[in] int $mov_id         The movement ID.
   * @param[in] int $item_id        The item ID.
   * @param[in] int $loc_id         The location ID.
   *
   * @return    void.
   */
  public function __construct($mov_id, $item_id, $loc_id) {
    $this->__mov_id = $mov_id;
    $this->__item_id = $item_id;
    $this->__loc_id = $loc_id;

    $args = array('singular' => __('Movement History', 'piwi-warehouse'),
                  'ajax' => false,
                  'screen' => null);
    parent::__construct($args);
  }
  
  /**
   * @brief     Gets a list of columns as 'internal-name' => 'Title'.
   *
   * @return    array the columns list.
   * @retkey    date
   * @retkey    moved
   * @retkey    lent
   * @retkey    returned
   * @retkey    donated
   * @retkey    lost
   */
  public function get_columns() {
    $columns = array('date' => __('Operation date', 'piwi-warehouse'),
                     'moved' => __('Moved', 'piwi-warehouse'),
                     'returned' => __('Returned', 'piwi-warehouse'),
                     'donated' => __('Donated', 'piwi-warehouse'),
                     'lost' => __('Lost', 'piwi-warehouse'),
                     'lent' => __('Lent', 'piwi-warehouse'));
    return $columns;
  }
  
  /**
   * @brief     Renders a column when no column specific method exists.
   * @note      Before displaying columns WordPress looks for methods called 
   *            column_{key_name}. Each column must have its own method. The 
   *            column_default method is used to process any column for which no 
   *            special method is defined.
   *
   * @param[in] array $item         The item.
   * @param[in] string $column_name The columnn name.
   *
   * @return mixed The column value or whole array.
   */
  public function column_default($item, $column_name) {
    switch ($column_name) {
      case 'date':
        $timeformat = get_option('time_format');
        $dateformat = get_option('date_format');
        $format = $dateformat . ' - ' . $timeformat;
        return date($format, strtotime($item[$column_name]));
        break;
      case 'moved':
      case 'returned':
      case 'donated':
      case 'lost':
      case 'lent':
        return $item[$column_name];
        break;
      default:
        /*Show the whole array for troubleshooting purposes. */
        return print_r($item, true);
        break;
    }
  }
  
  /**
   * @brief     Prepares the list for displaying.
   *
   * @return    void.
   */
  public function prepare_items() {
    $columns = $this->get_columns();
    $hidden = array();
    $sortable = array();
    $this->_column_headers = array($columns, $hidden, $sortable);
    $history = new pwwh_core_movement_history();
    $args = array('mov_id' => $this->__mov_id, 
                  'item_id' => $this->__item_id,
                  'loc_id' => $this->__loc_id);
    $this->items = $history->get($args);
  }

  /**
   * @brief     Displays the list.
   *
   * @return    The list as ui table.
   */
  public function get_history() {
    $this->prepare_items();
    $output = '<div class="pwwh-history">';
    ob_start();
    $this->display();
    $output .= ob_get_clean();
    $output .= '</div>';
    return $output;
  }
  
  /**
   * @brief     Displays the list.
   *
   * @return    The list as ui table.
   */
  public function display_history() {
    echo $this->get_history();
  }
  
  /**
   * @brief     Displays the table navigation.
   * @note      Currently is empty to avoid nonce repetition.
   */
  protected function display_tablenav($which) {
    
  }
}
/** @} */