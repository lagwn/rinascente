<?php
/**
 * Template Name: Member Login
 *
 * @package YUMEHO
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$shared_member_url = yumeho_member_login_url( yumeho_member_page_url() );
wp_redirect( $shared_member_url );
exit;
