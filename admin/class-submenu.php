<?php
class Submenu
{
  private $submenu_page;

  public function __construct($submenu_page)
  {
    $this->submenu_page = $submenu_page;
  }
  public function init()
  {
    add_action('admin_menu', array($this, 'add_options_page'));
  }
  public function add_options_page()
  {
    add_options_page(
      'Restrict Admin Page', // the text to display as the title of the corresponding options page
      'Restrict Post', // the text to display as the submenu text for the menu item
      'manage_options', // the capabilities needed to access this menu item
      'custom-admin-page', // the menu slug that's used to identify this submenu item
      array($this->submenu_page, 'render'), // a callback to a function that's responsible for rendering the admin page
      'dashicons-schedule', // icon url
      66 // position
    );
  }
}
