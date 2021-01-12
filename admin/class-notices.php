<?php
class Notices
{
  private $notices;
  private $notice_name;

  public function __construct($notice_name = '')
  {
    $this->notice_name = $notice_name ? $notice_name : time();
    add_action('admin_notices', array($this, 'display_notices'));
  }

  public function display_notices()
  {
    if (!$my_notices = get_transient($this->notice_name)) {
      return;
    }

    foreach ($my_notices as $notice_type => $notice_message) {
      foreach ($notice_message as $message) {
        echo '<div class="notice notice-' . $notice_type . '">
        ' . $message . '
        </div>';
      }
    }

    delete_transient($this->notice_name);
  }

  public function add_notice($type, $message)
  {
    $this->notices[$type][] = $message;
  }

  public function set_notice_transient()
  {
    set_transient($this->notice_name, $this->notices, 30);
  }
}
