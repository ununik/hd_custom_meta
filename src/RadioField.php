<?php
namespace HdCustomMeta;

class RadioField extends Field
{
    protected $_content = '';

    public function __construct( $field )
    {
        global $post;

        if ( !isset( $field['id'] ) ) {
            return;
        }

        $value_data = get_post_meta( $post->ID, $field['id'], true );

        $data = [];
        if ( isset( $field['options'] ) ) {
            $data = $field['options'];
        }
        ob_start();
        ?>
        <?php
        foreach( $data as  $id => $value ) {
            $checked = '';
            if ($id == $value_data){
                $checked = 'checked';
            }
            ?>
            <input type="radio" <?php echo $checked; ?> id="<?php echo $field['id']; ?>_<?php echo $id; ?>" value="<?php echo $id;?>" name="<?php echo $field['id']; ?>">
            <lablel for="<?php echo $field['id']; ?>_<?php echo $id; ?>"><?php echo $value; ?></lablel><br>
            <?php
        }
        ?>
        </select>
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