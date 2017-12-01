<?php
namespace HdCustomMeta;

class DateField extends Field
{
    protected $_content = '';

    public function __construct( $field )
    {
        global $post;

        if ( !isset( $field['id'] ) ) {
            return;
        }

        $value = get_post_meta( $post->ID, $field['id'], true );
        if ($value && is_numeric($value)) {
            $value = date('d.m.Y', $value);
        }
        ob_start();
        ?>
        <input type="text" value="<?php echo $value; ?>" id="<?php echo $field['id']; ?>" name="<?php echo $field['id']; ?>" class="text_field custom_date">
        <script type="text/javascript">
            $(document).ready(function($) {
                $('.custom_date').datepicker({
                    dateFormat : 'dd.mm.yy'
                });
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
        return ['date'];
    }
}