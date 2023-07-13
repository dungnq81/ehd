<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package eHD
 * @since 1.0.0
 */

use EHD_Cores\Helper;

\defined( 'ABSPATH' ) || die;

wp_redirect(Helper::home(), 302);
