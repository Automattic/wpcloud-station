<?php
/**
 * Header file for the WP Cloud Dashboard theme.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WP Cloud
 * @subpackage WP_Cloud_Dashboard
 */

?><!DOCTYPE html>

<html class="no-js" <?php language_attributes(); ?>>

	<head>

		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" >

		<link rel="profile" href="https://gmpg.org/xfn/11">
		<link rel="stylesheet" href="<?php echo esc_url( get_stylesheet_uri() ); ?>" type="text/css" />

		<?php wp_head(); ?>

	</head>

	<body <?php body_class(); ?> >
		<?php wp_body_open(); ?>
		<div class="layout">
		 <?php get_sidebar(); ?>
		 <div class="main-wrapper">
