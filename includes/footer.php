<?php
/**
 * footer.php
 *
 * Shared layout file that renders the bottom HTML structure of every page.
 *
 * Responsibilities:
 * - Closes main content containers and outputs the site footer.
 * - Optionally renders flash messages at the bottom of the page, if configured.
 *
 * Used by: all user-facing and admin pages.
 */


$year = date('Y');
?>
<footer>
  <small>© <?php echo $year; ?> Big Premiere Point — Student Cinema Project</small>
  <small>Built with plain HTML, CSS &amp; JS</small>
</footer>
