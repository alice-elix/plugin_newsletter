<?php
/*
Plugin Name: info_lettre
Description: Un plugin d'envoi de lettres d'information
Version: 1
Author: Gaëlle David
Author URI: https://gaelle-david.cmoi.cc
*/

class Gd_Plugin
{
    ////////////// construction du plugin : appel des pages avec instanciation 
    public function __construct()
    {
        include_once plugin_dir_path( __FILE__ ).'/newsletter.php';
            new Gd_Newsletter();

        register_activation_hook(__FILE__, array('Gd_Newsletter', 'install_newsletter'));

        register_uninstall_hook(__FILE__, array('Gd_Newsletter', 'uninstall_newsletter'));

        add_action('admin_menu', array($this, 'add_admin_menu'));

        if(!is_admin()){
            add_action('wp_enqueue_scripts', array($this, 'info_lettreCSS'),15);
            add_action('wp_enqueue_scripts', array($this, 'info_lettreJS'),15);
        }
    }

    /////////////////// chargement des css et js 

    public function info_lettreCSS(){
        wp_enqueue_style('info_lettre', plugins_url('info_lettre/css/info_lettre.css'));
    }
    public function info_lettreJS(){
        wp_enqueue_script('info_lettre', plugins_url('info_lettre/js/info_lettre.js'));
    }

    ////////////////// création du menu dans la barre lat
    public function add_admin_menu()
    {
        ///// nom qui apparait dans la barre lat
        add_menu_page('Lettre d\'information', 'info lettre ', 'manage_options', 'info_lettre', array($this, 'menu_html'));
        //// nom qui apparait en "sous-menu"
        add_submenu_page('info_lettre', 'Accueil', 'Accueil', 'manage_options', 'info_lettre', array($this, 'menu_html'));
    }

    /////////////// contenu de la page d'accueil
    public function menu_html()
    {
        echo '<h1>'.get_admin_page_title().'</h1>';
        echo '<p>Bienvenue sur la page d\'accueil de la lettre d\'informations.</p>';
    }
}

new Gd_Plugin();