<?php
/*
 * Template Name: Ajax
 * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
 */
@header('Content-Type: application/json; charset=' . get_option('blog_charset'));
status_header(200);
echo apply_filters('the_content', '');
die;