<div class="wrap">
  <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
  <form method="POST" action="<?php echo esc_html(admin_url('admin-post.php')) ?>">
    <div id="universal-message-container">
      <h2>Universal Message</h2>
      <div class="options">
        <p>
          <lable>What message would you like to display above each post?</lable>
          <br />
          <input type="text" name="acme-message" value="<?php echo esc_attr($this->deserializer->get_value('restrict-custom-data')); ?>" />
        </p>
      </div>
    </div>
    <?php
    wp_nonce_field('acme-settings-save', 'acme-custom-message');
    submit_button();
    ?>
  </form>
</div>