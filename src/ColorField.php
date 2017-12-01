<?php
namespace HdCustomMeta;

class ColorField extends Field
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
        <input type="text" value="<?php echo $value; ?>" id="<?php echo $field['id']; ?>" name="<?php echo $field['id']; ?>" class="text_field color_picker_input">
        <script>
            $(function() {
                $('.color_picker_input').wpColorPicker();
            });
        </script>
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