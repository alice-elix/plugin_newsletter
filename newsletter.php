<?php

include_once plugin_dir_path( __FILE__ ).'/newsletterwidget.php';

class Gd_Newsletter
{
    ///////////////////////////// constructeur de la page newsletter
    public function __construct()
    {
    	add_action('widgets_init', function(){register_widget('Gd_Newsletter_Widget');});

        add_action('wp_loaded', array($this, 'save_email'));

        add_action('admin_menu', array($this, 'add_admin_menu'), 20);// le 20 signale que c'est un sous-menu de deuxième niveau

        add_action('admin_init', array($this, 'register_settings'));

    }

    ////////////////////// création et suppression de la table dans la bdd en cas d'installation ou suppresion de l'extension
    public static function install_newsletter()
    {
        global $wpdb;

        $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}gd_newsletter_email (id INT AUTO_INCREMENT PRIMARY KEY, email VARCHAR(255) NOT NULL);");
    }

    public static function uninstall_newsletter()
    {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}gd_newsletter_email;");
    }


    ////////////////////////////// insertion bdd de l'info récupérée par le formulaire de la newsletter
    public function save_email()
    {
        $safe= array_map('trim', array_map('strip_tags', $_POST));
        if (isset($safe['gd_newsletter_email']) && !empty($safe['gd_newsletter_email'])) {
            global $wpdb;
            $email = sanitize_email($safe['gd_newsletter_email']);

            $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}gd_newsletter_email WHERE email = %s", $email));
            if (is_null($row)) {
                $wpdb->insert("{$wpdb->prefix}gd_newsletter_email", array('email' => $email));
            }
        }
    }

    //////////////////////////////// menu latéral
    public function add_admin_menu()
    {
        $hook = add_submenu_page('info_lettre', 'InfoLettre', 'InfoLettre', 'manage_options', 'gd_newsletter', array($this, 'menu_html'));
    add_action('load-'.$hook, array($this, 'process_action'));
    }

    //////////////////////////////// ce qui s'affiche dans l'écran principal
    public function menu_html()
    {
        echo '<h1>'.get_admin_page_title().'</h1>';
         ?>
        <form method="post" action="options.php">
        <?php settings_fields('gd_newsletter_settings') ?>
        <?php do_settings_sections('gd_newsletter_settings') ?>
        <?php submit_button(); ?>
        </form>

        <form method="post" action="">
            <input type="hidden" name="send_newsletter" value="1"/>
            <?php submit_button('Envoyer l\'infoLettre') ?>
        </form>


        <?php
    }

    ////////////////////////////////  constructeur de l'interieur de la page
    public function register_settings()
    {
        register_setting('gd_newsletter_settings', 'gd_newsletter_sender');

        register_setting('gd_newsletter_settings', 'gd_newsletter_object');

        register_setting('gd_newsletter_settings', 'gd_newsletter_content');

        register_setting('gd_newsletter_settings', 'gd_newsletter_pj');

        add_settings_section('gd_newsletter_section', 'Paramètres d\'envoi', array($this, 'section_html'), 'gd_newsletter_settings');

        add_settings_field('gd_newsletter_sender', 'Expéditeur', array($this, 'sender_html'), 'gd_newsletter_settings', 'gd_newsletter_section');

        add_settings_field('gd_newsletter_object', 'Objet', array($this, 'object_html'), 'gd_newsletter_settings', 'gd_newsletter_section');

        add_settings_field('gd_newsletter_content', 'Contenu', array($this, 'content_html'), 'gd_newsletter_settings', 'gd_newsletter_section');

        add_settings_field('gd_newsletter_pj', 'PJ', array($this, 'pj_html'), 'gd_newsletter_settings', 'gd_newsletter_section');

    }

    /////construction visuelle
    //////////////////////////// en-tête
    public function section_html()
    {
        echo 'Renseignez les paramètres d\'envoi de l\'infoLettre.';
    }

    //////////////////////////// adresse mail de l'expé
    public function sender_html()
    {?>
        <input type="text" name="gd_newsletter_sender" value="<?php echo get_option('gd_newsletter_sender')?>"/>
        <?php
    }

    //////////////////////////// objet de la newletter
    public function object_html()
    {?>
        <input type="text" name="gd_newsletter_object" value="<?php echo get_option('gd_newsletter_object')?>"/>
        <?php
    }

    //////////////////////////// Contenu
    public function content_html()
    {?>
        <textarea name="gd_newsletter_content"><?php echo get_option('gd_newsletter_content')?></textarea>
        <?php
    }

    //////////////////////////// en-tête
        //// ici mettre la fonctionnalité pour mettre une pj ou afficher un pdf dans le mail
    public function pj_html()
    {?>
        <input type="file" name="gd_newsletter_pj" />
        <?php
    }


    //////////////////////////// lancement du mail
    public function process_action()
    {
        if (isset($_POST['send_newsletter'])) {
            $this->send_newsletter();
        }
    }

    //////////////////////////// Construction du mail 
    public function send_newsletter()
    {
        global $wpdb;
        $recipients = $wpdb->get_results("SELECT email FROM {$wpdb->prefix}gd_newsletter_email");
        $object = get_option('gd_newsletter_object', 'InfoLettre');
        $content = get_option('gd_newsletter_content', 'Mon contenu');
        $sender = get_option('gd_newsletter_sender', 'no-reply@example.com');
        $pj = get_option('gd_newletter_pj', 'PJ');
        $header = array('From: '.$sender);

        foreach ($recipients as $_recipient) {
            $result = wp_mail($_recipient->email, $object, $content, $header, $pj);
        }
    }

}