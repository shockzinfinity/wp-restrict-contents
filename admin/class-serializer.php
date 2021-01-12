<?php
class Serializer
{
  private $notices;

  public function init()
  {
    add_action('admin_post', array($this, 'save'));

    $this->notices = new Notices();
  }

  public function save()
  {
    // 1. validate nonce
    // 2. verify user permission
    // 3. save if above are valid

    if (!($this->has_valid_nonce() && current_user_can('manage_options'))) {
      // TODO: display errors
      $this->notices->add_notice('error', 'Error Message');
    }

    // Checks for invalid UTF-8
    // Converts single `<` characters to entities
    // Strips all tags
    // Removes line breaks, tabs, and extra whitespace
    // Strips octets
    if (null !== wp_unslash($_POST['acme-message'])) {
      // save
      $value = sanitize_text_field($_POST['acme-message']);
      update_option('restrict-custom-data', $value);
      $this->notices->add_notice('success', 'Success message');
    }

    $this->notices->set_notice_transient();
    $this->redirect();
  }

  private function has_valid_nonce()
  {
    if (!isset($_POST['acme-custom-message'])) {
      return false;
    }

    $field = wp_unslash($_POST['acme-custom-message']);
    $action = 'acme-settings-save';

    return wp_verify_nonce($field, $action);
  }

  private function redirect()
  {
    // init
    if (!isset($_POST['_wp_http_referer'])) {
      $_POST['_wp_http_referer'] = wp_login_url();
    }

    $url = sanitize_text_field(wp_unslash($_POST['_wp_http_referer']));

    wp_safe_redirect(urlencode($url));
    exit;
  }
}
