<?php
/**
 * Template Name: Forgot Password
 *
 * @package YUMEHO
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

wp_redirect( yumeho_member_forgot_password_url() );
exit;
