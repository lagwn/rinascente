<?php
/**
 * Template Name: Reset Password
 *
 * @package YUMEHO
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

wp_redirect( yumeho_member_reset_password_url() );
exit;
