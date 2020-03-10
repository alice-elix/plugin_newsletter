<?php
class Gd_Newsletter_Widget extends WP_Widget
{
    ////////////// construction du widget
    public function __construct()
    {
        parent::__construct('gd_newsletter', 'InfoLettre', array('description' => 'Un formulaire d\'inscription à l\'infoLettre.'));
    }

    /////////////// ancrage et champs du widget
    public function widget($args, $instance)
    {
        echo $args['before_widget'];?>
            <div id="newsletterPerso">
                <h2 id="footer-perso-left">
                    <span id="titleNewsLetter">
                        <?php echo apply_filters('widget_title', $instance['title']);
                        echo $args['after_title'];?>
                    </span>
                </h2>
                <form action="" method="post">
                    <p id="textNewsletter">
                        <input id="gd_newsletter_email" name="gd_newsletter_email" type="email" placeholder="Votre courriel" />
                    </p>
                    <div class="buttonsNewsletter" >
                        <input type="submit" class="buttonNewsletter" value="Je m'abonne" id="submitNewsLetter"/>
                        <a href="http://localhost/municipales2020/wordpress/la-ressourcerie/newsletter/"><input type="button" value="InfoLettres passées" class="buttonNewsletter" /></a>
                    </div>
                </form>
            </div>
                <?php
                echo $args['after_widget'];
            
    }

    /////////// visuel lors de l'insertion du widget (définition du titre du widget ect)
    public function form($instance)
    {
        $title = isset($instance['title']) ? $instance['title'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo  $title; ?>" />
        </p>
        <?php
    }

}