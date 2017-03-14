<?php
/**
 * Form template
 *
 * @package Setting\Templates
 */

?>
<h1>Settings Page</h1>
<form method="POST" action="options.php">
	<?php
		// Pass slug name of page, also referred to in Settings API as option group name.
		settings_fields( $this->page_slug );
		// Pass slug name of page.
		do_settings_sections( $this->page_slug );
		submit_button();
	?>
</form>
