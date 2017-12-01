<?php
namespace HdCustomMeta;

class InteagerField extends Field
{
    protected $_content = '';

    public function __construct( $field )
    {
        global $post;

        if ( !isset( $field['id'] ) ) {
            return;
        }

        $value = get_post_meta( $post->ID, $field['id'], true );
        ob_start();
        ?>
        <input type="number"
        <?php
            if ( isset( $field['min'] ) && is_int( $field['min'] ) ) {
                echo 'min="'.$field['min'].'" ';
            }
            if ( isset( $field['max'] ) && is_int( $field['max'] ) ) {
                echo 'max="'.$field['max'].'" ';
            }
        ?>
       value="<?php echo $value; ?>" id="<?php echo $field['id']; ?>" name="<?php echo $field['id']; ?>" class="text_field">
        <?php
        $this->_content = ob_get_clean();
    }

    public function output()
    {
        return $this->_content;
    }

    public function fieldProtection()
    {
        return [];
    }
}